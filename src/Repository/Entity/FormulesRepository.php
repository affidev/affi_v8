<?php

namespace App\Repository\Entity;

use App\Entity\Module\Formules;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Formules|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formules|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formules[]    findAll()
 * @method Formules[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormulesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formules::class);
    }


    /*------------------------------query ----------------------------------*/


    public function queryFormule(): QueryBuilder
    {
        return $this->createQueryBuilder('f')
            -> andwhere('f.deleted = false')
            -> leftJoin('f.services', 's')
            -> addSelect('s')
            -> leftJoin('f.catformules', 'c')
            -> addSelect('c')
            -> leftJoin('c.declinaison', 'd')
            -> addSelect('d')
            -> leftJoin('f.articles', 'a')
            -> addSelect('a')
            -> leftJoin('a.pict', 'p')
            -> addSelect('p')
            -> leftJoin('a.categorie', 'ct')
            -> addSelect('ct')
            -> leftJoin('f.pictformule', 'pf')
            -> addSelect('pf')
            -> leftJoin('f.parution', 'pu')
            -> addSelect('pu');
    }


    //-------------------------------function //


    public function findByKey($key){
        $qb=$this->queryFormule();
        return $qb
            -> andWhere('f.keymodule =:key')
            -> setParameter('key', $key)
            -> orderBy('f.createAt', 'ASC')
            -> getQuery()
            -> getResult();
    }



    public function findlastformuleKey($key){
        $qb=$this->queryFormule();
        return $qb
            -> andWhere('f.keymodule =:key')
            -> setParameter('key', $key)
            -> orderBy('f.createAt', 'DESC')
            -> getQuery()
            -> getResult();
    }

    public function findformuleKey($key){
        $qb=$this->queryFormule();
        return $qb
            -> andWhere('f.keymodule =:key')
            -> setParameter('key', $key)
            -> orderBy('f.createAt', 'ASC')
            -> getQuery()
            -> getResult();
    }

    public function findFormulessByKeyWithOutId($key,$id){
        $qb=$this->queryFormule();
        return $qb
            -> andWhere('f.keymodule =:key')
            -> setParameter('key', $key)
            -> andwhere('f.id !=:id')
            -> setParameter('id', $id)
            -> orderBy('f.createAt', 'DESC')
            -> getQuery()
            -> getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findFormuleById($id){
        $qb=$this->queryFormule();
        return $qb
            -> andWhere('f.id =:id')
            -> setParameter('id', $id)
            -> getQuery()
            -> getOneOrNullResult();
    }
}
