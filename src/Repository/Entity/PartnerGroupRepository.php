<?php

namespace App\Repository\Entity;

use App\Entity\Partners\PartnerGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PartnerGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method PartnerGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method PartnerGroup[]    findAll()
 * @method PartnerGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartnerGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartnerGroup::class);
    }

    // /**
    //  * @return PartnerGroup[] Returns an array of PartnerGroup objects
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
    public function findOneBySomeField($value): ?PartnerGroup
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
