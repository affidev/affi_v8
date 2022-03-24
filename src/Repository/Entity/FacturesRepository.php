<?php

namespace App\Repository\Entity;

use App\Entity\Admin\Factures;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Factures|null find($id, $lockMode = null, $lockVersion = null)
 * @method Factures|null findOneBy(array $criteria, array $orderBy = null)
 * @method Factures[]    findAll()
 * @method Factures[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacturesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Factures::class);
    }

    public function findFactWebsite($id){

        return $this->createQueryBuilder('f')
            ->leftJoin('f.orders','o')
            ->addSelect('o')
            ->leftJoin('o.wbcustomer','w')
            ->addSelect('w')
            ->andwhere('w.id = :id')
            ->setParameter('id', $id)
            ->orderBy('f.create_at', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * @throws NonUniqueResultException
     */
    public function findOneFacture($id){

        return $this->createQueryBuilder('f')
            ->leftJoin('f.orders','o')
            ->addSelect('o')
            ->leftJoin('o.wbcustomer','w')
            ->addSelect('w')
            ->andwhere('f.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByCommand($id){

        return $this->createQueryBuilder('f')
            ->leftJoin('f.orders','o')
            ->addSelect('o')
            ->leftJoin('o.wbcustomer','w')
            ->addSelect('w')
            ->andwhere('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
