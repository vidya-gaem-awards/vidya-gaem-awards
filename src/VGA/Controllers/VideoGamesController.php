<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\Response;
use VGA\Model\GameRelease;
use VGA\Model\News;

class VideoGamesController extends BaseController
{
    public function indexAction()
    {
        $repo = $this->em->getRepository(GameRelease::class);
        $query = $repo->createQueryBuilder('gr');
        $query->select('gr')
            ->orderBy('gr.name', 'ASC');

        $games = $query->getQuery()->getResult();

        $tpl = $this->twig->loadTemplate('videoGames.twig');

        $response = new Response($tpl->render([
            'title' => 'Vidya in 2015',
            'games' => $games
        ]));
        $response->send();
    }
}
