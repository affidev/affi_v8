<?php

namespace App\Classe;

use App\Entity\Customer\Customers;
use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\Sector\Gps;
use App\Repository\Entity\DispatchSpaceWebRepository;
use App\Service\MenuNavigator;
use App\Service\Registration\Sessioninit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;


trait initdispatch
{
    private EntityManagerInterface $em;
    private MenuNavigator $menuNav;
    private mixed $iddispatch;
    private DispatchSpaceWeb $dispatch;
    private DispatchSpaceWebRepository $repodispacth;
    private Gps $locate;
    private Customers $customer;
    private Security $security;
    private RequestStack $requestStack;
    private Sessioninit $sessioninit;

    /**
     * ConstructatumCustomer constructor.
     * @param Security $security
     * @param Sessioninit $sessioninit
     * @param DispatchSpaceWebRepository $repodispatch
     * @param EntityManagerInterface $em
     * @param RequestStack $requestStack
     * @param MenuNavigator $menuNav
     */
    public function __construct(Security $security,Sessioninit $sessioninit,DispatchSpaceWebRepository $repodispatch, EntityManagerInterface $em,RequestStack $requestStack, MenuNavigator $menuNav){

        $this->em = $em;
        $this->menuNav = $menuNav;
        $this->requestStack = $requestStack;
        $this->repodispacth = $repodispatch;
        $this->security = $security;
        $this->sessioninit = $sessioninit;
        $this->requestStack = $requestStack;
        $this->pregmatch();

        if ($this->requestStack->getSession()->has('iddisptachweb')){
            $this->iddispatch = $this->requestStack->getSession()->get('iddisptachweb');
            $this->dispatch = $this->repodispacth->find($this->iddispatch);
            $this->customer=$this->dispatch->getCustomer();
            $this->locate=$this->dispatch->getLocality();
        }
    }

    protected function pregmatch(){
        if (preg_match('/mob/i', $_SERVER['HTTP_USER_AGENT'])) {
            $this->requestStack->getSession()->set('agent', 'mobile/');
        } else {
            $this->requestStack->getSession()->set('agent', 'desk/');
        }
    }
}