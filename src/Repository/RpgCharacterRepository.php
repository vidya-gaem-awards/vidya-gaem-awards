<?php

namespace App\Repository;

use App\Entity\RpgCharacter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RpgCharacter>
 *
 * @method RpgCharacter|null find($id, $lockMode = null, $lockVersion = null)
 * @method RpgCharacter|null findOneBy(array $criteria, array $orderBy = null)
 * @method RpgCharacter[]    findAll()
 * @method RpgCharacter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RpgCharacterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RpgCharacter::class);
    }

//    /**
//     * @return RpgCharacter[] Returns an array of RpgCharacter objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RpgCharacter
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
