<?php

namespace App\Repository\Entity;

use App\Entity\Customer\CommandeFormule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommandeFormule|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommandeFormule|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommandeFormule[]    findAll()
 * @method CommandeFormule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeFormuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandeFormule::class);
    }

    // /**
    //  * @return CommandeFormule[] Returns an array of CommandeFormule objects
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
    public function findOneBySomeField($value): ?CommandeFormule
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
