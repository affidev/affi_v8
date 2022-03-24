<?php

namespace App\Repository\Entity;

use App\Entity\Module\Formules;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Formules|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formules|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formules[]    findAll()
 * @method Formules[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OldFormulesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formules::class);
    }

    public function findfmlQ3($id){
        $qb=$this->queryFormule();
        return $qb
            -> andWhere('f.website =:id')
            -> setParameter('id', $id)
            -> orderBy('f.createAt', 'DESC')
            -> getQuery()
            -> getResult();
    }

    public function findByKey($key){
        $qb=$this->queryFormule();
        return $qb
            -> andWhere('f.keymodule =:key')
            -> setParameter('key', $key)
            -> orderBy('f.createAt', 'DESC')
            -> getQuery()
            -> getResult();
    }



    public function findlastformuleKey($key){
        $qb=$this->queryKeyFormule();
        return $qb
            -> andWhere('f.keymodule =:key')
            -> setParameter('key', $key)
            -> orderBy('f.createAt', 'DESC')
            -> getQuery()
            -> getResult();
    }

    public function findformuleKey($key){
        $qb=$this->queryKeyFormule();
        return $qb
            -> andWhere('f.keymodule =:key')
            -> setParameter('key', $key)
            -> orderBy('f.createAt', 'ASC')
            -> getQuery()
            -> getResult();
    }

    public function findFormule($id){
        $qb=$this->queryFormule();
        return $qb
            -> andWhere('f.id =:id')
            -> setParameter('id', $id)
            -> getQuery()
            -> getOneOrNullResult();
    }

    public function findFormuleById($id){
        $qb=$this->queryFormulekey();
        return $qb
            -> andWhere('f.id =:id')
            -> setParameter('id', $id)
            -> getQuery()
            -> getOneOrNullResult();
    }


    /*------------------------------query ----------------------------------*/

    public function queryFormule()
    {
        return $this->createQueryBuilder('f')
            -> andwhere('f.deleted = false')
            -> leftJoin('f.articles', 'ar')
            -> addSelect('ar')
            -> leftJoin('ar.categorie', 'ct')
            -> addSelect('ct')
            -> leftJoin('ar.pict', 'p')
            -> addSelect('p')
            -> leftJoin('f.catformules', 'ctf')
            -> addSelect('ctf')
            -> leftJoin('ctf.declinaison', 'd')
            -> addSelect('d')
            -> leftJoin('f.services', 'sv')
            -> addSelect('sv')
            -> leftJoin('f.parution', 'pu')
            -> addSelect('pu');
    }

    public function queryFormulekey()
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

    public function queryKeyFormule()
    {
        return $this->createQueryBuilder('f')
            -> andwhere('f.deleted = false')
            -> leftJoin('f.articles', 'ar')
            -> addSelect('ar')
            -> leftJoin('ar.categorie', 'ct')
            -> addSelect('ct')
            -> leftJoin('ar.pict', 'p')
            -> addSelect('p')
            -> leftJoin('f.catformules', 'ctf')
            -> addSelect('ctf')
            -> leftJoin('ctf.declinaison', 'd')
            -> addSelect('d')
            -> leftJoin('f.services', 'sv')
            -> addSelect('sv')
            -> leftJoin('f.parution', 'pu')
            -> addSelect('pu');
    }

    // /**
    //  * @return Formules[] Returns an array of Formules objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Formules
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
