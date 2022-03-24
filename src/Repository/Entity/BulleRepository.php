<?php

namespace App\Repository\Entity;

use App\Entity\Bulles\Bulle;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

/**
 * @method Bulle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bulle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bulle[]    findAll()
 * @method Bulle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BulleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bulle::class);
    }


    /**
     * @throws NonUniqueResultException
     */
    public function findBulleOfPublication($id, $classe){
        return $this->createQueryBuilder('b')
            -> leftJoin('b.bullettes','bl')
            -> addSelect('bl')
            -> leftJoin('b.Dispatchp','d')
            -> addSelect('d')
            -> andWhere('b.idmodule = :id AND b.modulebubble = :classe')
            -> setParameter('id', $id)
            -> setParameter('classe', $classe)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }


    /**
     * @param $datenow
     * @return Bulle[] Returns an array of bulle objects
     */
    public function findAleatBubbleNow($datenow): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.create_at >= :val')
            ->setParameter('val', $datenow)
            -> leftJoin('b.bullettes','bl')
            -> addSelect('bl')
            -> leftJoin('b.media','m')
            -> addSelect('m')
            -> leftJoin('m.imagejpg','i')
            -> addSelect('i')
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $locate
     * @return QueryBuilder
     * @throws Exception
     */
    public function QueryBulbeLocate($locate): QueryBuilder
    {
        return $this->QueryBulbeAll()
            -> andwhere('l.latloc <= :latend')
            -> andWhere('l.latloc >= :latstart')
            -> andWhere('l.lonloc >= :lonstart')
            -> andWhere('l.lonloc <= :lonend')
            -> setParameter('latend', ($locate['lat']+1))
            -> setParameter('latstart', ($locate['lat']-1))
            -> setParameter('lonstart', ($locate['long']-0.1))
            -> setParameter('lonend', ($locate['long']+0.1));
    }

    /**
     * @param $lat
     * @param $lon
     * @return QueryBuilder
     * @throws Exception
     */
    public function QueryBulbeLocateObj($lat, $lon): QueryBuilder
    {
        return $this->QueryBulbeAll()
            -> andwhere('l.latloc <= :latend')
            -> andWhere('l.latloc >= :latstart')
            -> andWhere('l.lonloc >= :lonstart')
            -> andWhere('l.lonloc <= :lonend')
            -> setParameter('latend', ($lat+1))
            -> setParameter('latstart', ($lat-1))
            -> setParameter('lonstart', ($lon-0.1))
            -> setParameter('lonend', ($lon+0.1));
    }

    /**
     * @return QueryBuilder
     * @throws Exception
     */
    public function QueryBulbeAll(): QueryBuilder
    {
        return $this->createQueryBuilder('b')
            -> leftJoin('b.offre','ofr')
            -> addSelect('ofr')
            -> leftJoin('ofr.website','w')
            -> addSelect('w')
            -> leftJoin('w.locality','l')
            -> addSelect('l')
            -> andWhere('b.create_at BETWEEN :start AND :end')
            -> setParameter('start',((new DateTime())->modify('- 8 days'))->format('Y-m-d 00:00:00'))
            -> setParameter('end', ((new DateTime()))->format('Y-m-d 23:59:59'));
    }

    /**
     * @param $locate
     * @return mixed
     * @throws Exception
     */
    public function findAleatBubbleNowLocate($locate): mixed
    {
        if($locate){
            $qr= $this->QueryBulbeLocate($locate);
        }else{
            $qr=$this->QueryBulbeAll();
        }
        return $qr -> leftJoin('b.bullettes','bl')
                -> addSelect('bl')
                -> leftJoin('b.media','m')
                -> addSelect('m')
                -> leftJoin('m.imagejpg','i')
                -> addSelect('i')
                -> setMaxResults(6)
                -> getQuery()
                -> getResult()
            ;
    }

    /**
     * @param $lat
     * @param $lon
     * @return mixed
     * @throws Exception
     */
    public function findAleatBubbleNowLocateObj($lat, $lon){
        $qr= $this->QueryBulbeLocateObj($lat, $lon);
        return $qr -> leftJoin('b.bullettes','bl')
            -> addSelect('bl')
            -> leftJoin('b.media','m')
            -> addSelect('m')
            -> leftJoin('m.imagejpg','i')
            -> addSelect('i')
            -> setMaxResults(6)
            -> getQuery()
            -> getResult();
    }

    public function  findInfo($id){

        return $this->createQueryBuilder('b')
            -> andWhere('b.id = :id')
            -> setParameter('id', $id)
            -> leftJoin('b.bullettes','bl')
            -> addSelect('bl')
            -> leftJoin('b.tagueries','tg')
            -> addSelect('tg')
            -> leftJoin('b.dispatchspaceexp','d')
            -> addSelect('d')
            -> leftJoin('d.template', 't')
            -> addSelect('t')
            -> leftJoin('t.sector', 's')
            -> addSelect('s')
            -> leftJoin('s.adresse', 'a')
            -> addSelect('a')
            -> getQuery()
            -> getArrayResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function  findAllOneBUlle($id){

        return $this->createQueryBuilder('b')
            -> andWhere('b.id = :id')
            -> setParameter('id', $id)
            -> leftJoin('b.media','m')
            -> addSelect('m')
            -> leftJoin('m.imagejpg','i')
            -> addSelect('i')
            -> leftJoin('b.bullettes','bl')
            -> addSelect('bl')
            -> leftJoin('b.tagueries','tg')
            -> addSelect('tg')
            -> leftJoin('b.moduletype','mt')
            -> addSelect('mt')
            -> leftJoin('mt.website','w')
            -> addSelect('w')
            //-> leftJoin('d.template', 't')
            //-> addSelect('t')
           // -> leftJoin('t.sector', 's')
           // -> addSelect('s')
           // -> leftJoin('s.adresse', 'a')
           // -> addSelect('a')
            -> getQuery()
            ->getOneOrNullResult();
    }

    public function  findShowBulle($id){

        return $this->createQueryBuilder('b')
            -> andWhere('b.id = :id')
            -> setParameter('id', $id)
            -> leftJoin('b.media','m')
            -> addSelect('m')
            -> leftJoin('m.imagejpg','i')
            -> addSelect('i')
            -> leftJoin('b.bullettes','bl')
            -> addSelect('bl')
            -> leftJoin('b.tagueries','tg')
            -> addSelect('tg')
            -> leftJoin('b.moduletype','mt')
            -> addSelect('mt')
            -> leftJoin('mt.website','w')
            -> addSelect('w')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.logo', 'l')
            -> addSelect('l')
            -> getQuery()
            ->getOneOrNullResult();
    }
}
