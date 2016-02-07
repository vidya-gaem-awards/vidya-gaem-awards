<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use VGA\Model\Action;
use VGA\Model\Autocompleter;
use VGA\Model\Category;
use VGA\Model\TableHistory;
use VGA\Utils;

class CategoryAdminController extends BaseController
{
    /**
     * @param Category $categoryEditing
     */
    public function managerListAction($categoryEditing = null)
    {
        $repo = $this->em->getRepository(Category::class);
        $condition = $this->user->canDo('categories-secret') ? [] : ['secret' => false];
        $categories = $repo->findBy($condition, ['order' => 'ASC']);

        if ($this->request->get('sort') === 'feedback') {
            usort($categories, function ($a, $b) {
                /** @var Category $a */
                /** @var Category $b */
                return $b->getFeedbackPercent()['positive'] <=> $a->getFeedbackPercent()['positive'];
            });
        }

        $tpl = $this->twig->loadTemplate('categoryManager.twig');

        $variables = [
            'title' => 'Manage Awards',
            'categories' => $categories
        ];

        if ($categoryEditing !== null) {
            $variables['category'] = $categoryEditing;
            $variables['editing'] = true;
        }

        $response = new Response($tpl->render($variables));
        $response->send();
    }

    public function managerPostAction()
    {
        $post = $this->request->request;
        $flashbag = $this->session->getFlashBag();

        // Add a new category
        if ($post->get('action') === 'new') {
            if (strlen($post->get('id')) == 0 || strlen($post->get('name')) == 0 ||
                strlen($post->get('subtitle')) == 0 || strlen($post->get('order')) == 0) {
                $flashbag->add('formError', 'You need to fill in all of the fields.');
            } elseif (!preg_match('/^[0-9a-zA-Z-]+$/', $post->get('id'))) {
                $flashbag->add('formError', 'ID can only contain letters, numbers and dashes.');
            } elseif (!ctype_digit($post->get('order'))) {
                $flashbag->add('formError', 'The order must be a positive integer.');
            } elseif (intval($_POST['order']) > 10000) {
                $flashbag->add('formError', 'Order must be less than 10000.');
            } else {
                $category = new Category();
                $category
                    ->setId(strtolower($post->get('id')))
                    ->setName($post->get('name'))
                    ->setSubtitle($post->get('subtitle'))
                    ->setOrder($post->getInt('order'))
                    ->setEnabled($post->getBoolean('enabled'))
                    ->setNominationsEnabled($post->getBoolean('nominations'))
                    ->setSecret($post->getBoolean('secret'));
                $this->em->persist($category);

                $action = new Action('category-added');
                $action->setUser($this->user)
                    ->setPage(__CLASS__)
                    ->setData1($post->get('id'));
                $this->em->persist($action);

                $history = new TableHistory();
                $history->setUser($this->user)
                    ->setTable('Category')
                    ->setEntry($post->get('id'))
                    ->setValues($post->all());
                $this->em->persist($history);

                $this->em->flush();
                $flashbag->add('formSuccess', 'Category successfully added.');
            }
        }

        // Open / close all categories
        if ($post->get('action') === 'massChangeNominations') {
            $repo = $this->em->getRepository(Category::class);
            $query = $repo->createQueryBuilder('c');

            if ($post->get('todo') === 'open') {
                $query->update()->set('c.nominationsEnabled', true);
                $query->getQuery()->execute();

                $action = new Action('mass-nomination-change');
                $action->setUser($this->user)
                    ->setPage(__CLASS__)
                    ->setData1('open');
                $this->em->persist($action);
                $flashbag->add('formSuccess', 'Nominations for all categories are now open.');
            } elseif ($post->get('todo') === 'close') {
                $query->update()->set('c.nominationsEnabled', 0);
                $query->getQuery()->execute();

                $action = new Action('mass-nomination-change');
                $action->setUser($this->user)
                    ->setPage(__CLASS__)
                    ->setData1('close');
                $this->em->persist($action);
                $flashbag->add('formSuccess', 'Nominations for all categories are now closed.');
            }

            $this->em->flush();
        }

        $response = new RedirectResponse($this->generator->generate('categoryManager'));
        $response->send();
    }

    public function editCategoryAction($category)
    {
        /** @var Category $category */
        $category = $this->em->getRepository(Category::class)->find($category);

        if (!$category || ($category->isSecret() && !$this->user->canDo('categories-secret'))) {
            $this->session->getFlashBag()->add('error', 'Invalid category ID specified.');
            $response = new RedirectResponse($this->generator->generate('categoryManager'));
            $response->send();
            return;
        }

        $autocompleters = $this->em->getRepository(Autocompleter::class)->findAll();
        $this->twig->addGlobal('autocompleters', $autocompleters);

        $this->managerListAction($category);
    }

    public function editCategoryPostAction($category)
    {
        /** @var Category $category */
        $category = $this->em->getRepository(Category::class)->find($category);

        if (!$category || ($category->isSecret() && !$this->user->canDo('categories-secret'))) {
            $this->session->getFlashBag()->add('error', 'Invalid category ID specified.');
            $response = new RedirectResponse($this->generator->generate('categoryManager'));
            $response->send();
            return;
        }

        $post = $this->request->request;
        $flashbag = $this->session->getFlashBag();

        if ($post->get('delete')) {
            if ($this->user->canDo('categories-delete')) {
                $this->em->remove($category);
                $this->em->flush();

                $flashbag->add('formSuccess', sprintf('Category \'%s\' successfully deleted.', $category->getName()));

                $action = new Action('category-delete');
                $action->setUser($this->user)
                    ->setPage(__CLASS__)
                    ->setData1($category->getId());
                $this->em->persist($action);
                $this->em->flush();
            } else {
                $flashbag->add('formSuccess', 'You aren\'t allowed to delete categories.');
            }
            $response = new RedirectResponse($this->generator->generate('categoryManager'));
            $response->send();
        } else {
            if (strlen($post->get('name')) == 0 || strlen($post->get('subtitle')) == 0
                || strlen($post->get('order')) == 0) {
                $flashbag->add('editFormError', 'You need to fill in all of the fields.');
            } elseif (!ctype_digit($post->get('order'))) {
                $flashbag->add('editFormError', 'The order must be a positive integer.');
            } elseif (intval($_POST['order']) > 10000) {
                $flashbag->add('editFormError', 'Order must be less than 10000.');
            } else {
                if ($post->get('autocompleter')) {
                    $autocompleter = $this->em->getRepository(Autocompleter::class)->find($post->get('autocompleter'));
                    if (!$autocompleter) {
                        $autocompleter = null;
                    }
                } else {
                    $autocompleter = null;
                }

                $category
                    ->setName($post->get('name'))
                    ->setSubtitle($post->get('subtitle'))
                    ->setComments($post->get('comments'))
                    ->setOrder($post->getInt('order'))
                    ->setAutocompleter($autocompleter)
                    ->setEnabled((bool)$post->get('enabled'))
                    ->setNominationsEnabled((bool)$post->get('nominationsEnabled'))
                    ->setSecret((bool)$post->get('secret'));

                if ($this->user->canDo('voting-results')) {
                    if ($post->get('winnerImage') && !Utils::startsWith($post->get('winnerImage'), 'https://')) {
                        $flashbag->add('editFormError', 'Winner image must start with https://');
                    } else {
                        $category->setWinnerImage($post->get('winnerImage'));
                    }
                }

                $this->em->persist($category);

                $action = new Action('category-edited');
                $action->setUser($this->user)
                    ->setPage(__CLASS__)
                    ->setData1($category->getId());
                $this->em->persist($action);

                $history = new TableHistory();
                $history->setUser($this->user)
                    ->setTable('Category')
                    ->setEntry($category->getId())
                    ->setValues($post->all());
                $this->em->persist($history);
                $this->em->flush();

                $flashbag->add('editFormSuccess', 'Category successfully edited.');
            }

            $response = new RedirectResponse(
                $this->generator->generate('editCategory', ['category' => $category->getId()])
            );
            $response->send();
        }
    }
}
