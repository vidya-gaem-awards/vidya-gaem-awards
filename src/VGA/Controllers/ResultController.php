<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\Response;
use VGA\Model\Category;
use VGA\Model\ResultCache;

class ResultController extends BaseController
{
    public function indexAction($all = null)
    {
        /** @var Category[] $categories */
        $categories = $this->em->getRepository(Category::class)
            ->createQueryBuilder('c')
            ->where('c.enabled = true')
            ->orderBy('c.order', 'ASC')
            ->getQuery()
            ->getResult();

        $filters = [
            '01-all' => 'No filtering',
            '02-voting-code' => 'Voting code',
            '03-null' => 'NULL',
            '04-4chan' => '4chan',
            '05-4chan-and-voting-code' => '4chan with code',
            '06-4chan-without-voting-code' => '4chan without code',
            '07-4chan-or-null' => '4chan + NULL',
            '08-4chan-or-null-with-voting-code' => '4chan + NULL with code',
            '09-null-and-voting-code' => 'NULL with code',
            '10-null-without-voting-code' => 'NULL without code',
            '11-reddit' => 'Reddit',
            '12-twitter' => 'Twitter',
            '13-something-awful' => 'Something Awful',
            '14-neogaf' => 'NeoGAF',
            '15-facepunch' => 'Facepunch',
            '16-8chan' => '8chan',
            '17-twitch' => 'Twitch',
            '18-facebook' => 'Facebook',
            '19-google' => 'Google',
        ];

        $nominees = [];

        foreach ($categories as $category) {
            foreach ($category->getNominees() as $nominee) {
                $nominees[$category->getId()][$nominee->getShortName()] = $nominee;
            }
        }

        $tpl = $this->twig->loadTemplate('results.twig');

        $response = new Response($tpl->render([
            'title' => 'Results',
            'categories' => $categories,
            'nominees' => $nominees,
            'all' => (bool)$all,
            'filters' => $filters
        ]));
        $response->send();
    }

    public function pairwiseAction()
    {
        /** @var ResultCache[] $results */
        $results = $this->em->getRepository(ResultCache::class)
            ->createQueryBuilder('rc')
            ->join('rc.category', 'c')
            ->where('c.enabled = true')
            ->andWhere("rc.filter = '08-4chan-or-null-with-voting-code'")
            ->orderBy('c.order', 'ASC')
            ->getQuery()
            ->getResult();

        $categories = [];
        $pairwise = [];

        foreach ($results as $result) {
            $category = $result->getCategory();
            $categories[] = $category;
            $pairwise[$category->getId()] = $result->getSteps()['pairwise'];
        }

        $tpl = $this->twig->loadTemplate('resultsPairwise.twig');
        $response = new Response($tpl->render([
            'categories' => $categories,
            'pairwise' => $pairwise
        ]));
        $response->send();
    }
}
