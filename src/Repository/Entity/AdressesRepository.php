<?php

namespace App\Repository\Entity;

use App\Entity\Sector\Adresses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\String\s;

/**
 * @method Adresses|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adresses|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adresses[]    findAll()
 * @method Adresses[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdressesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adresses::class);
    }


    public function findAllAdresses($sector)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.sector','s')
            ->addSelect('s')
            ->andWhere('s.id = :val')
            ->setParameter('val', $sector)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Adresses
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
