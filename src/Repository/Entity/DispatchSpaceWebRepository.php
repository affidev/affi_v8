<?php

namespace App\Repository\Entity;

use App\Entity\DispatchSpace\DispatchSpaceWeb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DispatchSpaceWeb|null find($id, $lockMode = null, $lockVersion = null)
 * @method DispatchSpaceWeb|null findOneBy(array $criteria, array $orderBy = null)
 * @method DispatchSpaceWeb[]    findAll()
 * @method DispatchSpaceWeb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DispatchSpaceWebRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DispatchSpaceWeb::class);
    }

    public function queryDispacthById($id)
    {
        return $this->createQueryBuilder('d')
            -> andWhere('d.id =:id')
            -> setParameter('id', $id);
    }

    public function findForInit($id){

        return $this->queryDispacthById($id)
            -> leftJoin('d.customer', 'c')
            -> addSelect('c')
            -> getQuery()
            -> getOneOrNullResult();
    }

    /**
     * @param $email
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function finddispatchmail($email): mixed
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.customer','c')
            ->addSelect('c')
            ->leftJoin('c.profil','p')
            ->addSelect('p')
            ->andWhere('c.emailcontact = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $idcustomer
     * @return mixed
     */
    public function findwithidcustomerAll($idcustomer): mixed
    {
        return $this->createQueryBuilder('d')
            -> leftJoin('d.customer', 'c')
            -> addSelect('c')
            -> leftJoin('c.profil','p')
            -> addSelect('p')
            -> leftJoin('c.services','sv')
            -> addSelect('sv')
            -> leftJoin('d.dispatchlinks', 'l')
            -> addSelect('l')
            -> leftJoin('d.tbnotifs', 'tbn')
            -> addSelect('tbn')
            -> leftJoin('d.locality', 'loc')
            -> addSelect('loc')
            -> leftJoin('d.spwsite', 'pw')
            -> addSelect('pw')
            -> leftJoin('pw.website', 'w')
            -> addSelect('w')
            -> leftJoin('w.locality', 'lw')
            -> addSelect('lw')
            -> leftJoin('w.template', 't')
            -> addSelect('t')
            -> leftJoin('t.logo', 'lg')
            -> addSelect('lg')
            -> leftJoin('w.websitepartner', 'gp')
            -> addSelect('gp')
           // -> leftJoin('p.sector', 's')
           // -> addSelect('s')
            -> leftJoin('d.bulles', 'bb')
            -> addSelect('bb')
            //-> leftJoin('s.adresse', 'a')
            //-> addSelect('a')
            -> andWhere('c.id =:id')
            -> setParameter('id', $idcustomer)
            -> andWhere('pw.role = :admin OR pw.role = :super')
            -> setParameter('super', 'superadmin')
            -> setParameter('admin', 'admin')
            -> getQuery()
            -> getOneOrNullResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findwithAll($id)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.id = :val')
            ->setParameter('val', $id)
            ->leftJoin('d.spwsite','s')
            ->addSelect('s')
            ->leftJoin('d.sector','sc')
            ->addSelect('sc')
            ->leftJoin('sc.adresse','ad')
            ->addSelect('ad')
            ->leftJoin('s.website','w')
            ->addSelect('w')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findwithAllArray($id)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.id = :val')
            ->setParameter('val', $id)
            ->leftJoin('d.spwsite','s')
            ->addSelect('s')
            ->leftJoin('s.website','w')
            ->addSelect('w')
            ->getQuery()
            ->getArrayResult();
        ;
    }

    /**
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findLinkContact($id){
        return $this->createQueryBuilder('d')
            ->andWhere('d.id = :val')
            ->setParameter('val', $id)
            ->leftJoin('d.spwsite','s')
            ->addSelect('s')
            ->leftJoin('s.website','w')
            ->addSelect('w')
            ->leftJoin('w.module','m')
            ->addSelect('m')
            ->andWhere('m.typemodule = :contact')
            ->setParameter('contact', 'contact')
            ->andWhere('s.role = :admin')
            ->setParameter('admin', 'admin')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }



    public function findPostByAppointWithPeriodForOneTeam($id, $start, $end){
        return $this->queryDispacthById($id)
            -> leftJoin('d.template', 't')
            -> addSelect('t')
            -> leftJoin('d.module', 'm')
            -> addSelect('m')
            -> leftJoin('m.postevent', 'e')
            -> addSelect('e')
            -> leftjoin('m.appointment', 'a')
            -> addSelect('a')
            -> andWhere('a.starttime <= :end')
            -> andWhere('a.endtime >= :start')  //TODO c'est faux ......
            -> setParameter('start', $start->format('Y-m-d 00:00:00'))
            -> setParameter('end', $end->format('Y-m-d 23:59:59'))
            -> orderBy('p.startPeriod', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPostForAllSpaceWeb(){
        return $this->findActiveQuery()
            -> leftJoin('d.template', 't')
            -> addSelect('t')
            -> leftJoin('d.module', 'm')
            -> addSelect('m')
            -> leftJoin('m.postevent', 'e')
            -> addSelect('e')
            -> leftJoin('m.appointment', 'a')
            -> addSelect('a')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param $nameSpaceWeb
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findSpaceWebByName($nameSpaceWeb){
        return $this->findActiveQuery()
            ->andWhere('p.name =:name')
            ->setParameter('name', $nameSpaceWeb)
            ->getQuery()
            ->getOneOrNullResult();
    }


    private function findActiveQuery()
    {
        return $this->createQueryBuilder('d')
            ->where('d.active = true');
    }

    /**
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findandWebsite($id)
    {
        return $this->queryDispacthById($id)
            -> leftJoin('d.website', 'w')
            -> addSelect('w')
            -> getQuery()
            -> getOneOrNullResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findSpaceWebsById($id)
    {
        return $this->createQueryBuilder('d')
            -> where('d.active = true')
            -> andWhere('d.idUser =:id')
            -> setParameter('id', $id)
            -> getQuery()
            -> getOneOrNullResult();
    }

    /**
     * @param $id DispatchSpaceWeb
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findInfoObj($id){

        return $this->queryDispacthById($id)
            -> leftJoin('d.template', 't')
            -> addSelect('t')
            -> leftJoin('d.customer', 'c')
            -> addSelect('c')
            -> leftJoin('d.localisation', 'l')
            -> addSelect('l')
            -> leftJoin('t.sector', 's')
            -> addSelect('s')
            -> leftJoin('s.adresse', 'a')
            -> addSelect('a')
            -> getQuery()
            -> getOneOrNullResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findWhithOpendaysAndTemplate($id){
        return $this->queryDispacthById($id)
            -> leftJoin('d.template', 't')
            -> addSelect('t')
            -> leftJoin('t.logo', 'l')
            -> addSelect('l')
            -> leftJoin('t.sector', 's')
            -> addSelect('s')
            -> leftJoin('d.website', 'w')
            -> addSelect('w')
            -> leftJoin('w.tabopendays', 'o')
            -> addSelect('o')
            -> getQuery()
            -> getOneOrNullResult();
    }

}
