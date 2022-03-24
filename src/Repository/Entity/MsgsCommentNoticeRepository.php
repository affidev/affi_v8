<?php

namespace App\Repository\Entity;

use App\Entity\Comments\MsgsCommentNotice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MsgsCommentNotice|null find($id, $lockMode = null, $lockVersion = null)
 * @method MsgsCommentNotice|null findOneBy(array $criteria, array $orderBy = null)
 * @method MsgsCommentNotice[]    findAll()
 * @method MsgsCommentNotice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MsgsCommentNoticeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MsgsCommentNotice::class);
    }

    // /**
    //  * @return MsgsCommentNotice[] Returns an array of MsgsCommentNotice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MsgsCommentNotice
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
