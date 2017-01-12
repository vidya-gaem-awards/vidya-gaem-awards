<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\Response;
use VGA\Model\Award;

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

        $tpl = $this->twig->loadTemplate('winners.twig');
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
                '02-voting-code' => 'Voting code',
                '04-4chan' => '4chan',
                '08-4chan-or-null-with-voting-code' => '4chan + NULL with code',
            ],
            [
                '05-4chan-and-voting-code' => '4chan with code',
                '06-4chan-without-voting-code' => '4chan without code',
                '03-null' => 'NULL',
                '07-4chan-or-null' => '4chan + NULL',
            ],
            [
                '09-null-and-voting-code' => 'NULL with code',
                '10-null-without-voting-code' => 'NULL without code',
            ],
            [
                '17-twitch' => 'Twitch',
                '11-reddit' => 'Reddit',
                '18-facebook' => 'Facebook',
                '19-google' => 'Google',
            ],
            [
                '15-facepunch' => 'Facepunch',
                '16-8chan' => '8chan',
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

        $tpl = $this->twig->loadTemplate('results.twig');

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

        $tpl = $this->twig->loadTemplate('resultsPairwise.twig');
        $response = new Response($tpl->render([
            'awards' => $awards,
            'pairwise' => $pairwise
        ]));
        $response->send();
    }
}
