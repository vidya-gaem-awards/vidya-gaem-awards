<?php

namespace App\Repository;

use App\Entity\CaptchaResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CaptchaResponse>
 *
 * @method CaptchaResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method CaptchaResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method CaptchaResponse[]    findAll()
 * @method CaptchaResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CaptchaResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CaptchaResponse::class);
    }

//    /**
//     * @return CaptchaResponse[] Returns an array of CaptchaResponse objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CaptchaResponse
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
