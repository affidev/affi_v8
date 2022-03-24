<?php

namespace App\Repository\Entity;

use App\Entity\Admin\Wbcustomers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Wbcustomers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wbcustomers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wbcustomers[]    findAll()
 * @method Wbcustomers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WbcustomersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wbcustomers::class);
    }

    // /**
    //  * @return Wbcustomers[] Returns an array of Wbcustomers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Wbcustomers
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
