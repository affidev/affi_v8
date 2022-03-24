<?php

namespace App\Repository\Entity;

use App\Entity\Admin\OrderProducts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderProducts|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderProducts|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderProducts[]    findAll()
 * @method OrderProducts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderProducts::class);
    }


    public function findByOrder($order)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.order = :val')
            ->setParameter('val', $order)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?OrderProducts
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
