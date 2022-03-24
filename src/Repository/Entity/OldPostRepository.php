<?php

namespace App\Repository\Entity;

use App\Entity\Posts\Post;
use DateTime;
use DateInterval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OldPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }


    /**
     * @throws NonUniqueResultException
     */
    public function findOneOffreAndMsg($id){
        return $this->createQueryBuilder('p')
            -> leftJoin('p.tbmessages', 'tb')
            -> addSelect('tb')
            -> leftJoin('tb.idmessage', 'msgp')
            -> addSelect('msgp')
            -> leftJoin('msgp.msgs', 'msgs')
            -> addSelect('msgs')
            -> leftJoin('msgs.tabreaders', 'tr')
            -> addSelect('tr')
            -> leftJoin('tr.tabnotifs', 'nt')
            -> addSelect('nt')
            -> leftJoin('p.author', 'aut')
            -> addSelect('aut')
            -> leftJoin('p.media', 'md')
            -> addSelect('md')
            -> leftJoin('md.imagejpg', 'jpg')
            -> addSelect('jpg')
            -> leftJoin('p.htmlcontent', 'ht')
            -> addSelect('ht')
            -> andWhere('p.id =:id')
            -> setParameter('id', $id)
            -> getQuery()
            -> getOneorNullResult();
    }

    public function findAllAndWebsite(){
        return $this->createQueryBuilder('p')
            -> leftJoin('p.website', 'w')
            -> addSelect('w')
            -> leftJoin('w.locality', 'l')
            -> addSelect('l')
            -> getQuery()
            -> getResult();
    }

    public function ListpostByKey($key){
        return $this->createQueryBuilder('p')
            -> andwhere('p.deleted = false')
            -> leftJoin('p.tagueries', 'tg')
            -> addSelect('tg')
            -> leftJoin('p.author', 'aut')
            -> addSelect('aut')
            -> leftJoin('p.media', 'md')
            -> addSelect('md')
            -> leftJoin('md.imagejpg', 'jpg')
            -> addSelect('jpg')
            -> andWhere('p.keymodule =:key')
            -> setParameter('key', $key)
            -> orderBy('p.createAt', 'DESC')
            -> getQuery()
            -> getResult();
    }

    public function findPstKey($key){
        return $this->createQueryBuilder('p')
            -> leftJoin('p.tagueries', 'tg')
            -> addSelect('tg')
            -> leftJoin('p.author', 'aut')
            -> addSelect('aut')
            -> leftJoin('p.media', 'md')
            -> addSelect('md')
            -> leftJoin('md.imagejpg', 'jpg')
            -> addSelect('jpg')
            -> leftJoin('p.localisation', 'lc')
            -> addSelect('lc')
            -> leftJoin('p.htmlcontent', 'ht')
            -> addSelect('ht')
            -> andWhere('p.keymodule =:key')
            -> setParameter('key', $key)
            -> andwhere('p.deleted = false')
            -> orderBy('p.createAt', 'DESC')
            -> getQuery()
            -> getResult();

    }

    public function findlastByKey($key){
        $qb=$this->queryPostAll();
        return $qb
            -> andWhere('p.keymodule =:key')
            -> setParameter('key', $key)
            -> orderBy('p.createAt', 'DESC')
            -> setMaxResults(1)
            -> getQuery()
            -> getResult();
    }

    public function queryPostAll(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            -> andwhere('p.deleted = false')
            -> leftJoin('p.tagueries', 'tg')
            -> addSelect('tg')
            -> leftJoin('p.author', 'aut')
            -> addSelect('aut')
            -> leftJoin('p.media', 'md')
            -> addSelect('md')
            -> leftJoin('md.imagejpg', 'jpg')
            -> addSelect('jpg');

    }

    public function ListpostTrakofOnepost($wbid,$tag){
        return $this->createQueryBuilder('p')
            -> andwhere('p.deleted = false')
            -> innerJoin('p.tagueries','tg')
            -> addSelect('tg')
            -> andWhere('tg IN (:tag)')
            -> setParameter('tag', array_values($tag))
            -> leftJoin('p.author', 'aut')
            -> addSelect('aut')
            -> leftJoin('p.media', 'md')
            -> addSelect('md')
            -> leftJoin('md.imagejpg', 'jpg')
            -> addSelect('jpg')
            -> andWhere('p.website =:id')
            -> setParameter('id', $wbid)
            -> getQuery()
            -> getResult();
    }

    public function findlastBycity($city){ // todo ici plusieru technique pour les dates
        $date=new dateTime();
        $end = new \DateTimeImmutable();
        $start=$end->sub(new DateInterval('P150D'));
        $q= $this->createQueryBuilder('p')
            -> andwhere('p.deleted = false')
            -> leftJoin('p.tagueries', 'tg')
            -> addSelect('tg')
            -> leftJoin('p.author', 'aut')
            -> addSelect('aut')
            -> leftJoin('p.media', 'md')
            -> addSelect('md')
            -> leftJoin('md.imagejpg', 'jpg')
            -> addSelect('jpg')
            -> leftJoin('p.localisation', 'loc')
            -> addSelect('loc')
            -> andWhere('loc.id =:city')
            -> setParameter('city', $city)
            -> andWhere('p.createAt BETWEEN :start AND :end')
            //-> andWhere('loc.slugcity =:city')
            //-> setParameter('city', $city)
            //-> setParameter('start', $start->format('Y-m-d 00:00:00'))
            //-> setParameter('end', $end->format('Y-m-d 23:59:59'))
            -> setParameter('start', date('Y-m-d', strtotime(' - 300 days')))
            -> setParameter('end', $date)
            //-> setParameter('start', $start) ça marche aussi
            //-> setParameter('end', $end)  comme avec strotime à verifier
            -> orderBy('p.createAt', 'ASC');

        return $q
            -> getQuery()
            -> getResult();

    }

    public function findAllByKey($key){
        $date=new dateTime();
        $end = new \DateTimeImmutable();
        $start=$end->sub(new DateInterval('P150D'));
        $q= $this->createQueryBuilder('p')
            -> andwhere('p.deleted = false')
            -> leftJoin('p.tagueries', 'tg')
            -> addSelect('tg')
            -> leftJoin('p.author', 'aut')
            -> addSelect('aut')
            -> leftJoin('p.media', 'md')
            -> addSelect('md')
            -> leftJoin('md.imagejpg', 'jpg')
            -> addSelect('jpg')
            -> leftJoin('p.localisation', 'loc')
            -> addSelect('loc')
            -> andWhere('p.keymodule =:key')
            -> setParameter('key', $key)
            -> andWhere('p.createAt BETWEEN :start AND :end')
            //-> andWhere('loc.slugcity =:city')
            //-> setParameter('city', $city)
            //-> setParameter('start', $start->format('Y-m-d 00:00:00'))
            //-> setParameter('end', $end->format('Y-m-d 23:59:59'))
            -> setParameter('start', date('Y-m-d', strtotime(' - 300 days')))
            -> setParameter('end', $date)
            //-> setParameter('start', $start) ça marche aussi
            //-> setParameter('end', $end)  comme avec strotime à verifier
            -> orderBy('p.createAt', 'ASC');

        return $q
            -> getQuery()
            -> getResult();

    }

    public function findAroundlastBycity($locate){
        $date=new dateTime();
        $q= $this->createQueryBuilder('p')
            -> andwhere('p.deleted = false')
            -> leftJoin('p.tagueries', 'tg')
            -> addSelect('tg')
            -> leftJoin('p.media', 'md')
            -> addSelect('md')
            -> leftJoin('md.imagejpg', 'jpg')
            -> addSelect('jpg')
            -> leftJoin('p.website', 'w')
            -> addSelect('w')
            -> leftJoin('p.localisation', 'l')
            -> addSelect('l')
            -> andwhere('l.latloc <= :latend')
            -> andWhere('l.latloc >= :latstart')
            -> andWhere('l.lonloc >= :lonstart')
            -> andWhere('l.lonloc <= :lonend')
            -> setParameter('latend', ($locate->getLatloc()+1))
            -> setParameter('latstart', ($locate->getLatloc()-1))
            -> setParameter('lonstart', ($locate->getLonloc()-0.1))
            -> setParameter('lonend', ($locate->getLonloc()+0.1))
            -> andWhere('p.createAt BETWEEN :start AND :end')
            -> setParameter('start', date('Y-m-d', strtotime(' - 400 days')))
            -> setParameter('end', $date)
            -> orderBy('p.createAt', 'ASC');
        return $q
            -> getQuery()
            -> getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOnePost($id){
        return $this->createQueryBuilder('p')
            -> andwhere('p.deleted = false')
            -> andWhere('p.id =:id')
            -> setParameter('id', $id)
            -> leftJoin('p.tbmessages', 'tm')
            -> addSelect('tm')
            -> leftJoin('tm.idmessage', 'msg')
            -> addSelect('msg')
            -> leftJoin('msg.msgs', 'msgs')
            -> addSelect('msgs')
            -> leftJoin('p.author', 'ath')
            -> addSelect('ath')
            -> leftJoin('p.media', 'md')
            -> addSelect('md')
            -> leftJoin('md.imagejpg', 'img')
            -> addSelect('img')
            -> leftJoin('p.localisation', 'lc')
            -> addSelect('lc')
            -> leftJoin('p.htmlcontent', 'ht')
            -> addSelect('ht')
            -> getQuery()
            -> getOneorNullResult();
    }

    // function simples :

    public function findAllSimple($id){

        return  $this->createQueryBuilder('p')
            -> andwhere('p.deleted = false')
            -> andWhere('p.website =:id')
            -> leftJoin('p.website','wb')
            -> addSelect('wb')
            -> setParameter('id', $id)
            -> getQuery()
            -> getResult();
    }
    /**
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findOneSimple($id){
        return  $this->createQueryBuilder('p')
            -> andwhere('p.deleted = false')
            -> andWhere('p.id =:id')
            -> setParameter('id', $id)
            -> getQuery()
            -> getOneorNullResult();
    }

    /*-------------- non testés ----------------------------------*/



    public function queryPosts()
    {
        return $this->createQueryBuilder('p')
            -> andwhere('p.deleted = false')
            -> leftJoin('p.tagueries', 'tg')
            -> addSelect('tg')
            -> leftJoin('p.author', 'ath')
            -> addSelect('ath')
            -> leftJoin('p.media', 'md')
            -> addSelect('md')
            -> leftJoin('md.imagejpg', 'img')
            -> addSelect('img')
            -> leftJoin('p.localisation', 'lc')
            -> addSelect('lc')
            -> leftJoin('p.htmlcontent', 'ht')
            -> addSelect('ht');
    }

    public function queryPostsedit(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            -> leftJoin('p.tagueries', 'tg')
            -> addSelect('tg')
            -> leftJoin('p.author', 'ath')
            -> addSelect('ath')
            -> leftJoin('p.media', 'md')
            -> addSelect('md')
            -> leftJoin('md.imagejpg', 'img')
            -> addSelect('img')
            -> leftJoin('p.localisation', 'lc')
            -> addSelect('lc')
            -> leftJoin('p.htmlcontent', 'ht')
            -> addSelect('ht');
    }


    public function findCount($idwebsite){
        return $this->createQueryBuilder('p')
            -> where('p.deleted = false')
            -> getQuery()
            -> getResult();
    }

    /**
     * @param $key
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countPost($key): mixed
    {
        return $this->createQueryBuilder('p')
            -> select('count(p.id)')
            -> where('p.deleted = false')
            -> andWhere('p.keymodule =:key')
            -> setParameter('key', $key)
            -> getQuery()
            -> getSingleScalarResult();
    }

    /**
     * @param $id
     * @param $patch
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findPstQ2($id, $patch){
        $qb=$this->queryPostsedit();
        return $qb
            -> andWhere('p.id =:id')
            -> setParameter('id', $id)
            -> andWhere('ath.id =:patch')
            -> setParameter('patch', $patch)
            -> getQuery()
            -> getOneOrNullResult();
    }

    public function findPstQ0($id){
        $qb=$this->queryPostsedit();
        return $qb
            -> andWhere('p.id =:id')
            -> setParameter('id', $id)
            -> getQuery()
            -> getOneOrNullResult();
    }

    public function findPstQ3($id){
        $qb=$this->queryPosts();
        return $qb
            -> andWhere('p.website =:id')
            -> setParameter('id', $id)
            -> getQuery()
            -> getResult();
    }

    public function findPstQ3last($id){
        $qb=$this->queryPosts();
        return $qb
            -> andWhere('p.website =:id')
            -> setParameter('id', $id)
            -> orderBy('p.id', 'DESC')
            -> setMaxresults(1)
            -> getQuery()
            -> getResult();
    }


    public function findPostByAppointWithPeriodForOneTeam($provider, $start, $end){ //todo a revoir
        return $this->createQueryBuilder('p')
            -> leftJoin('p.idmodule', 'm')
            -> addSelect('m')
            -> leftJoin('m.postevent', 'e')
            -> addSelect('e')
            -> leftjoin('m.appointment', 'a')
            -> addSelect('a')
            -> leftjoin('m.spaceweb', 'pvd')
            -> addSelect('pvd')
            -> andWhere('pvd.id = :spaceweb')
            -> setParameter('spaceweb', $provider)
            -> andWhere('m.expire_at > :datetime')
            -> setParameter('datetime', new Datetime())
            -> andWhere('a.starttime <= :end')
            -> andWhere('a.endtime >= :start')
            -> setParameter('start', $start->format('Y-m-d 00:00:00'))
            -> setParameter('end', $end->format('Y-m-d 23:59:59'))
            -> orderBy('m.expire_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOnePostationWhitPeriodsforMonth($id, \DateTimeInterface $start, \DateTimeInterface $end):array
    {
        $qb= $this->querypostationAllById($id);
        $qb -> leftJoin('p.idmodule', 'm')
            -> addSelect('m')
            -> leftJoin('m.postevent', 'e')
            -> addSelect('e')
            -> leftJoin('e.media', 'md')
            -> addSelect('md')
            -> leftjoin('md.imagejpg', 'jpg')
            -> addSelect('jpg')
            -> leftjoin('m.appointment', 'a')
            -> addSelect('a')
            -> leftjoin('m.spaceweb', 'pvd')
            -> addSelect('pvd')
            -> leftJoin('pvd.template', 't')
            -> addSelect('t')
            -> leftjoin('a.idPeriods', 'pr')
            -> addSelect('pr')
            -> andWhere('a.starttime <= :end')
            -> andWhere('a.endtime >= :start')
            -> setParameter('start', $start->format('Y-m-d 00:00:00'))
            -> setParameter('end', $end->format('Y-m-d 23:59:59'));

        $qb->andWhere($qb->expr()->eq('a.confirmed', '?1'))
            ->setParameter('1', 1);
        return $qb
            ->getQuery()
            ->getArrayResult()
            ;
    }

    public function querypostationById($id)
    {
        return $this->createQueryBuilder('p')
            -> where('p.deleted = false')
            -> andWhere('p.id =:id')
            -> setParameter('id', $id);
    }
}
