<?php

namespace App\Repository;

use App\Entity\ArgConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArgConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArgConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArgConfig[]    findAll()
 * @method ArgConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArgConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArgConfig::class);
    }

    // /**
    //  * @return ArgConfig[] Returns an array of ArgConfig objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ArgConfig
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
