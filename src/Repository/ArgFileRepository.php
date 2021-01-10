<?php

namespace App\Repository;

use App\Entity\ArgFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArgFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArgFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArgFile[]    findAll()
 * @method ArgFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArgFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArgFile::class);
    }

    // /**
    //  * @return ArgFile[] Returns an array of ArgFile objects
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
    public function findOneBySomeField($value): ?ArgFile
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
