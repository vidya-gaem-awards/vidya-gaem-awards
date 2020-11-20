<?php

namespace App\Repository;

use App\Entity\LootboxTier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LootboxTier|null find($id, $lockMode = null, $lockVersion = null)
 * @method LootboxTier|null findOneBy(array $criteria, array $orderBy = null)
 * @method LootboxTier[]    findAll()
 * @method LootboxTier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LootboxTierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LootboxTier::class);
    }

    public function getTotalRelativeDropChance(): float
    {
        return (float) $this->createQueryBuilder('t')
            ->select('SUM(t.drop_chance)')
            ->getQuery()
            ->getSingleScalarResult() ?? 0.0;
    }
}
