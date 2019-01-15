<?php
namespace App\Controller;

use App\Entity\Action;
use App\Entity\ResultCache;
use App\Service\AuditService;
use App\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use App\VGA\FileSystem;
use App\Entity\Award;
use App\Entity\TableHistory;

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

        $results = [];

        $ranks = [
            '1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th',
            '11th', '12th', '13th', '14th', '15th', '16th', '17th', '18th', '19th', '20th'
        ];

        foreach ($awards as $award) {
            $rankings = array_values($award->getOfficialResults() ? $award->getOfficialResults()->getResults() : []);

            foreach ($rankings as $key => &$value) {
                $nominee = $award->getNominee($value);
                $value = '<span class="rank">' . $ranks[$key] . '.</span> ';
                if ($nominee) {
                    $value .= $nominee->getName();
                } else {
                    $value .= '<span style="color: white; background: red;">' . $value . '</span>';
                }
            }
            $theOthers = implode(', ', array_slice($rankings, 5));
            $rankings = array_slice($rankings, 0, 5);
            $rankings[] = $theOthers;

            $results[$award->getId()] = $rankings;
        }

        return $this->render('winners.html.twig', [
            'awards' => $awards,
            'results' => $results
        ]);
    }

    public function detailedAction(?string $all, EntityManagerInterface $em, Request $request)
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
            '15-facepunch' => 'Facepunch',
            '18-facebook' => 'Facebook',
            '12-twitter' => 'Twitter',
            '17-twitch' => 'Twitch',
            '11-reddit' => 'Reddit',
            '21-kiwifarms' => 'Kiwifarms',
            '14-neogaf' => 'NeoGAF',
            '13-something-awful' => 'Something Awful',
            '20-yandex' => 'Yandex',
        ];

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

    public function winnerImageUploadAction(EntityManagerInterface $em, Request $request, AuditService $auditService, ConfigService $configService)
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
            $imagePath = FileSystem::handleUploadedFile($request->files->get('file'), 'winners', $award->getId());
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()]);
        }

        $award->setWinnerImage($imagePath);
        $em->persist($award);

        $auditService->add(
            new Action('winner-image-updated', $award->getId()),
            new TableHistory(Award::class, $award->getId(), ['image' => $imagePath])
        );
        $em->flush();

        return $this->json(['success' => true, 'filePath' => $imagePath]);
    }
}
