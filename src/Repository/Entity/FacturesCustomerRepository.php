<?php

namespace App\Repository\Entity;

use App\Entity\Admin\FacturesCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FacturesCustomer|null find($id, $lockMode = null, $lockVersion = null)
 * @method FacturesCustomer|null findOneBy(array $criteria, array $orderBy = null)
 * @method FacturesCustomer[]    findAll()
 * @method FacturesCustomer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacturesCustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacturesCustomer::class);
    }


    public function findFactCustomer($id){

        return $this->createQueryBuilder('f')
            ->leftJoin('f.orders','o')
            ->addSelect('o')
            ->leftJoin('o.numclient','c')
            ->addSelect('c')
            ->leftJoin('c.idcustomer','cus')
            ->addSelect('cus')
            ->andwhere('cus.id = :id')
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
            ->leftJoin('o.numclient','c')
            ->addSelect('c')
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
            ->leftJoin('o.numclient','c')
            ->addSelect('w')
            ->andwhere('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
