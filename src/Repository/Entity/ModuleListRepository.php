<?php

namespace App\Repository\Entity;

use App\Entity\Module\ModuleList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ModuleList|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModuleList|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModuleList[]    findAll()
 * @method ModuleList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModuleList::class);
    }


    public function findwebsiteBykeymodule($key)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.keymodule = :val')
            ->setParameter('val', $key)
            -> leftJoin('m.module', 'md')
            -> addSelect('md')
            -> leftJoin('md.website', 'w')
            -> addSelect('w')
            -> leftJoin('w.locality', 'locwb')
            -> addSelect('locwb')
            -> leftJoin('w.template', 'tp')
            -> addSelect('tp')
            -> leftJoin('tp.logo', 'lg')
            -> addSelect('lg')
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $key
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function findTheWebsiteBykeymodule($key)
    {
        return $this->createQueryBuilder('m')
            -> andWhere('m.keymodule = :val')
            -> setParameter('val', $key)
            -> leftJoin('m.module', 'md')
            -> addSelect('md')
            -> leftJoin('md.website', 'w')
            -> addSelect('w')
            -> leftJoin('w.locality', 'locwb')
            -> addSelect('locwb')
            -> leftJoin('w.template', 'tp')
            -> addSelect('tp')
            -> leftJoin('tp.logo', 'lg')
            -> addSelect('lg')
            -> getQuery()
            -> getOneOrNullResult()
            ;
    }

    public function findListforWebsite($id){

        return $this->createQueryBuilder('m')
            -> andWhere('m.module = :val')
            -> setParameter('val', $id)
            -> getQuery()
            -> getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?ModuleList
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
