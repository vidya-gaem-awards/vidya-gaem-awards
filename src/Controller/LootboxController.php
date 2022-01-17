<?php
namespace App\Controller;

use App\Entity\Action;
use App\Entity\Award;
use App\Entity\LootboxItem;
use App\Entity\LootboxTier;
use App\Entity\TableHistory;
use App\Repository\LootboxItemRepository;
use App\Repository\LootboxTierRepository;
use App\Service\AuditService;
use App\Service\ConfigService;
use App\Service\FileService;
use App\Service\LootboxService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\News;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LootboxController extends AbstractController
{
    public function items(EntityManagerInterface $em)
    {
        $query = $em->createQueryBuilder();
        $items = $query
            ->select('i')
            ->from(LootboxItem::class, 'i')
            ->indexBy('i', 'i.id')
            ->getQuery()
            ->getResult();

        $tiers = $em->createQueryBuilder()
            ->select('t')
            ->from(LootboxTier::class, 't')
            ->orderBy('t.drop_chance', 'DESC')
            ->indexBy('t', 't.id')
            ->getQuery()
            ->getResult();

        return $this->render( 'lootboxItems.html.twig', [
            'items' => $items,
            'tiers' => $tiers
        ]);
    }

    public function itemPost(ConfigService $configService, Request $request, LootboxService $lootboxService, EntityManagerInterface $em, AuditService $auditService, FileService $fileService)
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
            $item = new LootboxItem();
        } else {
            $item = $em->getRepository(LootboxItem::class)->find($post->get('id'));
            if (!$item) {
                $this->json(['error' => 'Invalid item specified.']);
            }
        }

        if ($action === 'delete') {
            if (!$item->getUserItems()->isEmpty()) {
                return $this->json(['error' => 'This drop has already been acquired by somebody, and it cannot be deleted.']);
            }
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

        if (!preg_match('/^[0-9a-z-]+$/', $post->get('short-name'))) {
            return $this->json(['error' => 'ID must consist of lowercase letters and dashes only.']);
        }

        if (strlen(trim($post->get('name', ''))) === 0) {
            return $this->json(['error' => 'You need to enter a name.']);
        }

        if (!$post->getInt('tier')) {
            return $this->json(['error' => 'You need to select a tier.']);
        }

        $tier = $em->getRepository(LootboxTier::class)->find($post->getInt('tier'));
        if (!$tier) {
            return $this->json(['error' => 'Invalid tier selected.']);
        }

        $item
            ->setShortName($post->get('short-name'))
            ->setName($post->get('name'))
            ->setTier($tier)
            ->setCss($post->getBoolean('css'))
            ->setBuddie($post->getBoolean('buddie'))
            ->setMusic($post->getBoolean('music'))
            ->setCssContents($post->get('cssContents'));


        if ($post->getBoolean('drop-chance-override')) {
            if ($post->get('drop-chance-relative') !== '' && $post->get('drop-chance-absolute') !== '') {
                return $this->json(['error' => 'You can\'t have a relative and absolute drop chance set at the same time.']);
            }

            if ($post->get('drop-chance-relative') !== '') {
                $item->setDropChance($post->get('drop-chance-relative'));
            } else {
                $item->setDropChance(null);
            }

            if ($post->get('drop-chance-absolute') !== '' && $post->get('drop-chance-absolute') !== null) {
                $totalDropChance = $em->getRepository(LootboxItem::class)->getTotalAbsoluteDropChance();

                if ($totalDropChance - (float)$item->getAbsoluteDropChance() + (float)$post->get('drop-chance-absolute') / 100 > 1) {
                    return $this->json(['error' => 'Absolute drop chance is too high: the total of all items in the database cannot be over 100%']);
                }

                $item->setAbsoluteDropChance($post->get('drop-chance-absolute') / 100);
            } else {
                $item->setAbsoluteDropChance(null);
            }
        } else {
            $item->setDropChance(null);
            $item->setAbsoluteDropChance(null);
        }

        if (!$item->getId() && !$request->files->get('image')) {
            return $this->json(['error' => 'An image is required.']);
        }

        $em->persist($item);
        $em->flush();

        if ($request->files->get('image')) {
            try {
                $file = $fileService->handleUploadedFile(
                    $request->files->get('image'),
                    'LootboxItem.image',
                    'rewards',
                    $item->getID()
                );
            } catch (\Exception $e) {
                return $this->json(['error' => $e->getMessage()]);
            }

            if ($item->getImage()) {
                $fileService->deleteFile($item->getImage());
            }

            $item->setImage($file);
            $em->persist($item);
            $em->flush();
        }

        if ($request->files->get('musicFile')) {
            try {
                $file = $fileService->handleUploadedFile(
                    $request->files->get('musicFile'),
                    'LootboxItem.musicFile',
                    'music',
                    $item->getID()
                );
            } catch (\Exception $e) {
                return $this->json(['error' => $e->getMessage()]);
            }

            if ($item->getMusicFile()) {
                $fileService->deleteFile($item->getMusicFile());
            }

            $item->setMusicFile($file);
            $em->persist($item);
            $em->flush();
        }

        $auditService->add(
            new Action('item-' . $action, $item->getId()),
            new TableHistory(LootboxItem::class, $item->getId(), $post->all())
        );

        $em->flush();

        $lootboxService->updateCachedValues();

        return $this->json(['success' => true]);
    }

    public function itemCalculation(LootboxItemRepository $itemRepo, LootboxTierRepository $tierRepo, LootboxService $lootboxService, Request $request)
    {
        $post = $request->request;

        if ($post->getInt('id')) {
            $originalItem = $itemRepo->find($post->getInt('id'));
            if (!$originalItem) {
                return $this->json(['error' => 'Invalid item ID.'], 400);
            }
        } else {
            $originalItem = null;
        }

        $tier = $tierRepo->find($post->getInt('tier'));
        if (!$tier) {
            return $this->json(['error' => 'Invalid tier ID.'], 400);
        }

        $item = new LootboxItem();
        $item->setId(-1);

        if ($post->get('dropChanceOverride')) {
            $item->setTier($tier);

            if ($post->get('absoluteDropChance') !== '') {
                $item->setAbsoluteDropChance($post->get('absoluteDropChance') / 100);
            } elseif ($post->get('dropChance') !== '') {
                $item->setDropChance($post->get('dropChance'));
            }
        }

        $itemChances = $lootboxService->getItemArray($item, $originalItem);
        if (!isset($itemChances[$item->getId()])) {
            $absoluteDropChance = 0.0;
        } else {
            $absoluteDropChance = $itemChances[$item->getId()] / array_sum($itemChances);
        }

        return $this->json([
            'success' => true,
            'absoluteDropChance' => $absoluteDropChance
        ]);
    }

    public function tiers(EntityManagerInterface $em)
    {
        $query = $em->createQueryBuilder();
        $tiers = $query
            ->select('t')
            ->from(LootboxTier::class, 't')
            ->orderBy('t.drop_chance', 'DESC')
            ->indexBy('t', 't.id')
            ->getQuery()
            ->getResult();

        return $this->render('lootboxTiers.html.twig', [
            'tiers' => $tiers
        ]);
    }

    public function tierPost(ConfigService $configService, LootboxService $lootboxService, Request $request, EntityManagerInterface $em, AuditService $auditService)
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
            $tier = new LootboxTier();
        } else {
            $tier = $em->getRepository(LootboxTier::class)->find($post->get('id'));
            if (!$tier) {
                return $this->json(['error' => 'Invalid lootbox tier specified.']);
            }
        }

        if ($action === 'delete') {
            if (!$tier->getItems()->isEmpty()) {
                return $this->json(['error' => 'This tier can\'t be deleted while it still contains items.']);
            }

            $em->remove($tier);
            $auditService->add(
                new Action('lootbox-tier-delete', $tier->getId())
            );
            $em->flush();

            return $this->json(['success' => true]);
        }

        if (strlen(trim($post->get('name', ''))) === 0) {
            return $this->json(['error' => 'You need to enter a name.']);
        }

        if (strlen(trim($post->get('color', ''))) === 0) {
            return $this->json(['error' => 'You need to enter a color.']);
        }

        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $post->get('color'))) {
            return $this->json(['error' => 'Color is invalid: must be in the format #000000']);
        }

        if (!preg_match('/^(\d+.)?\d+$/', $post->get('dropChance'))) {
            return $this->json(['error' => 'You need to enter a drop chance.']);
        }

        $tier
            ->setName($post->get('name'))
            ->setDropChance($post->get('dropChance'))
            ->setColor($post->get('color'));

        $em->persist($tier);
        $em->flush();

        $auditService->add(
            new Action('lootbox-tier-' . $action, $tier->getId()),
            new TableHistory(LootboxTier::class, $tier->getId(), $post->all())
        );

        $em->flush();

        $lootboxService->updateCachedValues();

        return $this->json(['success' => true]);
    }

    public function tierCalculation(LootboxTierRepository $repo, LootboxService $lootboxService, Request $request)
    {
        $post = $request->request;

        if ($post->getInt('id')) {
            $tier = $repo->find($post->getInt('id'));
            if (!$tier) {
                return $this->json(['error' => 'Invalid tier ID.'], 400);
            }
        } else {
            $tier = null;
        }

        $newChance = (float) $post->get('dropChance');

        $absoluteDropChance = $lootboxService->getAbsoluteDropChanceFromRelativeChance($newChance, !$tier, $tier);

        return $this->json([
            'success' => true,
            'absoluteDropChance' => $absoluteDropChance
        ]);
    }

    public function settings(ConfigService $configService)
    {
        $settings = [
            'cost' => $configService->get('lootbox-cost', '')
        ];

        return $this->render('lootboxSettings.html.twig', [
            'settings' => $settings
        ]);
    }

    public function settingsSave(Request $request, ConfigService $configService, EntityManagerInterface $em, AuditService $auditService)
    {
        if ($configService->isReadOnly()) {
            $this->addFlash('error', 'The site is currently in read-only mode. No changes can be made.');
            return $this->redirectToRoute('lootboxSettings');
        }

        $post = $request->request;
        $configService->set('lootbox-cost', $post->getInt('cost'));
        $em->flush();

        $auditService->add(
            new Action('lootbox-settings-update')
        );

        $this->addFlash('success', 'Lootbox settings saved.');
        return $this->redirectToRoute('lootboxSettings');
    }
}
