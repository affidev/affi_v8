<?php

namespace App\Repository\Entity;

use App\Entity\Module\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    // /**
    //  * @return resa[] Returns an array of resa array
    //  */

    public function findBySpaceWebForDate($id,\DateTimeInterface $start, \DateTimeInterface $end)
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.spaceweb','sp')
            ->addSelect('sp')
            ->andWhere('r.spaceweb = :id')
            ->setParameter('id', $id)
            ->where('a.start BETWEEN :start AND :end')
            ->setParameter('start', $start->format('Y-m-d 00:00:00'))
            ->setParameter('end', $end->format('Y-m-d 23:59:59'))
            ->orderBy('r.dateresa_at', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;
    }
    /**
     * @param $spaceweb
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findopendaysForOnespaceweb($spaceweb)
    {
        return $this->createQueryBuilder('r')
            -> leftJoin('r.idmodule', 'm')
            -> addSelect('m')
            -> leftJoin('m.postevent', 'pe')
            -> addSelect('pe')
            -> leftJoin('m.spaceweb', 'pvd')
            -> addSelect('pvd')
            -> leftJoin('pvd.template', 't')
            -> addSelect('t')
            -> leftJoin('r.resas', 'rsa')
            -> addSelect('rsa')
            -> andWhere('r.id =:id')
            -> setParameter('id', $provider)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findReservationByApi($id)
    {
        return $this->createQueryBuilder('r')
            -> andWhere('r.id = :id')
            -> setParameter('id', $id)
            -> leftJoin('r.idmodule', 'm')
            -> addSelect('m')
            -> leftJoin('m.postevent', 'e')
            -> addSelect('e')
            -> leftjoin('m.spaceweb', 'pvd')
            -> addSelect('pvd')
            -> leftjoin('pvd.template', 'tp')
            -> addSelect('tp')
            -> leftjoin('pvd.customer', 'cto')
            -> addSelect('cto')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

}
