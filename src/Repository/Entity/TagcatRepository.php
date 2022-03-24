<?php

namespace App\Repository\Entity;

use App\Entity\UserMap\Tagcat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tagcat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tagcat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tagcat[]    findAll()
 * @method Tagcat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagcatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tagcat::class);
    }

    // /**
    //  * @return Tagcat[] Returns an array of Tagcat objects
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
    public function findOneBySomeField($value): ?Tagcat
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
