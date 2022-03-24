<?php

namespace App\Repository\Entity;

use App\Entity\Admin\Wborders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Wborders|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wborders|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wborders[]    findAll()
 * @method Wborders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WbordersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wborders::class);
    }


    /**
     * @param $id
     * @return Wborders
     * @throws NonUniqueResultException
     */
    public function findAllOrder($id): Wborders
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.id = :val')
            ->setParameter('val', $id)
            ->leftJoin('w.wbcustomer', 'wb')
            ->addSelect('wb')
            ->leftJoin('w.products', 'ops')
            ->addSelect('ops')
            ->leftJoin('wb.website', 'wbs')
            ->addSelect('wbs')
            ->leftJoin('ops.subscription', 'sub')
            ->addSelect('sub')
            ->leftJoin('ops.product', 'p')
            ->addSelect('p')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findAllOrderForCoammande($id){
        return $this->createQueryBuilder('w')
            ->andWhere('w.id = :val')
            ->setParameter('val', $id)
            ->leftJoin('w.wbcustomer', 'wb')
            ->addSelect('wb')
            ->leftJoin('wb.website', 'wbs')
            ->addSelect('wbs')
            ->leftJoin('wbs.template', 't')
            ->addSelect('t')
            ->leftJoin('t.sector', 'sc')
            ->addSelect('sc')
            ->leftJoin('sc.adresse', 'adr')
            ->addSelect('adr')
            ->leftJoin('w.products', 'ops')
            ->addSelect('ops')
            ->leftJoin('ops.subscription', 'sub')
            ->addSelect('sub')
            ->leftJoin('ops.product', 'p')
            ->addSelect('p')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function byDateOrders()
    {
        $qb = $this->createQueryBuilder('w')
            ->leftJoin('w.wbcustomer','n')
            ->addSelect('n')
            ->leftJoin('n.website','wb')
            ->addSelect('wb')
            ->andWhere('w.state IS NOT NULL')
            ->orderBy('w.date');

        return $qb->getQuery()->getResult();
    }
}
