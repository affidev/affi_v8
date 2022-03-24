<?php

namespace App\Repository\Entity;

use App\Entity\Admin\FacturesMarket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FacturesMarket|null find($id, $lockMode = null, $lockVersion = null)
 * @method FacturesMarket|null findOneBy(array $criteria, array $orderBy = null)
 * @method FacturesMarket[]    findAll()
 * @method FacturesMarket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacturesMarketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacturesMarket::class);
    }

    // /**
    //  * @return FacturesMarket[] Returns an array of FacturesMarket objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FacturesMarket
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
