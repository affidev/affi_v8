<?php

namespace App\Repository\Entity;

use App\Entity\HyperCom\TagAnalytic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TagAnalytic|null find($id, $lockMode = null, $lockVersion = null)
 * @method TagAnalytic|null findOneBy(array $criteria, array $orderBy = null)
 * @method TagAnalytic[]    findAll()
 * @method TagAnalytic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagAnalyticRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TagAnalytic::class);
    }

    // /**
    //  * @return TagAnalytic[] Returns an array of TagAnalytic objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TagAnalytic
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
