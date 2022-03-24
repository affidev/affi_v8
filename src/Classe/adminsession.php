<?php

namespace App\Classe;

use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\DispatchSpace\Spwsite;
use App\Entity\Websites\Website;
use App\Exeption\RedirectException;
use App\Repository\Entity\DispatchSpaceWebRepository;
use App\Repository\Entity\SpwsiteRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Service\MenuNavigator;
use App\Service\Registration\Sessioninit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;


trait adminsession
{
    private EntityManagerInterface $em;
    private MenuNavigator $menuNav;
    private DispatchSpaceWebRepository $repodispacth;
    private SpwsiteRepository $spwrepo;
    private WebsiteRepository $wbrepo;
    private DispatchSpaceWeb $admin;
    private string $iddispatch;
    private DispatchSpaceWeb $dispatch;
    private array $permission=[];
    private RequestStack $requestStack;
    private Security $security;
    private Sessioninit $sessioninit;

    /**
     * ConstructatumCustomer constructor.
     * @param Sessioninit $sessioninit
     * @param Security $security
     * @param EntityManagerInterface $em
     * @param RequestStack $requestStack
     * @param MenuNavigator $menuNav
     * @param WebsiteRepository $websiteRepository
     * @param SpwsiteRepository $spwsiteRepository
     * @param DispatchSpaceWebRepository $repodispatch
     * @throws NonUniqueResultException
     * @throws RedirectException
     */
    public function __construct(Sessioninit $sessioninit, Security $security,EntityManagerInterface $em, RequestStack $requestStack, MenuNavigator $menuNav, WebsiteRepository $websiteRepository ,SpwsiteRepository $spwsiteRepository, DispatchSpaceWebRepository $repodispatch)
    {
        $this->em = $em;
        $this->menuNav = $menuNav;
        $this->wbrepo = $websiteRepository;
        $this->spwrepo = $spwsiteRepository;
        $this->sessioninit = $sessioninit;
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->repodispacth = $repodispatch;
        $this->pregmatch();
       // $this->admin = $this->getadminWebsite();

        if ($this->security->isGranted("IS_AUTHENTICATED_REMEMBERED") && !$this->requestStack->getSession()->has('idcustomer')) $sessioninit->initCustomer($this->security->getUser());
        if ($this->requestStack->getSession()->has('idcustomer')) {
            $this->dispatch=$this->repodispacth->findwithidcustomerAll($this->requestStack->getSession()->get('idcustomer'));
            $this->requestStack->getSession()->set('init', true);
        }
    }

    protected function pregmatch(){
        if (preg_match('/mob/i', $_SERVER['HTTP_USER_AGENT'])) {
            $this->requestStack->getSession()->set('agent', 'mobile/');
        } else {
            $this->requestStack->getSession()->set('agent', 'desk/');
        }
    }

    public function getadminDispatch(): ?DispatchSpaceWeb
    {
        return  $this->repodispacth->find(1);
    }

    public function getadminWebsite(): ?Website
    {
        return   $this->wbrepo->find(2);
    }

    public function getadminPwsite(): ?Spwsite
    {
        return   $this->spwrepo->find(4);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getDispatchByEmail($email)
    {
        return $this->repodispacth->finddispatchmail($email);
    }

}