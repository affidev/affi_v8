<?php

namespace App\Repository\Entity;

use App\Entity\Users\Contacts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contacts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contacts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contacts[]    findAll()
 * @method Contacts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contacts::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findBymail($email)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.emailCanonical = :val')
            ->setParameter('val', $email)
            ->leftJoin('c.useridentity', 'cu')
            ->addSelect('cu')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Contact
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
