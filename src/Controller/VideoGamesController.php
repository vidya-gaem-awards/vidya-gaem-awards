<?php
namespace App\Controller;

use App\Entity\TableHistory;
use App\Service\AuditService;
use App\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Action;
use App\Entity\GameRelease;

class VideoGamesController extends Controller
{
    public function indexAction(EntityManagerInterface $em)
    {
        $query = $em->createQueryBuilder()
            ->from(GameRelease::class, 'gr')
            ->select('gr')
            ->orderBy('gr.name', 'ASC');

        $games = $query->getQuery()->getResult();

        return $this->render('videoGames.html.twig', [
            'title' => 'Vidya in 2018',
            'games' => $games
        ]);
    }

    public function add(EntityManagerInterface $em, ConfigService $configService, Request $request, AuditService $auditService)
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

        $platforms = ['pc', 'vr', 'ps3', 'ps4', 'vita', 'x360', 'xb1', 'wiiu', 'switch', 'n3ds', 'mobile'];
        foreach ($platforms as $platform) {
            if ($post->get($platform)) {
                $game->{'set'.$platform}(true);
            }
        }

        if (count($game->getPlatforms()) === 0) {
            return $this->json(['error' => 'You need to select at least one platform.']);
        }

        $em->persist($game);
        $em->flush();

        $auditService->add(
            new Action('add-video-game', $game->getId()),
            new TableHistory(GameRelease::class, $game->getId(), $post->all())
        );
        $em->flush();

        return $this->json(['success' => $game->getName()]);
    }

    public function remove(EntityManagerInterface $em, ConfigService $configService, Request $request, AuditService $auditService)
    {
        if ($configService->isReadOnly()) {
            return $this->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        $post = $request->request;

        $game = $em->getRepository(GameRelease::class)->find($post->get('id'));
        if (!$game) {
            return $this->json(['error' => 'Couldn\'t find the selected game. Perhaps it has already been removed?']);
        }

        $em->remove($game);

        $auditService->add(
            new Action('remove-video-game', $game->getName())
        );
        $em->flush();

        return $this->json(['success' => true]);
    }
}
