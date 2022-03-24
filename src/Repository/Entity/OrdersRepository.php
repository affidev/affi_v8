<?php

namespace App\Repository\Entity;

use App\Entity\Admin\Orders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Orders|null find($id, $lockMode = null, $lockVersion = null)
 * @method Orders|null findOneBy(array $criteria, array $orderBy = null)
 * @method Orders[]    findAll()
 * @method Orders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orders::class);
    }


    /**
     * @throws NonUniqueResultException
     */
    public  function findAllcmd($id){
        return $this->createQueryBuilder('o')
            ->andWhere('o.id = :val')
            ->setParameter('val', $id)
            ->leftJoin('o.numclient', 'cl')
            ->addSelect('cl')
            ->leftJoin('o.listproducts', 'lp')
            ->addSelect('lp')
            ->leftJoin('cl.idcustomer', 'cu')
            ->addSelect('cu')
            ->leftJoin('cu.profil', 'pr')
            ->addSelect('pr')
            ->leftJoin('cu.dispatchspace', 'dps')
            ->addSelect('dps')
            ->leftJoin('dps.sector', 'sc')
            ->addSelect('sc')
            ->leftJoin('sc.adresse', 'ad')
            ->addSelect('ad')
            ->leftJoin('lp.subscription', 'sub')
            ->addSelect('sub')
            ->leftJoin('lp.product', 'p')
            ->addSelect('p')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function byFacture($utilisateur)
    {
        $qb = $this->createQueryBuilder('o')
            ->select('o')
            ->where('o.utilisateur = :utilisateur')
            ->andWhere('o.valider = 1')
            ->andWhere('o.reference != 0')
            ->orderBy('o.id')
            ->setParameter('utilisateur', $utilisateur);
        return $qb->getQuery()->getResult();
    }

    public function byDateCommand($date)
    {
        $qb = $this->createQueryBuilder('o')
            ->select('o')
            ->where('o.date > :date')
            ->andWhere('o.valider = 1')
            ->orderBy('o.id')
            ->setParameter('date', $date);

        return $qb->getQuery()->getResult();
    }

    public function byDateOrders()
    {
        $qb = $this->createQueryBuilder('o')
            ->leftJoin('o.numclient','n')
            ->addSelect('n')
            ->leftJoin('n.idcustomer','c')
            ->addSelect('c')
            ->leftJoin('c.dispatchspace','d')
            ->addSelect('d')
            ->andWhere('o.state IS NOT NULL')
            ->orderBy('o.date');

        return $qb->getQuery()->getResult();
    }
}
