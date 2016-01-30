<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use VGA\Model\Action;
use VGA\Model\Category;
use VGA\Model\Nominee;
use VGA\Model\TableHistory;

class NomineeController extends BaseController
{
    public function indexAction($category = null)
    {
        $repo = $this->em->getRepository(Category::class);
        $query = $repo->createQueryBuilder('c', 'c.id');
        $query->select('c')
            ->where('c.enabled = true')
            ->orderBy('c.order', 'ASC');

        if (!$this->user->canDo('categories-secret')) {
            $query->andWhere('c.secret = false');
        }
        $categories = $query->getQuery()->getResult();

        $categoryVariables = [];

        if ($category) {
            /** @var Category $category */
            $category = $this->em->getRepository(Category::class)->find($category);

            if (!$category || ($category->isSecret() && !$this->user->canDo('categories-secret'))) {
                $this->session->getFlashBag()->add('error', 'Invalid category ID specified.');
                $response = new RedirectResponse(
                    $this->generator->generate('nomineeManager', [] , UrlGenerator::ABSOLUTE_URL)
                );
                $response->send();
                return;
            }

            $alphabeticalSort = $this->request->get('sort') === 'alphabetical';

            $autocompleters = array_filter($category->getUserNominations(), function ($un) {
                return $un['count'] >= 3;
            });
            $autocompleters = array_map(function ($un) {
                return $un['title'];
            }, $autocompleters);
            sort($autocompleters);

            $nomineesArray = [];
            /** @var Nominee $nominee */
            foreach ($category->getNominees() as $nominee) {
                $nomineesArray[$nominee->getShortName()] = $nominee;
            }

            $categoryVariables = [
                'alphabeticalSort' => $alphabeticalSort,
                'autocompleters' => $autocompleters,
                'nominees' => $nomineesArray
            ];
        }

        $tpl = $this->twig->loadTemplate('nominees.twig');

        $response = new Response($tpl->render(array_merge([
            'title' => 'Nominee Manager',
            'categories' => $categories,
            'category' => $category,
        ], $categoryVariables)));
        $response->send();
    }

    public function postAction($category)
    {
        $response = new JsonResponse();

        /** @var Category $category */
        $category = $this->em->getRepository(Category::class)->find($category);

        if (!$category || ($category->isSecret() && !$this->user->canDo('categories-secret'))) {
            $response->setData(['error' => 'Invalid category specified.']);
            $response->send();
            return;
        } elseif (!$category->isEnabled()) {
            $response->setData(['error' => 'Category isn\'t enabled.']);
            $response->send();
            return;
        }

        $post = $this->request->request;
        $action = $post->get('action');

        if (!in_array($action, ['new', 'edit', 'delete'], true)) {
            $response->setData(['error' => 'Invalid action specified.']);
            $response->send();
            return;
        }

        if ($action === 'new') {
            if ($category->getNominee($post->get('id'))) {
                $response->setData(['error' => 'A nominee with that ID already exists for this award.']);
                $response->send();
                return;
            } elseif (!$post->get('id')) {
                $response->setData(['error' => 'You need to enter an ID.']);
                $response->send();
                return;
            } elseif (preg_match('/[^a-z0-9-]/', $post->get('id'))) {
                $response->setData(['error' => 'ID can only contain lowercase letters, numbers and dashes.']);
                $response->send();
                return;
            }

            $nominee = new Nominee();
            $nominee
                ->setCategory($category)
                ->setShortName($post->get('id'));
        } else {
            $nominee = $category->getNominee($post->get('id'));
            if (!$nominee) {
                $response->setData(['error' => 'Invalid nominee specified.']);
            }
        }

        if ($action === 'delete') {
            $this->em->remove($nominee);

            $action = new Action('nominee-delete');
            $action->setUser($this->user)
                ->setPage(__CLASS__)
                ->setData1($category->getId())
                ->SetData2($nominee->getShortName());
            $this->em->persist($action);

            $this->em->flush();

            $response->setData(['success' => true]);
            $response->send();
            return;
        }

        if (strlen(trim($post->get('name', ''))) === 0) {
            $response->setData(['error' => 'You need to enter a name.']);
            $response->send();
            return;
        }

        $nominee
            ->setName($post->get('name'))
            ->setSubtitle($post->get('subtitle'))
            ->setImage($post->get('image'))
            ->setFlavorText($post->get('flavorText'));
        $this->em->persist($nominee);

        $action = new Action('nominee-' . $action);
        $action->setUser($this->user)
            ->setPage(__CLASS__)
            ->setData1($category->getId())
            ->SetData2($nominee->getShortName());
        $this->em->persist($action);

        $history = new TableHistory();
        $history->setUser($this->user)
            ->setTable('Nominee')
            ->setEntry($category->getId() . '/' . $nominee->getShortName())
            ->setValues($post->all());
        $this->em->persist($history);
        $this->em->flush();

        $response->setData(['success' => true]);
        $response->send();
    }
}
