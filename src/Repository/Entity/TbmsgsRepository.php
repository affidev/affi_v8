<?php

namespace App\Repository\Entity;

use App\Entity\LogMessages\Tbmsgs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tbmsgs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tbmsgs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tbmsgs[]    findAll()
 * @method Tbmsgs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TbmsgsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tbmsgs::class);
    }


    public function findnoread($website)
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.idmessage', 'm' )
            ->addSelect('m')
            ->leftJoin('m.msgwebsite', 'ms' )
            ->addSelect('ms')
            ->leftJoin('ms.websitedest', 'wb' )
            ->addSelect('wb')
            ->andWhere('wb.id = :val')
            ->setParameter('val', $website)
            ->andWhere('t.isRead = false')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findMessnoreadToList($website)
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.idmessage', 'm' )
            ->addSelect('m')
            ->leftJoin('m.msgwebsite', 'ms' )
            ->addSelect('ms')
            ->leftJoin('ms.websitedest', 'wb' )
            ->addSelect('wb')
            ->andWhere('wb.id = :val')
            ->setParameter('val', $website)
            ->andWhere('t.isRead = false')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findPrivateMessnoreadToList($id)
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.idmessage', 'm' )
            ->addSelect('m')
            ->leftJoin('t.dispatch', 'd' )
            ->addSelect('d')
            ->andWhere('t.id = :val')
            ->setParameter('val', $id)
            ->andWhere('t.isRead = false')
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?Tbmsgs
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
