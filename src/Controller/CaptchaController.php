<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\Advertisement;
use App\Entity\BaseUser;
use App\Entity\CaptchaGame;
use App\Entity\CaptchaResponse;
use App\Entity\TableHistory;
use App\Repository\CaptchaGameRepository;
use App\Repository\CaptchaResponseRepository;
use App\Service\AuditService;
use App\Service\ConfigService;
use App\Service\FileService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ZipArchive;

class CaptchaController extends AbstractController
{
    public function indexAction(EntityManagerInterface $em)
    {
        $query = $em->createQueryBuilder();
        $games = $query
            ->select('cg')
            ->from(CaptchaGame::class, 'cg')
            ->indexBy('cg', 'cg.id')
            ->orderBy('cg.first', 'ASC')
            ->addOrderBy('cg.second', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('captchaManager.html.twig', [
            'games' => $games,
            'rows' => CaptchaGame::ROWS,
            'columns' => CaptchaGame::COLUMNS,
        ]);
    }

    public function statsAction(CaptchaGameRepository $repo, CaptchaResponseRepository $responseRepo)
    {
        $games = $repo->getGames();

        $gameData = [];

        foreach ($games as $game) {
            $responses = $responseRepo
                ->createQueryBuilder('cr')
                ->where('cr.first = :first')
                ->andWhere('cr.second = :second')
                ->setParameter('first', $game->getFirst())
                ->setParameter('second', $game->getSecond())
                ->getQuery()
                ->getResult();

            $correct = count(array_filter($responses, fn (CaptchaResponse $response) => in_array($game->getId(), $response->getSelected())));

            $gameData[] = [
                'game' => $game,
                'completions' => count($responses),
                'correct' => $correct,
                'incorrect' => count($responses) - $correct,
                'percent' => round($correct / count($responses) * 100, 2),
            ];
        }

        usort($gameData, fn ($a, $b) => $b['percent'] <=> $a['percent']);

        return $this->render('captchaStats.html.twig', [
            'gameData' => $gameData
        ]);
    }

    public function postAction(ConfigService $configService, Request $request, EntityManagerInterface $em, AuditService $auditService, FileService $fileService): JsonResponse
    {
        if ($configService->isReadOnly()) {
            return $this->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        $post = $request->request;
        $action = $post->get('action');

        if (!in_array($action, ['new', 'edit', 'delete'], true)) {
            return $this->json(['error' => 'Invalid action specified.']);
        }

        if ($action === 'new') {
            $game = new CaptchaGame();
        } else {
            /** @var CaptchaGame|null $game */
            $game = $em->getRepository(CaptchaGame::class)->find($post->get('id'));
            if (!$game) {
                $this->json(['error' => 'Invalid game specified.']);
            }
        }

        if ($action === 'delete') {
            $em->remove($game);
            $auditService->add(
                new Action('captcha-game-delete', $game->getId())
            );
            $em->flush();

            return $this->json(['success' => true]);
        }

        $title = $post->get('dialogTitle', '');
        if (strlen(trim($title)) === 0) {
            return $this->json(['error' => 'You need to enter a title.']);
        }

        $row = $post->get('dialogFirst', '');
        if (!in_array($row, CaptchaGame::ROWS, true)) {
            return $this->json(['error' => 'Invalid row specified.']);
        }

        $column = $post->get('dialogSecond', '');
        if (!in_array($column, CaptchaGame::COLUMNS, true)) {
            return $this->json(['error' => 'Invalid column specified.']);
        }

        $game
            ->setTitle($title)
            ->setFirst($row)
            ->setSecond($column);

        if ($request->files->get('image')) {
            try {
                $file = $fileService->handleUploadedFile(
                    $request->files->get('image'),
                    'CaptchaGame.image',
                    'captcha-games',
                    null
                );
            } catch (Exception $e) {
                return $this->json(['error' => $e->getMessage()]);
            }

            if ($game->getImage()) {
                $fileService->deleteFile($game->getImage());
            }

            $game->setImage($file);
        }

        $em->persist($game);
        $em->flush();

        $auditService->add(
            new Action('captcha-game-' . $action, $game->getId()),
            new TableHistory(CaptchaGame::class, $game->getId(), $post->all())
        );

        $em->flush();

        return $this->json(['success' => true]);
    }

    public function bulkImageAction()
    {
        return $this->render('captchaBulkImageUploader.html.twig');
    }

    public function bulkImageSubmitAction(ConfigService $configService, Request $request, EntityManagerInterface $em, AuditService $auditService, FileService $fileService)
    {
        if ($configService->isReadOnly()) {
            $this->addFlash('error', 'The site is currently in read-only mode. No changes can be made.');
            return $this->redirectToRoute('captchaBulkImageUploader');
        }

        /** @var UploadedFile|null $file */
        $file = $request->files->get('images');
        $fileService->validateUploadedFile($file);

        $zip = new ZipArchive();
        $zip->open($file->getPathname());

        $processedFiles = 0;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            $pattern = '/^(' . implode('|', CaptchaGame::ROWS) . ') ([1-9]|1[0-6]+)\.png$/';
            if (!preg_match($pattern, $name, $matches)) {
                continue;
            }

            $row = $matches[1];
            $column = CaptchaGame::COLUMNS[$matches[2] - 1];
            $filename = strtolower(str_replace(' ', '-', "{$row}_{$column}"));

            $game = $em->getRepository(CaptchaGame::class)->findOneBy([
                'first' => $matches[1],
                'second' => CaptchaGame::COLUMNS[$matches[2] - 1],
            ]);

            if (!$game) {
                continue;
            }

            $stream = $zip->getStream($name);

            if (!$stream) {
                continue;
            }

            $contents = '';

            while (!feof($stream)) {
                $contents .= fread($stream, 2);
            }

            $gameFile = $fileService->createFileFromString(
                $contents,
                'png',
                'CaptchaGame.image',
                'captcha-games',
                $filename
            );

            if ($game->getImage()) {
                $fileService->deleteFile($game->getImage());
            }

            $game->setImage($gameFile);
            $em->persist($game);
            $processedFiles++;
        }

        $em->flush();

        $auditService->add(
            new Action('captcha-game-bulk-upload', $processedFiles),
        );

        if ($processedFiles === 0) {
            $this->addFlash('error', '0 files succesfully processed.');
            return $this->redirectToRoute('captchaBulkImageUploader');
        }

        $this->addFlash('success', "{$processedFiles} file" . ($processedFiles === 1 ? '' : 's') . ' succesfully processed.');
        return $this->redirectToRoute('captchaManager');
    }

    public function resultAction(
        ConfigService $configService,
        AuthorizationCheckerInterface $authChecker,
        EntityManagerInterface $em,
        AuditService $auditService,
        UserInterface $user,
        Request $request,
    ): JsonResponse {
        if ($configService->isReadOnly()) {
            return $this->json(['error' => 'Voting has closed.']);
        }

        if (!$authChecker->isGranted('ROLE_VOTING_VIEW')) {
            if ($configService->getConfig()->isVotingNotYetOpen()) {
                return $this->json(['error' => 'Voting hasn\'t started yet.']);
            } elseif ($configService->getConfig()->hasVotingClosed()) {
                return $this->json(['error' => 'Voting has closed.']);
            }
        }

        /** @var BaseUser $user */

        $response = new CaptchaResponse();
        $response
            ->setTimestamp(new DateTimeImmutable())
            ->setUser($user->getFuzzyID())
            ->setFirst($request->request->get('row'))
            ->setSecond($request->request->get('column'))
            ->setScore($request->request->get('score'))
            ->setGames($request->request->all('games'))
            ->setSelected($request->request->all('selected'));

        $auditService->add(
            new Action('captcha-game-result'),
        );

        $em->persist($response);
        $em->flush();

        return $this->json(['success' => true]);
    }
}
