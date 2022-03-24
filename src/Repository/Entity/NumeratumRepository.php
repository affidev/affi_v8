<?php

namespace App\Repository\Entity;

use App\Entity\Admin\Numeratum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Numeratum|null find($id, $lockMode = null, $lockVersion = null)
 * @method Numeratum|null findOneBy(array $criteria, array $orderBy = null)
 * @method Numeratum[]    findAll()
 * @method Numeratum[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NumeratumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Numeratum::class);
    }

    // /**
    //  * @return Numeratum[] Returns an array of Numeratum objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Numeratum
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
