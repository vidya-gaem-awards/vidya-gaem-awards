<?php
namespace App\Controller;

use App\Entity\Action;
use App\Entity\Advertisement;
use App\Entity\ResultCache;
use App\Service\AuditService;
use App\Service\ConfigService;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Award;
use App\Entity\TableHistory;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ResultController extends AbstractController
{
    public function simpleAction(EntityManagerInterface $em)
    {
        /** @var Award[] $awards */
        $awards = $em->createQueryBuilder()
            ->select('a')
            ->from(Award::class, 'a')
            ->where('a.enabled = true')
            ->orderBy('a.order', 'ASC')
            ->getQuery()
            ->getResult();

        $results = $winners = [];

        $ranks = [
            '1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th',
            '11th', '12th', '13th', '14th', '15th', '16th', '17th', '18th', '19th', '20th'
        ];

        foreach ($awards as $award) {
            $rankings = array_values($award->getOfficialResults() ? $award->getOfficialResults()->getResults() : []);

            $winners[$award->getId()] = $award->getNominee($rankings[0]);

            foreach ($rankings as $key => &$value) {
                $nominee = $award->getNominee($value);
                $output = '<strong>' . $ranks[$key] . '</strong> ';

                if ($nominee) {
                    $output .= str_replace(' ', '&nbsp;', $nominee->getName());
                } else {
                    $output .= '<span style="color: white; background: red;">' . $value . '</span>';
                }
                $value = $output;
            }

//            $theOthers = implode(' ', array_slice($rankings, 1));
//            $rankings = array_slice($rankings, 0, 1);
//            $rankings[] = $theOthers;

            $results[$award->getId()] = $rankings;
        }


        // Fake ads
        $adverts = $em->getRepository(Advertisement::class)->findBy(['special' => 0]);

        if (empty($adverts)) {
            $ad1 = $ad2 = false;
        } else {
            $ad1 = $adverts[array_rand($adverts)];
            $ad2 = $adverts[array_rand($adverts)];
        }

        return $this->render('winners.html.twig', [
            'ad1' => $ad1,
            'ad2' => $ad2,
            'awards' => $awards,
            'results' => $results,
            'winners' => $winners,
        ]);
    }

    public function detailedAction(?string $all, EntityManagerInterface $em, Request $request, AuthorizationCheckerInterface $authChecker)
    {
        /** @var Award[] $awards */
        $awards = $em->createQueryBuilder()
            ->select('a')
            ->from(Award::class, 'a')
            ->where('a.enabled = true')
            ->orderBy('a.order', 'ASC')
            ->getQuery()
            ->getResult();

        $results = [];

        $filters = [
            '01-all' => 'No filtering',
//                '03-null' => 'No referrer',
            '08-4chan-or-null-with-voting-code' => '4chan',
            '19-google' => 'Google',
            '16-8chan' => '8chan',
//            '02-voting-code' => 'Voting code',
//            '04-4chan' => '4chan',
//            '05-4chan-and-voting-code' => '4chan with code',
//            '06-4chan-without-voting-code' => '4chan without code',
//            '07-4chan-or-null' => '4chan + NULL',
//            '09-null-and-voting-code' => 'NULL with code',
//            '10-null-without-voting-code' => 'NULL without code',
            '15-knockout' => 'Knockout',
            '18-facebook' => 'Facebook',
            '12-twitter' => 'Twitter',
            '17-twitch' => 'Twitch',
            '11-reddit' => 'Reddit',
            '21-kiwifarms' => 'Kiwifarms',
            '14-neogaf' => 'NeoGAF',
            '13-something-awful' => 'Something Awful',
            '20-yandex' => 'Yandex',
        ];

        if ($authChecker->isGranted('ROLE_VOTING_CODE')) {
            $filters['22-4chan-ads'] = '4chan Ads';
        }

        $nominees = [];

        foreach ($awards as $award) {
            foreach ($award->getNominees() as $nominee) {
                $nominees[$award->getId()][$nominee->getShortName()] = $nominee;
            }
            foreach ($award->getResultCache() as $result) {
                if (isset($filters[$result->getFilter()]) && $result->getVotes() >= 5) {
                    $results[$award->getId()][$result->getFilter()] = $result;
                }
            }

            if (isset($results[$award->getId()])) {
                // Display the filter with the most votes first (this will invariably put No Filtering and 4chan on top)
                uasort($results[$award->getId()], function (ResultCache $a, ResultCache $b) {
                    return $b->getVotes() <=> $a->getVotes();
                });
            }

            // If the result cache is empty, results haven't been generated yet.
            if ($award->getResultCache()->isEmpty() || !isset($results[$award->getId()])) {
                $results[$award->getId()] = null;
            }
        }

        return $this->render('results.html.twig', [
            'title' => 'Results',
            'awards' => $awards,
            'nominees' => $nominees,
            'all' => (bool)$all,
            'results' => $results,
            'filters' => $filters,
            'sweepPoints' => $request->query->getBoolean('sweepPoints')
        ]);
    }

    public function pairwiseAction(EntityManagerInterface $em)
    {
        /** @var Award[] $awards */
        $awards = $em->createQueryBuilder()
            ->select('a')
            ->from(Award::class, 'a')
            ->where('a.enabled = true')
            ->orderBy('a.order', 'ASC')
            ->getQuery()
            ->getResult();

        $pairwise = [];

        foreach ($awards as $award) {
            $pairwise[$award->getId()] = $award->getOfficialResults() ? $award->getOfficialResults()->getSteps()['pairwise'] : null;
        }

        return $this->render('resultsPairwise.html.twig', [
            'awards' => $awards,
            'pairwise' => $pairwise
        ]);
    }

    public function winnerImageUploadAction(EntityManagerInterface $em, Request $request, AuditService $auditService, ConfigService $configService, FileService $fileService)
    {
        if ($configService->isReadOnly()) {
            return $this->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        $id = $request->request->get('id') ?? false;

        /** @var Award $award */
        $award = $em->getRepository(Award::class)->find($id);

        if (!$award) {
            return $this->json(['error' => 'Invalid award specified.']);
        }

        try {
            $file = $fileService->handleUploadedFile(
                $request->files->get('file'),
                'Award.winnerImage',
                'winners',
                $award->getId()
            );
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()]);
        }

        if ($award->getWinnerImage()) {
            $fileService->deleteFile($award->getWinnerImage());
        }

        $award->setWinnerImage($file);
        $em->persist($award);

        $auditService->add(
            new Action('winner-image-updated', $award->getId()),
            new TableHistory(Award::class, $award->getId(), ['image' => $file->getId()])
        );
        $em->flush();

        return $this->json(['success' => true, 'filePath' => $file->getURL()]);
    }
}
