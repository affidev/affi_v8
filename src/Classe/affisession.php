<?php

namespace App\Classe;


use App\Entity\Customer\Customers;
use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\DispatchSpace\Spwsite;
use App\Entity\Websites\Website;
use App\Exeption\RedirectException;
use App\Repository\Entity\DispatchSpaceWebRepository;
use App\Repository\Entity\GpsRepository;
use App\Repository\Entity\SpwsiteRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Service\MenuNavigator;
use App\Service\Registration\Sessioninit;
use App\Util\DefaultModules;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

trait affisession
{
    private EntityManagerInterface $em;
    private MenuNavigator $menuNav;
    private mixed $customerid;
    private mixed $iddispatch;
    private DispatchSpaceWeb|null $dispatch;
    private Spwsite|null $pw;
    private array $pwArray;
    private DispatchSpaceWebRepository $repodispacth;
    private SpwsiteRepository $repopw;
    private array $locate;
    private bool $admin;
    private $city;
    private $memberwb;
    private mixed $permission = [];
    private Security $security;
    private Website|null $board;
    private GpsRepository $gpsRepository;
    private RouterInterface $router;
    private Sessioninit $sessioninit;
    private Customers|null $customer;
    private Bool $useragent;
    private RequestStack $requestStack;
    private array $tabmodule;
    private array $listwb;
    private array $catchs;
    private WebsiteRepository $repoWebsite;


    /**
     * ConstructatumCustomer constructor.
     * @param Security $security
     * @param ManagerRegistry $doctrine
     * @param RequestStack $requestStack
     * @param MenuNavigator $menuNav
     * @param RouterInterface $router
     * @param WebsiteRepository $repoWebsite
     * @param SpwsiteRepository $spwsiteRepository
     * @param DispatchSpaceWebRepository $repodispatch
     * @param GpsRepository $gpsRepository
     * @param Sessioninit $sessioninit
     * @throws NonUniqueResultException
     * @throws RedirectException
     */
    public function __construct(Security $security, ManagerRegistry $doctrine, RequestStack $requestStack, MenuNavigator $menuNav, RouterInterface $router,
                                WebsiteRepository $repoWebsite,SpwsiteRepository $spwsiteRepository, DispatchSpaceWebRepository $repodispatch, GpsRepository $gpsRepository, Sessioninit $sessioninit)
    {
        $this->em = $doctrine->getManager();
        $this->admin = false;
        $this->dispatch = null;
        $this->menuNav = $menuNav;
        $this->requestStack = $requestStack;
        $this->repodispacth = $repodispatch;
        $this->repopw = $spwsiteRepository;
        $this->gpsRepository = $gpsRepository;
        $this->repoWebsite=$repoWebsite;
        $this->router = $router;
        $this->sessioninit = $sessioninit;
        $this->security = $security;
        $this->customerid=null;
        $this->customer=null;
        $this->iddispatch =null;
        $this->locate =[];
        $this->permission =null;
        $this->tabmodule=[];
        $this->pregmatch();
        $this->catchs['blog']=[];
        $this->catchs['event']=[];


        if ($this->security->isGranted("IS_AUTHENTICATED_REMEMBERED")){
            if(!$this->requestStack->getSession()->has('idcustomer')) $sessioninit->initCustomer($this->security->getUser());
            $this->iddispatch = $this->requestStack->getSession()->get('iddisptachweb');
            /* dispatch comprend le customer, le profil, la liste des spwsite superadmin avec leur website attaché et dependances */
            // modif du 29/10/2021 : la liste des spwsite est limitée à ceux dont le dispatch est superadmin et admin
            $this->dispatch=$this->repodispacth->findwithidcustomerAll($this->requestStack->getSession()->get('idcustomer'));

         // dump($this->requestStack->getSession()->get('idcustomer'));

            $this->requestStack->getSession()->set('init', true);
        }else{
        $this->clearinit();}
    }

    protected function pregmatch()
    {
        if (preg_match('/mob/i', $_SERVER['HTTP_USER_AGENT'])) {
            $this->requestStack->getSession()->set('agent', 'mobile/');
            $this->useragent=false;
        } else {
            $this->requestStack->getSession()->set('agent', 'desk/');
            $this->useragent=true;
        }
    }

    public function selectedPwBoard($id): bool
    {
        foreach ($this->dispatch->getSpwsite() as $pw) {
            if ($pw->getWebsite()->getId() == $id){
                $this->board = $pw->getWebsite();
                $this->pw=$pw;
                return true;
            }
        }
        return false;
    }

    public function isPwDispatch($website): bool
    {
        foreach ($this->dispatch->getSpwsite() as $pw) {
            if ($pw->getWebsite()->getId() == $website->getId()){
                if($pw->isSuper() || $pw->getRole()=="admin")
                    $this->pw=$pw;
                return true;
            }
        }
        return false;
    }

    public function getTabCodeWb(): array|bool
    {
        if($this->dispatch){
            foreach ($this->dispatch->getSpwsite() as $pw) {
                $tabcode[]=$pw->getWebsite()->getCodesite();
            }
            return $tabcode??[];
        }else{
            return false;
        }
    }

    public function activeBoard($board=null)
    {
        if($board) {
            foreach ($this->dispatch->getSpwsite() as $pw) {
                if ($pw->getWebsite()->getSlug() == $board) return $this->board = $pw->getWebsite();
            }
        }
        foreach ($this->dispatch->getSpwsite() as $pw) {
            if ($pw->isDefault()) return $this->board = $pw->getWebsite();
        }

        //todo a suivre - modif du 27/01/22 suite a l'abandon de la creation de baord par defaut
        $this->board = $this->dispatch->getSpwsite()[0]->getWebsite();

    }

    public function reinitDefaultBoard($id): bool
    { // todo rajouter peut etre un test pour savoir si $id correspond bien a un spwsite du dispatch
        foreach ($this->dispatch->getSpwsite() as $pw) {
            $pw->setIsdefault(false);
            if ($pw->getWebsite()->getId() == $id){
                $this->dispatch->setLocality($pw->getWebsite()->getLocality());
                $pw->setIsdefault(true);
            }
            $this->em->persist($pw);
        }
        $this->em->persist($this->dispatch);
        $this->em->flush();
        return true;
    }

    public function intisimplCatchs(){
        $this->catchs['blog']=[]; $this->catchs['shop']=[]; $this->catchs['event']=[];
        foreach ($this->dispatch->getBulles() as $catch){
            switch ($catch->getModulebubble()){
                case 'blog':
                $this->catchs['blog'][]=$catch;
                break;

                case'shop':
                $this->catchs['shop'][]=$catch;
                break;

                case 'event':
                $this->catchs['event'][]=$catch;
                break;
            }
        }
    }

    public function intiTabbulle($city, $activities): bool|array
    {
         $t2=[];
        foreach ($this->dispatch->getSpwsite() as $pw){
            if($pw->getRole() == "superadmin"){
                $t2['city'][]=$pw->getWebsite()->getLocality()->getSlugcity();
            }
        }
        if(isset($t2['city'])){
            if (in_array($city, $t2['city'])){
                foreach ($activities as $module) {
                    $bulles[]=['name'=>DefaultModules::TAB_MODULES_NAME[$module],'url'=>$this->generateUrl(DefaultModules::TAB_MODULES_URL_ID[$module],['id'=>$this->board->getId()],UrlGeneratorInterface::ABSOLUTE_URL)];
                }
            }
            return $bulles??false;
        }
        return false;
    }

    public function reqTabWbUser($city, $activities): array
    {
        $t1=[]; $t2=[];
        foreach ($this->dispatch->getSpwsite() as $pw){
            if($pw->getRole() == "superadmin"){
                $t2['name'][]=$pw->getWebsite()->getNamewebsite();
                $t2['city'][]=$pw->getWebsite()->getLocality()->getSlugcity();
                $t2['id'][]=$pw->getWebsite()->getId();
            }else{
                $t1['name'][]=$pw->getWebsite()->getNamewebsite();
                $t1['city'][]=$pw->getWebsite()->getLocality()->getSlugcity();
                $t1['id'][]=$pw->getWebsite()->getId();
            }
        }
        return ['admin'=>$t2,'member'=>$t1];
    }



    /**
     * @throws NonUniqueResultException
     */
    public function getAllspwsiteOfWebsite($id)
    {
        $wb = $this->repoWebsite->findWbPWById($id);
        if(!$wb) return throw new Exception('board inconnu');
        $this->board=$wb;
        $allpw=$wb->getSpwsites();
        foreach ($allpw as $pw) {
            if ($pw->getDisptachwebsite()->getId() == $this->iddispatch){
                $this->pw=$pw;
                return $allpw;
            }
        }
        return false;
    }

    /**
     * @param $key
     * @return false
     */
    public function initBoardByKey($key): bool  // récuperation du board par sa clé pour un admin
    {
        $this->pw = $this->repopw->findPwByWbKeyForIdDipsatch($key, $this->iddispatch);
        if ($this->pw != null){
            $this->admin = $this->pw->isSuper() || $this->pw->getRole()=="admin";
            $this->board=$this->pw->getWebsite();
            return true;
        }
        return false;
    }




    /*-------------------------------  a verif ------------------------------*/

    /**
     * @param $idwebsite
     * @return false
     * @throws NonUniqueResultException
     */
    public function getUserspwsiteOfWebsite($idwebsite): bool
    {
        if($this->security->isGranted("ROLE_SUPER_ADMIN")){
            $this->pw = $this->repopw->findPwByWbForSuperAdmin($idwebsite);
                if ($this->pw != null){
                    $this->admin = true;
                    $this->board=$this->pw->getWebsite();
                    return true;
                }
                return false;

        }else{
            $this->pw = $this->repopw->findPwByWbForIdDipsatch($idwebsite, $this->iddispatch);
                if ($this->pw != null){
                    $this->admin = $this->pw->isSuper();
                    $this->board=$this->pw->getWebsite();
                    return true;
                }else{
                    return false;
                }

        }

    }



    public function getUserspwsiteOfWebsiteByKey($key): bool //todo idem fonction précedente à supprimer des refactorisation des controller terminée
    {
        $this->pw = $this->repopw->findPwByWbKeyForIdDipsatch($key, $this->iddispatch);
        if ($this->pw != null){
            $this->admin = $this->pw->isSuper();
            $this->board=$this->pw->getWebsite();
            return true;
        }
        return false;
    }

    /**
     * @param $slug
     * @return false
     */
    public function getUserspwsiteOfWebsiteSlug($slug): bool
    {
        $this->pw = $this->repopw->findPwByWbSlugForIdDipsatch($slug, $this->iddispatch);
        if ($this->pw != null){
            $this->admin = $this->pw->isSuper();
            $this->board=$this->pw->getWebsite();
            return true;
        }
        return false;
    }

    /**
     * @param $slug
     * @return Spwsite
     * @throws NonUniqueResultException
     */
    public function isDispatchIsSuperadminOfWebsite($slug): Spwsite
    {
        $this->pw = $this->repopw->findOrNullSuperadminForWebsiteByOneDispatch($slug, $this->iddispatch);
        if ($this->pw != null) $this->admin = true;
        return $this->pw;
    }


    /**
     * @throws NonUniqueResultException
     */
    public function getDispatchByEmail($email)
    {
        return $this->repodispacth->finddispatchmail($email);
    }

    /**
     * @param SpwsiteRepository $spwsiteRepository
     * @return mixed
     */
    public function getListeThanOneDispath(SpwsiteRepository $spwsiteRepository): mixed
    {

        return $spwsiteRepository->findwithAll($this->iddispatch);
    }


    /**
     * @param $idwebsite
     * @return bool|null
     */
    public function isAdmin($idwebsite): ?bool // faux retourne seulement que le website fait parti de ses spw mais n'indique pas si il en est admin
    {
        foreach ($this->dispatch->getSpwsite() as $spw) {
            /** @var Spwsite $spw */
            if ($spw->getWebsite()->getId() == $idwebsite) {
                return $spw->getIsadmin();
            }
        }
        return false;
    }


    /**
     * @param $pws
     */
    public function initStatut($pws)
    {
        foreach ($pws as $spw) {
            /** @var Spwsite $spw */
            if ($spw->getDisptachwebsite()->getId() == $this->iddispatch) {
                $this->admin = $spw->isSuper();
            }
        }
    }

    /**
     * @param $pws
     */
    public function clear($pws)
    {
        foreach ($pws as $spw) {
            /** @var Spwsite $spw */
            if ($spw->getDisptachwebsite()->getId() == $this->iddispatch) {
                $this->admin = $spw->isSuper();
            }
        }
    }

    public function clearinit(){
        $this->requestStack->getSession()->remove('city');
        $this->requestStack->getSession()->remove('idcustomer');
        $this->requestStack->getSession()->remove('lat');
        $this->requestStack->getSession()->remove('lon');
        $this->requestStack->getSession()->remove('iddisptachweb');
        $this->requestStack->getSession()->remove('permission');
        $this->requestStack->getSession()->remove('idcity');
    }

    /**
     * @param $pws
     */
    public function initMember($pws)
    {
        foreach ($pws as $spw) {
            /** @var Spwsite $spw */
            if ($spw->getDisptachwebsite()->getId() == $this->iddispatch) {
                $this->pw=$spw;
                $this->admin = $spw->isSuper();
                $this->memberwb=true;
            }
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws RedirectException
     */
    public function reClearInit(){
        $this->requestStack->getSession()->remove('city');
        $this->requestStack->getSession()->remove('idcity');
        $this->requestStack->getSession()->remove('idcustomer');
        $this->requestStack->getSession()->remove('lat');
        $this->requestStack->getSession()->remove('lon');
        $this->requestStack->getSession()->remove('iddisptachweb');
        $this->requestStack->getSession()->remove('permission');
        $this->sessioninit->initCustomer($this->security->getUser());
    }

}