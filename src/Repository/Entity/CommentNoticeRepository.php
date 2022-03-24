<?php

namespace App\Repository\Entity;

use App\Entity\Comments\CommentNotice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommentNotice|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentNotice|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentNotice[]    findAll()
 * @method CommentNotice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentNoticeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentNotice::class);
    }

    // /**
    //  * @return CommentNotice[] Returns an array of CommentNotice objects
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
    public function findOneBySomeField($value): ?CommentNotice
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
