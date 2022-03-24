<?php

namespace App\Repository\Entity;

use App\Entity\DispatchSpace\Spwsite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Spwsite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Spwsite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Spwsite[]    findAll()
 * @method Spwsite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpwsiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Spwsite::class);
    }


    public function findWebsitesOfDispatch($id): array|int|string
    {
        return $this->createQueryBuilder('s')
            -> leftJoin('s.disptachwebsite','d')
            -> addSelect('d')
            -> leftJoin('s.website','w')
            -> addSelect('w')
            -> leftJoin('w.locality', 'locwb')
            -> addSelect('locwb')
            -> leftJoin('w.template', 'tp')
            -> addSelect('tp')
            -> leftJoin('tp.logo', 'lg')
            -> addSelect('lg')
            -> andWhere('d.id = :val')
            -> setParameter('val', $id)
            -> getQuery()
            -> getArrayResult()
            ;
    }

    /**
     * @param $idwebsite
     * @param $id
     * @return mixed
     */
    public function findPwByWbForIdDipsatch($idwebsite, $id): mixed
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.disptachwebsite','d')
            ->addSelect('d')
            ->leftJoin('s.website','w')
            ->addSelect('w')
            ->leftJoin('w.listmodules','lm')
            ->addSelect('lm')
            ->leftJoin('w.locality','l')
            ->addSelect('l')
            ->leftJoin('w.tabopendays','op')
            ->addSelect('op')
            ->leftJoin('w.template', 't')
            ->addSelect('t')
            ->leftJoin('t.sector', 'sec')
            ->addSelect('sec')
            ->leftJoin('sec.adresse', 'adr')
            ->addSelect('adr')
            ->leftJoin('t.logo', 'lg')
            ->addSelect('lg')
            ->andWhere('s.website = :val2')
            ->setParameter('val2', $idwebsite)
            ->andWhere('d.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findPwByWbForSuperAdmin($idwebsite): mixed
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.disptachwebsite','d')
            ->addSelect('d')
            ->leftJoin('s.website','w')
            ->addSelect('w')
            ->leftJoin('w.listmodules','lm')
            ->addSelect('lm')
            ->leftJoin('w.locality','l')
            ->addSelect('l')
            ->leftJoin('w.tabopendays','op')
            ->addSelect('op')
            ->leftJoin('w.template', 't')
            ->addSelect('t')
            ->leftJoin('t.sector', 'sec')
            ->addSelect('sec')
            ->leftJoin('sec.adresse', 'adr')
            ->addSelect('adr')
            ->leftJoin('t.logo', 'lg')
            ->addSelect('lg')
            ->andWhere('s.website = :val2')
            ->setParameter('val2', $idwebsite)
            ->andWhere('d.id = :val')
            ->setParameter('val', 1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $key
     * @param $id
     * @return mixed
     */
    public function findPwByWbKeyForIdDipsatch($key, $id): mixed
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.disptachwebsite','d')
            ->addSelect('d')
            ->leftJoin('s.website','w')
            ->addSelect('w')
            ->leftJoin('w.listmodules','lm')
            ->addSelect('lm')
            ->leftJoin('w.locality','l')
            ->addSelect('l')
            ->leftJoin('w.tabopendays','op')
            ->addSelect('op')
            ->leftJoin('w.template', 't')
            ->addSelect('t')
            ->leftJoin('t.sector', 'sec')
            ->addSelect('sec')
            ->leftJoin('sec.adresse', 'adr')
            ->addSelect('adr')
            ->leftJoin('t.logo', 'lg')
            ->addSelect('lg')
            ->andWhere('w.codesite = :val2')
            ->setParameter('val2', $key)
            ->andWhere('d.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function  findadminwebsite($wb){
        return $this->createQueryBuilder('s')
            ->leftJoin('s.disptachwebsite','d')
            ->addSelect('d')
            ->leftJoin('d.customer','c')
            ->addSelect('c')
            ->leftJoin('c.profil','pf')
            ->addSelect('pf')
            ->leftJoin('s.website','w')
            ->addSelect('w')
            ->andWhere('w.id = :id')
            ->setParameter('id', $wb)
            ->andWhere('s.role LIKE :term')
            ->setParameter('term', 'superadmin')
            ->andWhere("s.disptachwebsite != '1' ")
            ->getQuery()
            ->getResult();
    }

    public function findPwByWbSlugForIdDipsatch($slug, $id)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.disptachwebsite','d')
            ->addSelect('d')
            ->leftJoin('s.website','w')
            ->addSelect('w')
            ->leftJoin('w.spwsites','pws')
            ->addSelect('pws')
            ->leftJoin('w.locality','l')
            ->addSelect('l')
            ->leftJoin('w.listmodules','lm')
            ->addSelect('lm')
            ->leftJoin('w.template', 't')
            ->addSelect('t')
            ->leftJoin('t.logo', 'lg')
            ->addSelect('lg')
            ->leftJoin('t.tagueries', 'tag')
            ->addSelect('tag')
            ->andWhere('w.slug = :slug')
            ->setParameter('slug', $slug)
            ->andWhere('d.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $slug
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findOrNullSuperadminForWebsiteByOneDispatch($slug, $id)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.disptachwebsite','d')
            ->addSelect('d')
            ->leftJoin('s.website','w')
            ->addSelect('w')
            ->leftJoin('w.template', 't')
            ->addSelect('t')
            ->leftJoin('t.logo', 'lg')
            ->addSelect('lg')
            ->andWhere('w.slug = :slug')
            ->setParameter('slug', $slug)
            ->andWhere('d.id = :id')
            ->setParameter('id', $id)
            ->andWhere('s.role LIKE :srole')
            ->setParameter('srole', 'superadmin')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findByAdmin($id)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.disptachwebsite = :val')
            ->setParameter('val', $id)
            ->andWhere('s.role LIKE :srole')
            ->setParameter('srole', 'admin')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findwithAll($id)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('d.disatchwebsite = :val')
            ->setParameter('val', $id)
            ->addSelect('s')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findWebsitesForOneDispatch($id){
        return $this->createQueryBuilder('s')
            ->leftJoin('s.website','w')
            ->addSelect('w')
            ->leftJoin('s.disptachwebsite','d')
            ->addSelect('d')
            ->andWhere('d.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findWebsitesForOneDispatchAndAllNotices($id){
        return $this->createQueryBuilder('s')
            -> leftJoin('s.website','w')
            -> addSelect('w')
            -> leftJoin('w.locality', 'locwb')
            -> addSelect('locwb')
            -> leftJoin('w.template', 'tp')
            -> addSelect('tp')
            -> leftJoin('tp.logo', 'lg')
            -> addSelect('lg')
            -> leftJoin('w.posts', 'p')
            -> addSelect('p')
            -> andwhere('p.deleted = false')
            -> leftJoin('p.tagueries', 'ptg')
            -> addSelect('ptg')
            -> leftJoin('p.author', 'paut')
            -> addSelect('paut')
            -> leftJoin('p.media', 'pmd')
            -> addSelect('pmd')
            -> leftJoin('pmd.imagejpg', 'jpg')
            -> addSelect('jpg')
            -> leftJoin('w.offres', 'o')
            -> addSelect('o')
            -> andwhere('o.deleted = false')
            -> leftJoin('o.product', 'op')
            -> addSelect('op')
            -> leftJoin('o.media', 'om')
            -> addSelect('om')
            -> leftJoin('op.tagueries', 'otg')
            -> addSelect('otg')
            -> leftJoin('o.author', 'oath')
            -> addSelect('oath')
            -> leftJoin('om.imagejpg', 'oimg')
            -> addSelect('oimg')
            -> leftJoin('s.disptachwebsite','d')
            -> addSelect('d')
            -> andWhere('d.id = :val')
            -> setParameter('val', $id)
            -> getQuery()
            -> getArrayResult()
            ;
    }

    public function findWebsitesSuperadminForOneDispatch($id){
        return $this->createQueryBuilder('s')
            ->leftJoin('s.website','w')
            ->addSelect('w')
            ->leftJoin('s.disptachwebsite','d')
            ->addSelect('d')
            ->andWhere('d.id = :val')
            ->setParameter('val', $id)
            ->andWhere('s.role = :admin')
            ->setParameter('admin', 'superadmin')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $website
     * @param $dispatch
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getOneSpwWithWebsiteThanDispath($website, $dispatch){
        return $this->createQueryBuilder('s')
            ->andWhere('s.disptachwebsite = :val')
            ->setParameter('val', $dispatch)
            ->andWhere('s.website = :web')
            ->setParameter('web', $website)
            ->leftJoin('s.disptachwebsite','d')
            ->addSelect('s')
            ->leftJoin('s.website','w')
            ->addSelect('w')
            -> leftJoin('w.locality','l')
            -> addSelect('l')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param $slug
     * @param $dispatch
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getOneSpwWithWebsiteThanDispathBySlug($slug, $dispatch){
        return $this->createQueryBuilder('s')
            ->leftJoin('s.disptachwebsite','d')
            ->addSelect('d')
            ->leftJoin('d.customer','c')
            ->addSelect('c')
            ->leftJoin('s.website','w')
            ->addSelect('w')
            ->leftJoin('w.module','m')
            ->addSelect('m')
            ->andWhere('s.disptachwebsite = :val')
            ->setParameter('val', $dispatch)
            ->andWhere('w.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?Spwsite
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
