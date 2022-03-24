<?php

namespace App\Repository\Entity;

use App\Entity\DispatchSpace\Tballmessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tballmessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tballmessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tballmessage[]    findAll()
 * @method Tballmessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TballmessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tballmessage::class);
    }


    public function findByDispatchAndAllMsg($id)
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.tballmsgp', 'tp')
            ->addSelect('tp')
            ->leftJoin('tp.msgs', 'mp')
            ->addSelect('mp')
            ->leftJoin('t.tballmsgd', 'td')
            ->addSelect('td')
            ->leftJoin('td.msgs', 'md')
            ->addSelect('md')
            ->leftJoin('t.tballmsgs', 'ts')
            ->addSelect('ts')
            ->leftJoin('ts.msgs', 'ms')
            ->addSelect('ms')
            ->leftJoin('t. dispatch', 'd')
            ->addSelect('d')
            ->andWhere('d.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findallforcommand(){
        return $this->createQueryBuilder('t')
            ->leftJoin('t.tballmsgp', 'tp')
            ->addSelect('tp')
            ->leftJoin('tp.tabpublication', 'pt')
            ->addSelect('pt')
            ->leftJoin('t.tballmsgd', 'td')
            ->addSelect('td')
            ->leftJoin('t.tballmsgs', 'ts')
            ->addSelect('ts')
            ->getQuery()
            ->getResult();
    }

    public function findByDispatchAndBoardMsg($id)
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.tballmsgs', 'ts')
            ->addSelect('ts')
            ->leftJoin('ts.msgs', 'ms')
            ->addSelect('ms')
            ->leftJoin('t. dispatch', 'd')
            ->addSelect('d')
            ->andWhere('d.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllMsgswebsiteQuery($id): \Doctrine\ORM\Query
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.tballmsgs', 'ts')
            ->addSelect('ts')
            ->leftJoin('ts.msgs', 'ms')
            ->addSelect('ms')
            ->leftJoin('ms.tabreaders','tr')
            ->addSelect('tr')
            ->leftJoin('t. dispatch', 'd')
            ->addSelect('d')
            ->andWhere('d.id = :val')
            ->setParameter('val', $id)
            ->orderBy('t.id', 'DESC')
            ->getQuery();
    }

    public function findAllMsgQuery($id): \Doctrine\ORM\Query
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.tballmsgp', 'tp')
            ->addSelect('tp')
            ->leftJoin('tp.tabpublication', 'pt')
            ->addSelect('pt')
            ->leftJoin('pt.offre', 'o')
            ->addSelect('o')
            ->leftJoin('pt.post', 'po')
            ->addSelect('po')
            ->leftJoin('tp.msgs', 'mp')
            ->addSelect('mp')
            ->leftJoin('mp.tabreaders','trp')
            ->addSelect('trp')
            ->leftJoin('t.tballmsgd', 'td')
            ->addSelect('td')
            ->leftJoin('td.msgs', 'md')
            ->addSelect('md')
            ->leftJoin('md.tabreaders','trd')
            ->addSelect('trd')
            ->leftJoin('t.tballmsgs', 'ts')
            ->addSelect('ts')
            ->leftJoin('ts.websitedest', 'w')
            ->addSelect('w')
            ->leftJoin('w.template', 'tw')
            ->addSelect('tw')
            ->leftJoin('tw.logo', 'lg')
            ->addSelect('lg')
            ->leftJoin('ts.msgs', 'ms')
            ->addSelect('ms')
            ->leftJoin('ms.tabreaders','trs')
            ->addSelect('trs')
            ->leftJoin('t. dispatch', 'd')
            ->addSelect('d')
            ->andWhere('d.id = :val')
            ->setParameter('val', $id)
            ->orderBy('t.lastconvers', 'DESC')
            ->getQuery();
    }

}
