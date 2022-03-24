<?php

namespace App\Repository\Entity;

use App\Entity\Sector\Gps;
use App\Entity\Websites\Website;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Website|null find($id, $lockMode = null, $lockVersion = null)
 * @method Website|null findOneBy(array $criteria, array $orderBy = null)
 * @method Website[]    findAll()
 * @method Website[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebsiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Website::class);
    }



    public function ifExistName($name): mixed
    {
        return $this->createQueryBuilder('w')
            ->andwhere('LOWER(w.namewebsite) LIKE :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $slug
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findWbBySlug($slug): mixed
    {
        return $this->createQueryBuilder('w')
            -> leftJoin('w.tabopendays', 'tod')
            -> addSelect('tod')
            -> leftJoin('w.locality','l')
            -> addSelect('l')
            -> leftjoin('w.websitepartner', 'pg')
            -> addSelect('pg')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.logo', 'lg')
            -> addSelect('lg')
            -> leftJoin('t.background', 'bk')
            -> addSelect('bk')
            -> leftJoin('t.tagueries', 'tag')
            -> addSelect('tag')
            -> leftJoin('w.listmodules', 'lm')
            -> addSelect('lm')
            -> andwhere('w.slug = :slug')
            -> setParameter('slug', $slug)
            -> getQuery()
            -> getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findWbByKey($key)
    {
        return $this->createQueryBuilder('w')
            -> leftJoin('w.spwsites', 'pw')
            -> addSelect('pw')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.logo', 'lg')
            -> addSelect('lg')
            -> andwhere('w.codesite =:key')
            -> setParameter('key', $key)
            -> getQuery()
            -> getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findWbPWById($id)
    {
        return $this->createQueryBuilder('w')
            -> leftJoin('w.spwsites', 'pw')
            -> addSelect('pw')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.logo', 'lg')
            -> addSelect('lg')
            -> andwhere('w.id =:id')
            -> setParameter('id', $id)
            -> getQuery()
            -> getOneOrNullResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findForCmdById($id){
        return $this->createQueryBuilder('w')
            -> leftJoin('w.listmodules', 'lm')
            -> addSelect('lm')
            -> leftJoin('w.wbcustomer', 'wc')
            -> addSelect('wc')
            -> leftJoin('w.spwsites', 'sp')
            -> addSelect('sp')
            -> andwhere('w.id = :id')
            -> setParameter('id', $id)
            -> getQuery()
            -> getOneOrNullResult();
    }


    public function findAllAndPwAdmin($req){
        return $this->createQueryBuilder('w')
            -> leftJoin('w.listmodules', 'lm')
            -> addSelect('lm')
            -> leftJoin('w.spwsites', 'sp')
            -> addSelect('sp')
            -> leftJoin('sp.disptachwebsite', 'dp')
            -> addSelect('dp')
            -> leftJoin('dp.customer', 'c')
            -> addSelect('c')
            -> leftJoin('c.services', 's')
            -> addSelect('s')
            -> andwhere('sp.role = :id')
            -> setParameter('id', $req)
            -> getQuery()
            -> getResult();
    }

    public function findForIndex(){
        return $this->createQueryBuilder('w')
            -> leftJoin('w.locality','l')
            -> addSelect('l')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.tagueries', 'tag')
            -> addSelect('tag')
            -> andwhere('w.active = :slug')
            -> setParameter('slug', true)
            -> getQuery()
            -> getResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findWbAndConversById($id){
        return $this->createQueryBuilder('w')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.logo', 'lg')
            -> addSelect('lg')
            -> leftJoin('t.tagueries', 'tag')
            -> addSelect('tag')
            -> leftJoin('w.module', 'm')
            -> addSelect('m')
            -> leftJoin('w.spwsites', 'pw')
            -> addSelect('pw')
            -> leftJoin('pw.disptachwebsite', 'dp')
            -> addSelect('dp')
            -> andwhere('w.id = :id')
            -> setParameter('id', $id)
            -> getQuery()
            -> getOneOrNullResult();
    }

    /**
     * @param $slug
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findWbAndConversBySlug($slug){
        return $this->createQueryBuilder('w')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.logo', 'lg')
            -> addSelect('lg')
            -> leftJoin('t.tagueries', 'tag')
            -> addSelect('tag')
            -> leftJoin('w.listmodules', 'lm')
            -> addSelect('lm')
            -> leftJoin('w.spwsites', 'pw')
            -> addSelect('pw')
            -> leftJoin('pw.disptachwebsite', 'dp')
            -> addSelect('dp')
            -> andwhere('w.slug = :slug')
            -> setParameter('slug', $slug)
            -> getQuery()
            -> getOneOrNullResult();
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function findWbBySlugAndMembers($slug): mixed
    {
        return $this->createQueryBuilder('w')
            -> leftJoin('w.tabopendays', 'tod')
            -> addSelect('tod')
            -> leftJoin('w.locality','l')
            -> addSelect('l')
            -> leftjoin('w.websitepartner', 'pg')
            -> addSelect('pg')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.logo', 'lg')
            -> addSelect('lg')
            -> leftJoin('t.background', 'bk')
            -> addSelect('bk')
            -> leftJoin('t.tagueries', 'tag')
            -> addSelect('tag')
            -> leftJoin('w.listmodules', 'm')
            -> addSelect('m')
            -> leftJoin('w.spwsites', 'pw')
            -> addSelect('pw')
            -> leftJoin('pw.disptachwebsite', 'dp')
            -> addSelect('dp')
            -> andwhere('w.slug = :slug')
            -> setParameter('slug', $slug)
            -> getQuery()
            -> getOneOrNullResult();
    }

    /**
     * @param $locate Gps
     * @return array|int|string
     */
    public function findWebsiteOfLocate(Gps $locate): array|int|string
    {
        //$qr= $this->QueryBulbeLocate($locate);
        $qr= $this->QueryByLocate($locate);
        return $qr
            -> getQuery()
            -> getArrayResult();
    }

    /**
     * @param $locate Gps
     * @return QueryBuilder
     */
    public function QueryBulbeLocate(Gps $locate): QueryBuilder
    {
        return $this->createQueryBuilder('w')
            -> leftJoin('w.locality','l')
            -> addSelect('l')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.tagueries', 'tag')
            -> addSelect('tag')
            -> leftJoin('t.logo', 'lg')
            -> addSelect('lg')
            -> andwhere('w.active = true')
            -> andwhere('l.latloc <= :latend')
            -> andWhere('l.latloc >= :latstart')
            -> andWhere('l.lonloc >= :lonstart')
            -> andWhere('l.lonloc <= :lonend')
            -> setParameter('latend', ($locate->getLatloc()+1))
            -> setParameter('latstart', ($locate->getLatloc()-1))
            -> setParameter('lonstart', ($locate->getLonloc()<0 ? $locate->getLonloc()+0.1:$locate->getLonloc()-0.1))
            -> setParameter('lonend', ($locate->getLonloc()<0 ? $locate->getLonloc()-0.1:$locate->getLonloc()+0.1))
            ;
    }

    /**
     * @param $locate Gps
     * @return QueryBuilder
     */
    public function QueryByLocate(Gps $locate): QueryBuilder
    {
        return $this->createQueryBuilder('w')
            -> leftJoin('w.locality','l')
            -> addSelect('l')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.tagueries', 'tag')
            -> addSelect('tag')
            -> leftJoin('t.logo', 'lg')
            -> addSelect('lg')
            -> leftJoin('t.background', 'bk')
            -> addSelect('bk')
            -> leftJoin('w.hits', 'h')
            -> addSelect('h')
            -> leftJoin('h.catag', 'ct')
            -> addSelect('ct')
            -> andwhere('w.active = true')
            -> andwhere('l.id = :id')
            -> setParameter('id', $locate->getId());
    }

    /**
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findWebsiteAdmin($id): mixed
    {

        return $this->createQueryBuilder('w')
            -> leftJoin('w.wbcustomer','wc')
            -> addSelect('wc')
            -> leftJoin('wc.orders','o')
            -> addSelect('o')
            -> leftJoin('w.locality','l')
            -> addSelect('l')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.logo', 'lg')
            -> addSelect('lg')
            -> leftJoin('t.background', 'bk')
            -> addSelect('bk')
            -> andwhere('w.id = :id')
            -> setParameter('id', $id)
            -> getQuery()
            -> getOneOrNullResult();
    }


    /* non testés ------------------------------------------------------------*/

    public function queryWebsiteObj(){
        return $this->createQueryBuilder('w')
            -> leftJoin('w.tabopendays', 'tod')
            -> addSelect('tod')
            -> leftJoin('w.locality','l')
            -> addSelect('l')
            -> leftjoin('w.websitepartner', 'pg')
            -> addSelect('pg')
            -> leftjoin('pg.partners', 'prt')
            -> addSelect('prt')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.logo', 'lg')
            -> addSelect('lg')
            -> leftJoin('t.background', 'bk')
            -> addSelect('bk')
            -> leftJoin('t.tagueries', 'tag')
            -> addSelect('tag')
            -> leftJoin('w.module', 'm')
            -> addSelect('m')
            -> leftJoin('m.contactation', 'ct')
            -> addSelect('ct')
            -> leftJoin('w.offres', 'off')
            -> addSelect('off')
            ;
    }

    public function queryAllRelation(): QueryBuilder
    {
        return $this->createQueryBuilder('w')
            -> leftJoin('w.tabopendays', 'tod')
            -> addSelect('tod')
            -> leftJoin('w.locality','l')
            -> addSelect('l')

            -> leftJoin('po.author', 'aut')
            -> addSelect('aut')
            -> leftJoin('po.tagueries', 'tg')
            -> addSelect('tg')
            -> leftJoin('po.media', 'md')
            -> addSelect('md')
            -> leftJoin('md.imagejpg', 'jpg')
            -> addSelect('jpg')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.logo', 'lg')
            -> addSelect('lg')
            -> leftJoin('t.background', 'bk')
            -> addSelect('bk')
            -> leftJoin('t.tagueries', 'tag')
            -> addSelect('tag')
            -> leftJoin('w.contactation', 'ct')
            -> addSelect('ct');
          ;
    }



    public function queryWebsiteById($id)
    {
        return $this->createQueryBuilder('w')
            -> where('w.active = true')
            -> andWhere('w.id =:id')
            -> setParameter('id', $id);
    }

    public function queryWebsiteBySlugAndDispatch($slug, $disptch)
    {
        return $this->createQueryBuilder('w')
            -> leftjoin('w.spwsites', 'spw')
            -> addSelect('spw')
            -> leftjoin('spw.disptachwebsite', 'd')
            -> addSelect('d')
            -> where('w.active = true')
            -> andWhere('w.slug = :slug')
            -> setParameter('slug', $slug)
            -> andwhere('d.id = :idpstch')
            -> setParameter('idpstch', $disptch);
    }





    public function findWebsiteOfLocateObj($lat, $lon){

        $qr= $this->QueryWebsiteLocate($lat, $lon);
        return $qr
            -> setMaxResults(6)
            -> getQuery()
            -> getArrayResult();

    }

    public function QueryWebsiteLocate($lat, $lon){
        return $this->createQueryBuilder('w')
            -> leftJoin('w.locality','l')
            -> addSelect('l')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.logo', 'lg')
            -> addSelect('lg')
            -> andWhere('l.latloc <= :latend')
            -> andWhere('l.latloc >= :latstart')
            -> andWhere('l.lonloc >= :lonstart')
            -> andWhere('l.lonloc <= :lonend')
            -> setParameter('latend', ($lat+1))
            -> setParameter('latstart', ($lat-1))
            -> setParameter('lonstart', ($lon-0.1))
            -> setParameter('lonend', ($lon+0.1));
    }

    private function findActiveQuery()
    {
        return $this->createQueryBuilder('w')
            ->where('w.active = true');
    }

    //--------------------fin query


    /**
     * @param $slug
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findWbQ1($slug, $id){
        $qb=$this->queryWebsiteBySlugAndDispatch($slug, $id);
        return $qb
            -> leftJoin('w.tabopendays', 'o')
            -> addSelect('o')
            -> leftJoin('w.locality','l')
            -> addSelect('l')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.logo', 'lg')
            -> addSelect('lg')
            -> leftJoin('t.tagueries', 'tag')
            -> addSelect('tag')
            -> leftJoin('w.posts', 'ps')
            -> addSelect('ps')
            -> getQuery()
            -> getOneOrNullResult();
    }


    /**
     * @param $slug
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findWbQ1Index($slug, $id){
        $qb=$this->queryWebsiteBySlugAndDispatch($slug, $id);
        return $qb
            -> leftJoin('w.tabopendays', 'o')
            -> addSelect('o')
            -> leftJoin('w.locality','l')
            -> addSelect('l')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.logo', 'lg')
            -> addSelect('lg')
            -> leftJoin('t.tagueries', 'tag')
            -> addSelect('tag')
            -> leftJoin('w.module', 'm')
            -> addSelect('m')
            -> leftJoin('w.posts', 'ps')
            -> addSelect('ps')
            -> leftJoin('ps.author', 'ath')
            -> addSelect('ath')
            -> leftJoin('ps.media', 'md')
            -> addSelect('md')
            -> leftJoin('md.imagejpg', 'img')
            -> addSelect('img')
            -> getQuery()
            -> getOneOrNullResult();
    }


    /**
     * @param $slug
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findWbQ2($slug, $id)
    {
        $qb = $this->queryWebsiteBySlugAndDispatch($slug, $id);
        return $qb
            ->leftJoin('w.template', 't')
            ->addSelect('t')
            ->leftJoin('t.logo', 'lg')
            ->addSelect('lg')
            ->leftJoin('w.module', 'm')
            ->addSelect('m')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $slug
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findWbQ3($slug, $id)
    {
        $qb = $this->queryWebsiteBySlugAndDispatch($slug, $id);
        return $qb
            ->leftJoin('w.template', 't')
            ->addSelect('t')
            ->leftJoin('t.logo', 'lg')
            ->addSelect('lg')
            ->leftJoin('t.background', 'bk')
            ->addSelect('bk')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $slug
     * @param $disptch
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findWebsiteBySlugAndIdDispatch($slug, $disptch)
    {
        $qb= $this->queryAllRelation();
        return $qb
            -> leftjoin('w.spwsites', 'spw')
            -> addSelect('spw')
            -> leftjoin('spw.disptachwebsite', 'd')
            -> addSelect('d')
            -> where('w.active = true')
            -> andWhere('w.slug = :slug')
            -> setParameter('slug', $slug)
            -> andwhere('d.id = :idpstch')
            -> setParameter('idpstch', $disptch)
            -> getQuery()
            -> getOneOrNullResult();
    }

    /**
     * @param $slug
     * @param $disptch
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findWebsiteBySlugAndIdDispatchForIndex($slug, $disptch)
    {
        $qb= $this->queryAllRelation();
        return $qb
            -> leftjoin('w.spwsites', 'spw')
            -> addSelect('spw')
            -> leftjoin('spw.disptachwebsite', 'd')
            -> addSelect('d')
            -> andWhere('w.active = true')
            -> andWhere('w.slug = :slug')
            -> setParameter('slug', $slug)
            -> andwhere('d.id = :idpstch')
            -> setParameter('idpstch', $disptch)
            -> getQuery()
            -> getOneOrNullResult();
    }

    public function findmember($slug){

        $qb= $this->queryAllRelation();
        $qb -> leftjoin('w.spwsites', 'spw')
            -> addSelect('spw')
            -> leftjoin('spw.disptachwebsite', 'd')
            -> addSelect('d')
            -> andwhere('w.slug = :slug')
            -> setParameter('slug', $slug)
            -> getQuery()
            -> getArrayResult();
    }



    /**
     * @param $slug
     * @return mixed
     */
    public function findAllBySlugObj($slug): mixed
    {
        $qb= $this->queryAllRelation();
        return $qb
            -> andwhere('w.slug = :slug')
            -> setParameter('slug', $slug)
            -> getQuery()
            -> getOneOrNullResult();
    }

    private function findWebsiteOutDispatch($id)
    {
        return $this->createQueryBuilder('w')
            -> leftjoin('w.spwsites', 'spw')
            -> addSelect('spw')
            -> leftjoin('spw.disptachwebsite', 'd')
            -> addSelect('d')
            -> where('d.id != :id')
            -> setParameter('id', $id);
    }

    /**
     * @param $slug
     * @param $disptch
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findWebsiteDispatchadmin($slug, $disptch)
    {
        $qb= $this->queryWebsiteBySlugAndDispatch($slug, $disptch);
        return $qb
            -> leftjoin('d.customer', 'c')
            -> leftJoin('w.locality','l')
            -> addSelect('l')
            -> addSelect('c')
           // -> andwhere('spw.role = admin')
           // -> setParameter('admin','admin')
            -> getQuery()
            -> getOneOrNullResult();
    }

    /**
     * @param $slug
     * @param $disptch
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findWebsiteAndOpenDaysBySlugAndIdDispatch($slug, $disptch)
    {
        $qb= $this->queryAllRelation();
        return $qb
            -> leftjoin('w.spwsites', 'spw')
            -> addSelect('spw')
            -> leftjoin('spw.disptachwebsite', 'd')
            -> addSelect('d')
            -> where('w.active = true')
            -> andWhere('w.slug = :slug')
            -> setParameter('slug', $slug)
            -> andwhere('d.id = :idpstch')
            -> setParameter('idpstch', $disptch)
            -> getQuery()
            -> getOneOrNullResult();
    }

    /**
     * @param $slug
     * @param $disptch
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findTokenSpwThanWebsiteForOneDispatch($slug, $disptch)
    {
        $qb= $this->queryWebsiteBySlugAndDispatch($slug, $disptch);
        return $qb
            -> andwhere('spw.role = :role')
            -> setParameter('role', 'superadmin')
            -> getQuery()
            -> getOneOrNullResult();
    }

    /**
     * @param $key
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findWebsiteCodesite($key)
    {
        return $this->createQueryBuilder('w')
            -> leftjoin('w.spwsites', 'spw')
            -> addSelect('spw')
            -> leftjoin('spw.disptachwebsite', 'd')
            -> addSelect('d')
            -> leftjoin('d.customer', 'c')
            -> addSelect('c')
            -> leftjoin('c.user', 'u')
            -> addSelect('u')
            -> where('w.codesite != :key')
            -> setParameter('key', $key)
            -> getQuery()
            -> getOneOrNullResult();
    }

    public function findLocate($locate, $id){
        return $this->findWebsiteOutDispatch($id)
            -> leftjoin('w.template', 'tp')
            -> addSelect('tp')
            -> leftJoin('tp.sector', 's')
            -> addSelect('s')
            -> leftJoin('s.adresse', 'a')
            -> addSelect('a')
            -> andWhere('a.lat <= :latend')
            -> andWhere('a.lat >= :latstart')
            -> andWhere('a.lon >= :lonstart')
            -> andWhere('a.lon <= :lonend')
            -> setParameter('latend', ($locate['lat']+1))
            -> setParameter('latstart', ($locate['lat']-1))
            -> setParameter('lonstart', ($locate['long']-0.1))
            -> setParameter('lonend', ($locate['long']+0.1))
            -> orderBy('w.create_at', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findwithGps($lat, $lon, $id){
        return $this->createQueryBuilder('w')
            -> leftJoin('w.locality','l')
            -> addSelect('l')
            -> leftJoin('w.template', 'tp')
            -> addSelect('tp')
            -> innerJoin('w.spwsites', 'spw')
            -> addSelect('spw')
            -> innerJoin('spw.disptachwebsite', 'd')
            -> addSelect('d')
            -> andwhere('w.active = false')
            -> andwhere('d.id != :id')
            -> setParameter('id', $id)
            -> andWhere('l.latloc <= :latend')
            -> andWhere('l.latloc >= :latstart')
            -> andWhere('l.lonloc >= :lonstart')
            -> andWhere('l.lonloc <= :lonend')
            -> setParameter('latend', $lat+1)
            -> setParameter('latstart', $lat-1)
            -> setParameter('lonstart', $lon-0.1)
            -> setParameter('lonend', $lon+0.1)
            -> orderBy('w.create_at', 'ASC')
            -> getQuery()
            -> getArrayResult()
            ;
    }

    public function findTemplate($id)
    {
        return $this->createQueryBuilder('w')
            -> leftJoin('w.locality','l')
            -> addSelect('l')
            -> leftjoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.logo', 'lg')
            -> addSelect('lg')
            -> leftJoin('t.tagueries', 'tag')
            -> addSelect('tag')
            -> andWhere('w.id = :val')
            -> setParameter('val', $id)
            -> getQuery()
            -> getArrayResult();
    }

    public function findPostByAppointWithPeriodForOneTeam($provider, $start, $end){
        return $this->queryWebsiteById($provider)
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('w.module', 'm')
            -> addSelect('m')
            -> leftJoin('m.postevent', 'e')
            -> addSelect('e')
            -> leftjoin('m.appointment', 'a')
            -> addSelect('a')
            -> andWhere('a.starttime <= :end')
            -> andWhere('a.endtime >= :start')  //TODO c'est faux ......
            -> setParameter('start', $start->format('Y-m-d 00:00:00'))
            -> setParameter('end', $end->format('Y-m-d 23:59:59'))
            -> orderBy('w.startPeriod', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
