<?php

namespace App\Repository\Entity;

use App\Entity\Websites\Tbsuggest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tbsuggest|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tbsuggest|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tbsuggest[]    findAll()
 * @method Tbsuggest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TbsuggestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tbsuggest::class);
    }

    // /**
    //  * @return Tbsuggest[] Returns an array of Tbsuggest objects
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
    public function findOneBySomeField($value): ?Tbsuggest
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
