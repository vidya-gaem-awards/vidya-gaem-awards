<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\Advertisement;
use App\Entity\CaptchaGame;
use App\Entity\TableHistory;
use App\Service\AuditService;
use App\Service\ConfigService;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
}
