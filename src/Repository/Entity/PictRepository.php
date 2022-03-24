<?php

namespace App\Repository\Entity;

use App\Entity\Media\Pict;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pict|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pict|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pict[]    findAll()
 * @method Pict[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pict::class);
    }

    // /**
    //  * @return Pict[] Returns an array of Pict objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pict
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
