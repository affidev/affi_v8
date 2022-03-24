<?php

namespace App\Repository\Entity;

use App\Entity\Admin\NumClients;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NumClients|null find($id, $lockMode = null, $lockVersion = null)
 * @method NumClients|null findOneBy(array $criteria, array $orderBy = null)
 * @method NumClients[]    findAll()
 * @method NumClients[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NumClientsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NumClients::class);
    }

    // /**
    //  * @return NumClients[] Returns an array of NumClients objects
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
    public function findOneBySomeField($value): ?NumClients
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
