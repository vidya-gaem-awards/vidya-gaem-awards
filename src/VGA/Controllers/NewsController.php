<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\Response;
use VGA\Model\News;

class NewsController extends BaseController
{
    public function indexAction()
    {
        $repo = $this->em->getRepository(News::class);
        $query = $repo->createQueryBuilder('n');
        $query->select('n')
            ->where('n.visible = true')
            ->orderBy('n.timestamp', 'DESC');

        $news = $query->getQuery()->getResult();

        $tpl = $this->twig->loadTemplate('news.twig');

        $response = new Response($tpl->render([
            'title' => 'News',
            'news' => $news
        ]));
        $response->send();
    }
}
