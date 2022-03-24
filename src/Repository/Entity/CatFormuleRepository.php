<?php

namespace App\Repository\Entity;

use App\Entity\Food\CatFormule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CatFormule|null find($id, $lockMode = null, $lockVersion = null)
 * @method CatFormule|null findOneBy(array $criteria, array $orderBy = null)
 * @method CatFormule[]    findAll()
 * @method CatFormule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatFormuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CatFormule::class);
    }

    // /**
    //  * @return CatFormule[] Returns an array of CatFormule objects
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
    public function findOneBySomeField($value): ?CatFormule
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
