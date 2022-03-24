<?php

namespace App\Repository\Entity;

use App\Entity\Notifications\Notifdispatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notifdispatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notifdispatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notifdispatch[]    findAll()
 * @method Notifdispatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotifdispatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notifdispatch::class);
    }


    /**
     * @throws NonUniqueResultException
     */
    public function findBlogBynotifId($id)
    {
        return $this->createQueryBuilder('n')
            ->leftJoin('n.tabmsgp', 'tb')
            ->addSelect( 'tb')
            ->leftJoin('tb.idmessage', 'msg')
            ->addSelect( 'msg')
            ->leftJoin('msg.publicationmsg', 'pmsg')
            ->addSelect( 'pmsg')
            ->leftJoin('pmsg.tabpublication', 'tbp')
            ->addSelect( 'tbp')
            ->leftJoin('tbp.post', 'p')
            ->addSelect( 'p')
            ->andWhere('n.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findAllBynotifId($id)
    {
        return $this->createQueryBuilder('n')
            ->leftJoin('n.tabmsgp', 'tb')
            ->addSelect( 'tb')
            ->leftJoin('n.tabmsgd', 'td')
            ->addSelect( 'td')
            ->leftJoin('n.tabmsgs', 'ts')
            ->addSelect( 'ts')
            ->andWhere('n.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

}
