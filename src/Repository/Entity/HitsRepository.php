<?php

namespace App\Repository\Entity;

use App\Entity\UserMap\Hits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Hits|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hits|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hits[]    findAll()
 * @method Hits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hits::class);
    }

    // /**
    //  * @return Hits[] Returns an array of Hits objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Hits
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
