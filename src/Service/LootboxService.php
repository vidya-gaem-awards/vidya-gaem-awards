<?php
declare(strict_types = 1);

namespace App\Service;

use App\Entity\DropChance;
use App\Entity\LootboxItem;
use App\Entity\LootboxTier;
use App\Repository\LootboxItemRepository;
use App\Repository\LootboxTierRepository;
use Doctrine\ORM\EntityManagerInterface;

class LootboxService {
    private EntityManagerInterface $em;
    private LootboxItemRepository $itemRepo;
    private LootboxTierRepository $tierRepo;

    public function __construct(EntityManagerInterface $em, LootboxItemRepository $itemRepo, LootboxTierRepository $tierRepo)
    {
        $this->em = $em;
        $this->itemRepo = $itemRepo;
        $this->tierRepo = $tierRepo;
    }

    public function getItemArray(?LootboxItem $itemToAdd = null, ?LootboxItem $itemToRemove = null)
    {
        /** @var LootboxTier[] $tiers */
        $tiers = $this->tierRepo->createQueryBuilder('t', 't.id')->getQuery()->getResult();

        /** @var LootboxItem[] $items */
        $items = $this->itemRepo->createQueryBuilder('i', 'i.id')->getQuery()->getResult();

        if ($itemToAdd) {
            $items[] = $itemToAdd;
        }

        $absolute_items = [];
        $relative_items = [];
        $standard_items_by_tier = [];

        foreach ($items as $item) {
            if ($itemToRemove === $item) {
                continue;
            }

            if ($item->getAbsoluteDropChance() !== null) {
                $absolute_items[] = $item;
                continue;
            }

            if ($item->getDropChance() !== null) {
                $relative_items[] = $item;
                continue;
            }

            $standard_items_by_tier[$item->getTier()->getId()][] = $item;
        }

        $full_item_array = [];

        foreach ($relative_items as $item) {
            $full_item_array[$item->getId()] = (float) $item->getDropChance();
        }

        foreach ($standard_items_by_tier as $tier_id => $items) {
            foreach ($items as $item) {
                $full_item_array[$item->getId()] = (float) $tiers[$tier_id]->getDropChance() / count($items);
            }
        }

        $full_item_array = array_filter($full_item_array, fn ($dropChance) => $dropChance > 0);

        $absolute_total = array_sum(array_map(fn (LootboxItem $item) => $item->getAbsoluteDropChance(), $absolute_items));
        $relative_total = array_sum($full_item_array) / (1 - $absolute_total);

        foreach ($absolute_items as $item) {
            $full_item_array[$item->getId()] = (float) $item->getAbsoluteDropChance() * $relative_total;
        }

        return $full_item_array;
    }

    public function updateCachedValues(): void
    {
        /** @var LootboxItem[] $items */
        $items = $this->itemRepo->createQueryBuilder('i', 'i.id')->getQuery()->getResult();

        $itemArray = $this->getItemArray();

        $count = 0.0;

        foreach ($items as $itemID => $item) {
            if (!isset($itemArray[$itemID])) {
                $item->setCachedDropValueStart(null);
                $item->setCachedDropValueEnd(null);
            } else {
                $item->setCachedDropValueStart((string) $count);
                $count += $itemArray[$itemID];
                $item->setCachedDropValueEnd((string) ($count - 0.00001));
            }
            $this->em->persist($item);
        }

        $this->em->flush();
    }

    public function getRandomItem(): LootboxItem
    {
        $maxDropValue = $this->itemRepo->createQueryBuilder('i')
            ->select('MAX(i.cachedDropValueEnd)')
            ->getQuery()
            ->getSingleScalarResult();

        $maxDropValue = (int) ($maxDropValue * 100000);

        $randomNumber = random_int(0, $maxDropValue);

        return $this->itemRepo->createQueryBuilder('i')
            ->where(':value BETWEEN i.cachedDropValueStart AND i.cachedDropValueEnd')
            ->setParameter('value', $randomNumber / 100000)
            ->getQuery()
            ->getSingleResult();
    }

    public function getTotalRelativeDropChance(): float
    {
        return $this->itemRepo->getTotalRelativeDropChance() + $this->tierRepo->getTotalRelativeDropChance();
    }

    public function getTotalAbsoluteDropChance(): float
    {
        return $this->itemRepo->getTotalAbsoluteDropChance();
    }

    /**
     * Gets the absolute drop chance (between 0.00 and 1.00) for a given relative drop chance.
     *
     * @param float $dropChance The relative drop chance of the item or tier.
     * @param bool $new True if this is for an item or tier that hasn't yet been saved to the database.
     * @param DropChance|null $originalObject Should only be provided if editing an existing item or tier and the
     *                                        changes haven't yet been saved to the database.
     *
     * @return float
     */
    public function getAbsoluteDropChanceFromRelativeChance(float $dropChance, bool $new, ?DropChance $originalObject = null): float
    {
        $total = $this->getTotalRelativeDropChance();

        if ($new) {
            $total += $dropChance;
        } elseif ($originalObject) {
            $total = $total - $originalObject->getDropChance() + $dropChance;
        }

        if ($total === 0.0) {
            return 0.0;
        }

        $totalAbsoluteDropChance = $this->getTotalAbsoluteDropChance();
        if ($originalObject) {
            $totalAbsoluteDropChance -= $originalObject->getAbsoluteDropChance();
        }

        return $dropChance / $total * max(1 - $totalAbsoluteDropChance, 0);
    }
}
