<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use VGA\FileSystem;
use VGA\Model\Award;
use VGA\Model\TableHistory;

class ResultController extends BaseController
{
    public function simpleAction()
    {
        /** @var Award[] $awards */
        $awards = $this->em->getRepository(Award::class)
            ->createQueryBuilder('c')
            ->where('c.enabled = true')
            ->orderBy('c.order', 'ASC')
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

        $tpl = $this->twig->load('winners.twig');
        $response = new Response($tpl->render([
            'awards' => $awards,
            'results' => $results
        ]));
        $response->send();
    }

    public function detailedAction($all = null)
    {
        /** @var Award[] $awards */
        $awards = $this->em->getRepository(Award::class)
            ->createQueryBuilder('c')
            ->where('c.enabled = true')
            ->orderBy('c.order', 'ASC')
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
        }

        $tpl = $this->twig->load('results.twig');

        $response = new Response($tpl->render([
            'title' => 'Results',
            'awards' => $awards,
            'nominees' => $nominees,
            'all' => (bool)$all,
            'results' => $results,
            'filters' => $filters
        ]));
        $response->send();
    }

    public function pairwiseAction()
    {
        /** @var Award[] $awards */
        $awards = $this->em->getRepository(Award::class)
            ->createQueryBuilder('c')
            ->where('c.enabled = true')
            ->orderBy('c.order', 'ASC')
            ->getQuery()
            ->getResult();

        $pairwise = [];

        foreach ($awards as $award) {
            $pairwise[$award->getId()] = $award->getOfficialResults()->getSteps()['pairwise'];
        }

        $tpl = $this->twig->load('resultsPairwise.twig');
        $response = new Response($tpl->render([
            'awards' => $awards,
            'pairwise' => $pairwise
        ]));
        $response->send();
    }

    public function winnerImageUploadAction()
    {
        $response = new JsonResponse();
        $id = $this->request->request->get('id') ?? false;

        /** @var Award $award */
        $award = $this->em->getRepository(Award::class)->find($id);

        if (!$award || ($award->isSecret() && !$this->user->canDo('awards-secret'))) {
            $response->setData(['error' => 'Invalid award specified.']);
            $response->send();
        }

        try {
            $imagePath = FileSystem::handleUploadedFile($_FILES['file'], 'winners', $award->getId());
        } catch (\Exception $e) {
            $response->setData(['error' => $e->getMessage()]);
            $response->send();
            return;
        }

        $award->setWinnerImage($imagePath);
        $this->em->persist($award);

        $history = new TableHistory();
        $history->setUser($this->user)
            ->setTable('Award')
            ->setEntry($award->getId())
            ->setValues(['image' => $imagePath]);
        $this->em->persist($history);
        $this->em->flush();

        $response->setData(['success' => true, 'filePath' => $imagePath]);
        $response->send();
    }
}
