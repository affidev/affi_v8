<?php

namespace App\Repository\Entity;

use App\Entity\UserMap\Taguery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Taguery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Taguery|null findOneBy(array $criteria, array $orderBy = null)
 * @method Taguery[]    findAll()
 * @method Taguery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagueryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Taguery::class);
    }

    // /**
    //  * @return Taguery[] Returns an array of Taguery objects
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
    public function findOneBySomeField($value): ?Taguery
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
