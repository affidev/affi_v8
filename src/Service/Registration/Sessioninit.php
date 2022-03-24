<?php

namespace App\Service\Registration;

use App\Entity\Customer\Customers;
use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\Sector\Gps;
use App\Entity\User;
use App\Exeption\RedirectException;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class Sessioninit
{
    private RouterInterface $router;
    private RequestStack $requestStack;
    private UserRepository $repouser;

    public function __construct(RequestStack $requestStack, UserRepository $userRepository, RouterInterface $router){
        $this->requestStack = $requestStack;
        $this->repouser = $userRepository;
        $this->router = $router;
    }


    /**
     * @param $user User
     * @return DispatchSpaceWeb|void|null
     * @throws NonUniqueResultException
     * @throws RedirectException
     */
    public function initCustomer(User $user){
        if(!$user->getCharte()) throw new  RedirectException($this->router->generate('spaceweb_charte'));
        /** @var Customers $customer */
        $customer=$this->repouser->findAllCustomByUserId($user->getId())->getCustomer();
        $this->requestStack->getSession()->set('idcustomer', $customer->getId());
        $this->requestStack->getSession()->set('typeuser', 'customer');
        if($avatar=$customer->getProfil()->getAvatar()) $this->requestStack->getSession()->set('avatar', $avatar->getUrl());
        if($dispatch=$customer->getDispatchspace()){
            $this->initSession($customer->getDispatchspace());
            return $dispatch;
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    public function preinitCustomer($user){
        /** @var Customers $customer */
        $customer=$this->repouser->findAllCustomByUserId($user->getId())->getCustomer();
        $this->requestStack->getSession()->set('idcustomer', $customer->getId());
        $this->requestStack->getSession()->set('typeuser', 'customer');
        if($dispatch=$customer->getDispatchspace()){
            $this->requestStack->getSession()->set('iddisptachweb', $dispatch->getId());
        }
    }

    /**
     * @var DispatchSpaceWeb $dispatch
     */
    public function initSession(DispatchSpaceWeb $dispatch){
        $this->requestStack->getSession()->set('iddisptachweb', $dispatch->getId());
        $this->requestStack->getSession()->set('namespaceweb', $dispatch->getName());
        $this->requestStack->getSession()->set('permission',$dispatch->getPermission());
        if($loc=$dispatch->getLocality()){
            $this->requestStack->getSession()->set('city', $loc->getCity());
            $this->requestStack->getSession()->set('idcity', $loc->getId());
            $this->requestStack->getSession()->set('lon', $loc->getLonloc());
            $this->requestStack->getSession()->set('lat', $loc->getLatloc());
        }
    }

    /**
     * @var DispatchSpaceWeb $dispatch
     */
    public function chenageLoc($dispatch){
        $loc=$dispatch->getLocality();
        $this->requestStack->getSession()->set('city', $loc->getCity());
        $this->requestStack->getSession()->set('lon', $loc->getLonloc());
        $this->requestStack->getSession()->set('lat', $loc->getLatloc());
    }

    /**
     * @var DispatchSpaceWeb $dispatch
     */
    public function preInitSpaceWeb($dispatch){
        $this->requestStack->getSession()->set('iddisptachweb', $dispatch->getId());
        $this->requestStack->getSession()->set('namespaceweb', $dispatch->getName());
        $this->requestStack->getSession()->set('permission',$dispatch->getPermission());
    }

    /**
     * @param $loc Gps
     */
    public function ipOrGpsPublicLoc($loc){
        $this->requestStack->getSession()->set('city', $loc->getCity());
        $this->requestStack->getSession()->set('lon', $loc->getLonloc());
        $this->requestStack->getSession()->set('lat', $loc->getLatloc());
    }
}