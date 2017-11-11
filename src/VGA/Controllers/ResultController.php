<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use VGA\FileSystem;
use VGA\Model\Action;
use VGA\Model\Category;
use VGA\Model\TableHistory;

class ResultController extends BaseController
{
    public function simpleAction()
    {
        /** @var Category[] $categories */
        $categories = $this->em->getRepository(Category::class)
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

        foreach ($categories as $category) {
            $rankings = array_values($category->getOfficialResults()->getResults());

            foreach ($rankings as $key => &$value) {
                $value = $ranks[$key] . '. ' . $category->getNominee($value)->getName();
            }
            $theOthers = implode(', ', array_slice($rankings, 5));
            $rankings = array_slice($rankings, 0, 5);
            $rankings[] = $theOthers;

            $results[$category->getId()] = $rankings;
        }

        $tpl = $this->twig->loadTemplate('winners.twig');
        $response = new Response($tpl->render([
            'categories' => $categories,
            'results' => $results
        ]));
        $response->send();
    }

    public function detailedAction($all = null)
    {
        /** @var Category[] $categories */
        $categories = $this->em->getRepository(Category::class)
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
                '16-8chan' => '8chan',
            ],
        ];

        $nominees = [];

        foreach ($categories as $category) {
            foreach ($category->getNominees() as $nominee) {
                $nominees[$category->getId()][$nominee->getShortName()] = $nominee;
            }
            foreach ($category->getResultCache() as $result) {
                $results[$category->getId()][$result->getFilter()] = $result;
            }
        }

        $tpl = $this->twig->loadTemplate('results.twig');

        $response = new Response($tpl->render([
            'title' => 'Results',
            'categories' => $categories,
            'nominees' => $nominees,
            'all' => (bool)$all,
            'results' => $results,
            'filters' => $filters
        ]));
        $response->send();
    }

    public function pairwiseAction()
    {
        /** @var Category[] $categories */
        $categories = $this->em->getRepository(Category::class)
            ->createQueryBuilder('c')
            ->where('c.enabled = true')
            ->orderBy('c.order', 'ASC')
            ->getQuery()
            ->getResult();

        $pairwise = [];

        foreach ($categories as $category) {
            $pairwise[$category->getId()] = $category->getOfficialResults()->getSteps()['pairwise'];
        }

        $tpl = $this->twig->loadTemplate('resultsPairwise.twig');
        $response = new Response($tpl->render([
            'categories' => $categories,
            'pairwise' => $pairwise
        ]));
        $response->send();
    }

    public function winnerImageUploadAction()
    {
        $response = new JsonResponse();
        if ($this->config->isReadOnly()) {
            $response->setData(['error' => 'The site is currently in read-only mode. No changes can be made.']);
            $response->send();
            return;
        }

        $post = $this->request->request;
        $id = $post->get('id') ?? false;

        /** @var Category $award */
        $award = $this->em->getRepository(Category::class)->find($id);

        if (!$award) {
            $response->setData(['error' => 'Invalid award specified.']);
            $response->send();
            return;
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

        $action = new Action('winner-image-updated');
        $action->setUser($this->user)
            ->setPage(__CLASS__)
            ->setData1($post->get('id'));
        $this->em->persist($action);

        $history = new TableHistory();
        $history->setUser($this->user)
            ->setTable('Category')
            ->setEntry($post->get('id'))
            ->setValues(['image' => $imagePath]);
        $this->em->persist($history);
        $this->em->flush();

        $response->setData(['success' => true, 'filePath' => $imagePath]);
        $response->send();
        return;
    }
}
