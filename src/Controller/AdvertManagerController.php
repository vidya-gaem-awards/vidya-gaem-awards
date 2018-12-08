<?php
namespace App\Controller;

use App\Entity\Action;
use App\Entity\Advertisement;
use App\Entity\TableHistory;
use App\Entity\User;
use App\Service\AuditService;
use App\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use App\VGA\FileSystem;

class AdvertManagerController extends AbstractController
{
    public function indexAction(EntityManagerInterface $em)
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

    public function postAction(ConfigService $configService, Request $request, EntityManagerInterface $em, AuditService $auditService)
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

        if (strlen(trim($post->get('name', ''))) === 0) {
            return $this->json(['error' => 'You need to enter a name.']);
        }

        if (!filter_var($post->get('link', ''), FILTER_VALIDATE_URL)) {
            return $this->json(['error' => 'You need to enter a valid link.']);
        }

        $advert
            ->setName($post->get('name'))
            ->setLink($post->get('link'))
            ->setSpecial((bool)$post->get('special', false));

        $em->persist($advert);
        $em->flush();

        if ($request->files->get('image')) {
            if ($advert->getImage()) {
                FileSystem::deleteFile(
                    'memes',
                    $advert->getToken() . substr($advert->getImage(), -4)
                );
            }

            try {
                $imagePath = FileSystem::handleUploadedFile(
                    $request->files->get('image'),
                    'memes',
                    $advert->getToken()
                );
            } catch (\Exception $e) {
                return $this->json(['error' => $e->getMessage()]);
            }

            $advert->setImage($imagePath);
            $em->persist($advert);
            $em->flush();
        }

        $auditService->add(
            new Action('advert-' . $action, $advert->getId()),
            new TableHistory(Advertisement::class, $advert->getId(), $post->all())
        );

        $em->flush();

        return $this->json(['success' => true]);
    }

    public function redirectAction(string $advertToken, EntityManagerInterface $em, UserInterface $user)
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
