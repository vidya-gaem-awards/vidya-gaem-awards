<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\Response;
use VGA\Model\News;

class CategoryController extends BaseController
{
    public function indexAction()
    {
        $repo = $this->em->getRepository(News::class);
        $query = $repo->createQueryBuilder('n');
        $query->select('n')
            ->where('n.visible = true')
            ->andWhere('n.timestamp < CURRENT_TIMESTAMP()')
            ->setMaxResults(5)
            ->orderBy('n.timestamp', 'DESC');

        $news = $query->getQuery()->getResult();

        $tpl = $this->twig->loadTemplate('categories.twig');

        $response = new Response($tpl->render([
            'title' => 'Awards and Nominations',
            'news' => $news
        ]));
        $response->send();
    }
}
