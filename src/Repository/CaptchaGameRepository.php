<?php

namespace App\Repository;

use App\Entity\CaptchaGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CaptchaGame>
 *
 * @method CaptchaGame|null find($id, $lockMode = null, $lockVersion = null)
 * @method CaptchaGame|null findOneBy(array $criteria, array $orderBy = null)
 * @method CaptchaGame[]    findAll()
 * @method CaptchaGame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CaptchaGameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CaptchaGame::class);
    }

//    /**
//     * @return CaptchaGame[] Returns an array of CaptchaGame objects
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

//    public function findOneBySomeField($value): ?CaptchaGame
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
