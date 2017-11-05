<?php
namespace AppBundle\Controller;

use AppBundle\Service\ConfigService;
use AppBundle\Service\NavbarService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Action;
use AppBundle\Entity\GameRelease;
use Symfony\Component\Security\Core\User\UserInterface;

class VideoGamesController extends Controller
{
    public function indexAction(EntityManagerInterface $em, NavbarService $navbar)
    {
        if (!$navbar->canAccessRoute('videoGames')) {
            throw $this->createAccessDeniedException();
        }

        $query = $em->createQueryBuilder()
            ->from(GameRelease::class, 'gr')
            ->select('gr')
            ->orderBy('gr.name', 'ASC');

        $games = $query->getQuery()->getResult();

        return $this->render('videoGames.twig', [
            'title' => 'Vidya in 2017',
            'games' => $games
        ]);
    }

    public function addAction(EntityManagerInterface $em, ConfigService $configService, Request $request, UserInterface $user)
    {
        if ($configService->isReadOnly()) {
            return $this->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        $post = $request->request;

        $game = trim($post->get('name'));

        if (trim($game) === '') {
            return $this->json(['error' => 'Please enter the name of the game.']);
        }

        $game = new GameRelease($game);

        $platforms = ['pc', 'vr', 'ps3', 'ps4', 'vita', 'x360', 'xb1', 'xbla', 'wii', 'wiiu', 'n3ds', 'mobile'];
        foreach ($platforms as $platform) {
            if ($post->get($platform)) {
                $game->{'set'.$platform}(true);
            }
        }

        if (count($game->getPlatforms()) === 0) {
            return $this->json(['error' => 'You need to select at least one platform.']);
        }

        $em->persist($game);

        $action = new Action('add-video-game');
        $action->setUser($user)
            ->setData1($game->getName());
        $em->persist($action);
        $em->flush();

        return $this->json(['success' => $game->getName()]);
    }
}
