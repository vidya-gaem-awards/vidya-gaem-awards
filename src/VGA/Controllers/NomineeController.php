<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use VGA\Model\Category;

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
        }

        $alphabeticalSort = $this->request->get('sort') === 'alphabetical';

        $tpl = $this->twig->loadTemplate('nominees.twig');

        $response = new Response($tpl->render([
            'title' => 'Nominee Manager',
            'categories' => $categories,
            'category' => $category,
            'alphabeticalSort' => $alphabeticalSort
        ]));
        $response->send();
    }
}
