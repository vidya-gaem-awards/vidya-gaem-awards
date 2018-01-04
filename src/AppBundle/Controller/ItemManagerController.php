<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Action;
use AppBundle\Entity\Award;
use AppBundle\Entity\InventoryItem;
use AppBundle\Entity\TableHistory;
use AppBundle\Service\AuditService;
use AppBundle\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\News;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use VGA\FileSystem;

class ItemManagerController extends Controller
{
    public function indexAction(EntityManagerInterface $em)
    {
        $query = $em->createQueryBuilder();
        $items = $query
            ->select('i')
            ->from(InventoryItem::class, 'i')
            ->indexBy('i', 'i.id')
            ->getQuery()
            ->getResult();

        return $this->render('itemManager.html.twig', [
            'items' => $items
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
            $item = new InventoryItem();
        } else {
            $item = $em->getRepository(InventoryItem::class)->find($post->get('id'));
            if (!$item) {
                $this->json(['error' => 'Invalid item specified.']);
            }
        }

        if ($action === 'delete') {
            $em->remove($item);
            $auditService->add(
                new Action('item-delete', $item->getId())
            );
            $em->flush();

            return $this->json(['success' => true]);
        }

        if (strlen(trim($post->get('short-name', ''))) === 0) {
            return $this->json(['error' => 'You need to enter an ID.']);
        }

        if (!preg_match('/^[a-z-]+$/', $post->get('short-name'))) {
            return $this->json(['error' => 'ID must consist of lowercase letters and dashes only.']);
        }

        if (strlen(trim($post->get('name', ''))) === 0) {
            return $this->json(['error' => 'You need to enter a name.']);
        }

        if (!preg_match('/^\d+$/', $post->get('rarity'))) {
            return $this->json(['error' => 'You need to enter a rarity.']);
        }

        if ($post->getInt('rarity') > 10) {
            return $this->json(['error' => 'Rarity cannot be higher than 10.']);
        }

        $item
            ->setShortName($post->get('short-name'))
            ->setName($post->get('name'))
            ->setRarity($post->get('rarity'))
            ->setType($post->get('type'));

        $em->persist($item);
        $em->flush();

        if ($request->files->get('image')) {
            if ($item->getImage()) {
                FileSystem::deleteFile(
                    'rewards',
                    $item->getID() . substr($item->getImage(), -4)
                );
            }

            try {
                $imagePath = FileSystem::handleUploadedFile(
                    $request->files->get('image'),
                    'rewards',
                    $item->getID()
                );
            } catch (\Exception $e) {
                return $this->json(['error' => $e->getMessage()]);
            }

            $item->setImage($imagePath);
            $em->persist($item);
            $em->flush();
        }

        $auditService->add(
            new Action('item-' . $action, $item->getId()),
            new TableHistory(InventoryItem::class, $item->getId(), $post->all())
        );

        $em->flush();

        return $this->json(['success' => true]);
    }
}
