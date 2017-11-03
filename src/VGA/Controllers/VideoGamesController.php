<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Action;
use AppBundle\Entity\GameRelease;


class VideoGamesController extends BaseController
{
    public function indexAction()
    {
        $repo = $this->em->getRepository(GameRelease::class);
        $query = $repo->createQueryBuilder('gr');
        $query->select('gr')
            ->orderBy('gr.name', 'ASC');

        $games = $query->getQuery()->getResult();

        $tpl = $this->twig->load('videoGames.twig');

        $response = new Response($tpl->render([
            'title' => 'Vidya in 2017',
            'games' => $games
        ]));
        $response->send();
    }

    public function addAction()
    {
        $response = new JsonResponse();

        if ($this->config->isReadOnly()) {
            $response->setData(['error' => 'The site is currently in read-only mode. No changes can be made.']);
            $response->send();
            return;
        }

        $post = $this->request->request;

        $game = trim($post->get('name'));

        if (trim($game) === '') {
            $response->setData(['error' => 'Please enter the name of the game.']);
            $response->send();
            return;
        }

        $game = new GameRelease($game);

        $platforms = ['pc', 'vr', 'ps3', 'ps4', 'vita', 'x360', 'xb1', 'xbla', 'wii', 'wiiu', 'n3ds', 'mobile'];
        foreach ($platforms as $platform) {
            if ($post->get($platform)) {
                $game->{'set'.$platform}(true);
            }
        }

        if (count($game->getPlatforms()) === 0) {
            $response->setData(['error' => 'You need to select at least one platform.']);
            $response->send();
            return;
        }

        $this->em->persist($game);

        $action = new Action('add-video-game');
        $action->setUser($this->user)
            ->setPage(__CLASS__)
            ->setData1($game->getName());
        $this->em->persist($action);
        $this->em->flush();

        $response->setData(['success' => $game->getName()]);
        $response->send();
    }
}
