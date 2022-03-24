<?php

namespace App\Repository\Entity;

use App\Entity\Admin\ProductCategories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductCategories|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductCategories|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductCategories[]    findAll()
 * @method ProductCategories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductCategoriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductCategories::class);
    }

    // /**
    //  * @return AdminCategories[] Returns an array of AdminCategories objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AdminCategories
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
