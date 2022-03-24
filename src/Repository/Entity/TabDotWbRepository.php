<?php

namespace App\Repository\Entity;

use App\Entity\DispatchSpace\TabDotWb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TabDotWb|null find($id, $lockMode = null, $lockVersion = null)
 * @method TabDotWb|null findOneBy(array $criteria, array $orderBy = null)
 * @method TabDotWb[]    findAll()
 * @method TabDotWb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TabDotWbRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TabDotWb::class);
    }

    // /**
    //  * @return TabDotWb[] Returns an array of TabDotWb objects
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
    public function findOneBySomeField($value): ?TabDotWb
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
