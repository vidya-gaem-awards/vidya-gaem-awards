<?php
declare(strict_types = 1);

namespace App\Repository;

use App\Entity\LootboxItem;
use App\Entity\LootboxTier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LootboxItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method LootboxItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method LootboxItem[]    findAll()
 * @method LootboxItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LootboxItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LootboxItem::class);
    }

    public function getTotalRelativeDropChance(): float
    {
        return (float) $this->createQueryBuilder('i')
            ->select('SUM(i.dropChance)')
            ->getQuery()
            ->getSingleScalarResult() ?? 0.0;
    }

    public function getTotalAbsoluteDropChance(): float
    {
        return (float) $this->createQueryBuilder('i')
            ->select('SUM(i.absoluteDropChance)')
            ->getQuery()
            ->getSingleScalarResult() ?? 0.0;
    }

    public function getItemCountInTier(LootboxTier $tier): int
    {
        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i)')
            ->where('i.tier = :tier')
            ->setParameter('tier', $tier)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
