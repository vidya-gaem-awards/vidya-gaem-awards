<?php
namespace App\Controller;

use App\Entity\Action;
use App\Entity\Advertisement;
use App\Entity\TableHistory;
use App\Entity\User;
use App\Service\AuditService;
use App\Service\ConfigService;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class AdvertManagerController extends AbstractController
{
    public function indexAction(EntityManagerInterface $em): Response
    {
        $query = $em->createQueryBuilder();
        $adverts = $query
            ->select('a')
            ->from(Advertisement::class, 'a')
            ->indexBy('a', 'a.id')
            ->orderBy('a.special', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('advertManager.html.twig', [
            'adverts' => $adverts
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
            $advert = new Advertisement();
        } else {
            $advert = $em->getRepository(Advertisement::class)->find($post->get('id'));
            if (!$advert) {
                $this->json(['error' => 'Invalid advert specified.']);
            }
        }

        if ($action === 'delete') {
            $em->remove($advert);
            $auditService->add(
                new Action('advert-delete', $advert->getId())
            );
            $em->flush();

            return $this->json(['success' => true]);
        }

        $name = $post->get('dialogName', '');
        if (strlen(trim($name)) === 0) {
            return $this->json(['error' => 'You need to enter a name.']);
        }

        $advert
            ->setName($name)
            ->setSpecial((bool)$post->get('dialogSpecial', false));

        if ($request->files->get('image')) {
            try {
                $file = $fileService->handleUploadedFile(
                    $request->files->get('image'),
                    'Advertisement.image',
                    'memes',
                    null
                );
            } catch (Exception $e) {
                return $this->json(['error' => $e->getMessage()]);
            }

            if ($advert->getImage()) {
                $fileService->deleteFile($advert->getImage());
            }

            $advert->setImage($file);
        }

        $em->persist($advert);
        $em->flush();

        $auditService->add(
            new Action('advert-' . $action, $advert->getId()),
            new TableHistory(Advertisement::class, $advert->getId(), $post->all())
        );

        $em->flush();

        return $this->json(['success' => true]);
    }

    public function redirectAction(string $advertToken, EntityManagerInterface $em, UserInterface $user): RedirectResponse
    {
        $advert = $em->getRepository(Advertisement::class)->findOneBy(['token' => $advertToken]);
        if (!$advert) {
            throw $this->createNotFoundException();
        }

        /** @var User $user */
        if (!$user->isSpecial()) {
            $advert->incrementClicks();
            $em->persist($advert);
            $em->flush();
        }

        return new RedirectResponse($advert->getLink());
    }
}
