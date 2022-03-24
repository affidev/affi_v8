<?php

namespace App\Repository\Entity;

use App\Entity\UserMap\Catching;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Catching|null find($id, $lockMode = null, $lockVersion = null)
 * @method Catching|null findOneBy(array $criteria, array $orderBy = null)
 * @method Catching[]    findAll()
 * @method Catching[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatchingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Catching::class);
    }

    // /**
    //  * @return Catching[] Returns an array of Catching objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Catching
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
