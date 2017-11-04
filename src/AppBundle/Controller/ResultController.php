<?php
namespace AppBundle\Controller;

use AppBundle\Service\NavbarService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use VGA\FileSystem;
use AppBundle\Entity\Award;
use AppBundle\Entity\TableHistory;

class ResultController extends Controller
{
    public function simpleAction(EntityManagerInterface $em, NavbarService $navbar)
    {
        if (!$navbar->canAccessRoute('results')) {
            throw $this->createAccessDeniedException();
        }

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
                $value = $ranks[$key] . '. ' . $award->getNominee($value)->getName();
            }
            $theOthers = implode(', ', array_slice($rankings, 5));
            $rankings = array_slice($rankings, 0, 5);
            $rankings[] = $theOthers;

            $results[$award->getId()] = $rankings;
        }

        return $this->render('winners.twig', [
            'awards' => $awards,
            'results' => $results
        ]);
    }

    public function detailedAction(?string $all, EntityManagerInterface $em, NavbarService $navbar)
    {
        if (!$navbar->canAccessRoute('results')) {
            throw $this->createAccessDeniedException();
        }

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
            [
                '01-all' => 'No filtering',
                '03-null' => 'No referrer',
                '08-4chan-or-null-with-voting-code' => 'Certified 4chan',
                '16-8chan' => '8chan',
//                '02-voting-code' => 'Voting code',
//                '04-4chan' => '4chan',
//            ],
//            [
//                '05-4chan-and-voting-code' => '4chan with code',
//                '06-4chan-without-voting-code' => '4chan without code',

//                '07-4chan-or-null' => '4chan + NULL',
            ],
//            [
//                '09-null-and-voting-code' => 'NULL with code',
//                '10-null-without-voting-code' => 'NULL without code',
//            ],
            [
                '19-google' => 'Google',
                '15-facepunch' => 'Facepunch',
                '18-facebook' => 'Facebook',

            ],
            [
                '17-twitch' => 'Twitch',
                '11-reddit' => 'Reddit',
                '14-neogaf' => 'NeoGAF',


            ],
        ];

        $nominees = [];

        foreach ($awards as $award) {
            foreach ($award->getNominees() as $nominee) {
                $nominees[$award->getId()][$nominee->getShortName()] = $nominee;
            }
            foreach ($award->getResultCache() as $result) {
                $results[$award->getId()][$result->getFilter()] = $result;
            }

            // If the result cache is empty, results haven't been generated yet.
            if ($award->getResultCache()->isEmpty()) {
                $results[$award->getId()] = null;
            }
        }

        return $this->render('results.twig', [
            'title' => 'Results',
            'awards' => $awards,
            'nominees' => $nominees,
            'all' => (bool)$all,
            'results' => $results,
            'filters' => $filters
        ]);
    }

    public function pairwiseAction(EntityManagerInterface $em, NavbarService $navbar)
    {
        if (!$navbar->canAccessRoute('results')) {
            throw $this->createAccessDeniedException();
        }

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

        return $this->render('resultsPairwise.twig', [
            'awards' => $awards,
            'pairwise' => $pairwise
        ]);
    }

    public function winnerImageUploadAction(EntityManagerInterface $em, AuthorizationCheckerInterface $authChecker, UserInterface $user, Request $request)
    {
        $id = $request->request->get('id') ?? false;

        /** @var Award $award */
        $award = $em->getRepository(Award::class)->find($id);

        if (!$award || ($award->isSecret() && !$authChecker->isGranted('ROLE_AWARDS_SECRET'))) {
            return $this->json(['error' => 'Invalid award specified.']);
        }

        try {
            $imagePath = FileSystem::handleUploadedFile($_FILES['file'], 'winners', $award->getId());
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()]);
        }

        $award->setWinnerImage($imagePath);
        $em->persist($award);

        $history = new TableHistory();
        $history->setUser($user)
            ->setTable('Award')
            ->setEntry($award->getId())
            ->setValues(['image' => $imagePath]);
        $em->persist($history);
        $em->flush();

        return $this->json(['success' => true, 'filePath' => $imagePath]);
    }
}
