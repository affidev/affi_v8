<?php

namespace App\Repository\Entity;

use App\Entity\Module\PostEvent;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PostEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostEvent[]    findAll()
 * @method PostEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostEvent::class);
    }

    public function findEventKey($key){
        $qb=$this->queryKeyEvent();
        return $qb
            -> andWhere('p.keymodule =:key')
            -> setParameter('key', $key)
            -> andwhere('p.deleted = false')
            -> orderBy('p.create_at', 'ASC')
            -> getQuery()
            -> getResult();
    }

    public function findEventsByKeyWithOutId($key, $id){
        $qb=$this->queryKeyEvent();
        return $qb
            -> andWhere('p.keymodule =:key')
            -> setParameter('key', $key)
            -> andwhere('p.deleted = false')
            -> andwhere('p.id !=:id')
            -> setParameter('id', $id)
            -> orderBy('p.create_at', 'ASC')
            -> getQuery()
            -> getResult();
    }

    public function findlastByKey($key){
        $qb=$this->queryKeyEvent();
        return $qb
            -> andWhere('p.keymodule =:key')
            -> setParameter('key', $key)
            -> orderBy('p.create_at', 'DESC')
            -> setMaxResults(1)
            -> getQuery()
            -> getResult();
    }

    public function findLastByCityBeforeWeek($city){ // todo ici plusieru technique pour les dates
        $date=new dateTime();
        //$end = new \DateTimeImmutable();
      //  $start=$end->sub(new DateInterval('P150D'));
        $qb=$this->queryKeyEvent();
        return $qb
            -> andWhere('loc.id =:city')
            -> setParameter('city', $city)
            -> andWhere('ap.starttime <= :now AND ap.endtime >= :now')
            //-> setParameter('start', $start->format('Y-m-d 00:00:00'))
            //-> setParameter('end', $end->format('Y-m-d 23:59:59'))
           // -> setParameter('start', date('Y-m-d', strtotime(' - 300 days')))
            -> setParameter('now', $date)
            //-> setParameter('start', $start) ça marche aussi
            //-> setParameter('end', $end)  comme avec strotime à verifier
            -> orderBy('p.create_at', 'ASC')
            -> getQuery()
            -> getResult();

    }

    /**
     * @throws NonUniqueResultException
     */
    public function findEventById($id){
        $qb=$this->queryKeyEvent();
        return $qb
            -> andWhere('p.id =:id')
            -> setParameter('id', $id)
            -> getQuery()
            -> getOneOrNullResult();
    }

    public function queryKeyEvent(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            -> andwhere('p.deleted = false')
            -> leftJoin('p.appointment', 'ap')
            -> addSelect('ap')
            -> leftJoin('ap.tabdate', 'td')
            -> addSelect('td')
            -> leftJoin('ap.localisation', 'loc')
            -> addSelect('loc')
            -> leftJoin('p.tagueries', 'tag')
            -> addSelect('tag')
            -> leftJoin('p.media', 'm')
            -> addSelect('m')
            -> leftJoin('m.imagejpg', 'pic')
            -> addSelect('pic')
            -> leftJoin('p.partners', 'pa')
            -> addSelect('pa');

    }
}
