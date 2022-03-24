<?php

namespace App\Classe;

use App\Entity\Customer\Customers;
use App\Entity\Websites\Website;
use App\Exeption\RedirectException;
use App\Repository\Entity\DispatchSpaceWebRepository;
use App\Service\MenuNavigator;
use App\Service\Registration\Sessioninit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;


trait customersession
{

    private mixed $useragent;
    private EntityManagerInterface $em;
    private MenuNavigator $menuNav;
    private mixed $permission;
    private mixed $dispatch;
    private DispatchSpaceWebRepository $repodispacth;
    private array $locate;
    private Security $security;
    private Website|null $board;
    private RouterInterface $router;
    private Sessioninit $sessioninit;
    private Customers|null $customer;
    private RequestStack $requestStack;

    /**
     * ConstructatumCustomer constructor.
     * @param Sessioninit $sessioninit
     * @param Security $security
     * @param EntityManagerInterface $em
     * @param RequestStack $requestStack
     * @param MenuNavigator $menuNav
     * @param DispatchSpaceWebRepository $repodispatch
     * @throws NonUniqueResultException
     * @throws RedirectException
     */
    public function __construct(Sessioninit $sessioninit, Security $security,EntityManagerInterface $em, RequestStack $requestStack, MenuNavigator $menuNav, DispatchSpaceWebRepository $repodispatch)
    {
        $this->em = $em;
        $this->menuNav = $menuNav;
        $this->sessioninit = $sessioninit;
        $this->security = $security;
        $this->requestStack = $requestStack;
        $this->repodispacth = $repodispatch;
        $this->pregmatch();


        if ($this->security->isGranted("IS_AUTHENTICATED_REMEMBERED") && !$this->requestStack->getSession()->has('idcustomer')) $sessioninit->initCustomer($this->security->getUser());
        if ($this->requestStack->getSession()->has('idcustomer')) {

            //todo faire la verif avec le user de la session
            $this->dispatch=$this->repodispacth->findwithidcustomerAll($this->requestStack->getSession()->get('idcustomer'));
            $this->customer=$this->dispatch->getCustomer();
            $this->board=$this->dispatch->getSpwsite()[0]->getWebsite();
            $this->requestStack->getSession()->set('init', true);
            $this->permission=$this->requestStack->getSession()->get('permission');
        }else{
            $this->clearinit();
        }
    }

    protected function pregmatch(){
        if (preg_match('/mob/i', $_SERVER['HTTP_USER_AGENT'])) {
            $this->useragent = "mobile/";
        } else {
            $this->useragent = "desk/";
        }
    }

    /**
     */
    public function clearinit(){
        $this->requestStack->getSession()->remove('city');
        $this->requestStack->getSession()->remove('idcustomer');
        $this->requestStack->getSession()->remove('lat');
        $this->requestStack->getSession()->remove('lon');
        $this->requestStack->getSession()->remove('iddisptachweb');
        $this->requestStack->getSession()->remove('permission');
    }

    /**
     * @throws NonUniqueResultException
     * @throws RedirectException
     */
    public function reClearInit(){
        $this->requestStack->getSession()->remove('city');
        $this->requestStack->getSession()->remove('idcustomer');
        $this->requestStack->getSession()->remove('lat');
        $this->requestStack->getSession()->remove('lon');
        $this->requestStack->getSession()->remove('iddisptachweb');
        $this->requestStack->getSession()->remove('permission');
        $this->sessioninit->initCustomer($this->security->getUser());
    }


}