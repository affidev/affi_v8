<?php


namespace App\Controller\Admin;


use App\Classe\adminsession;
use App\Entity\Customer\Services;
use App\Entity\Module\ModuleList;
use App\Entity\Websites\Website;
use App\Lib\Links;
use App\Repository\Entity\CustomersRepository;
use App\Repository\Entity\DispatchSpaceWebRepository;
use App\Repository\Entity\ModuleListRepository;
use App\Repository\Entity\ProductsRepository;
use App\Repository\Entity\SpwsiteRepository;
use App\Repository\Entity\WbordersRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Service\backoffice\Initbacktor;
use App\Service\Messages\Messageor;
use App\Util\DefaultModules;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/board-affilink/")
 * @IsGranted("ROLE_SUPER_ADMIN")
 */

class BoardController extends AbstractController // board-v5-1/back-admin/?keyboard=v5-12020test
{

    use adminsession;

    /**
     * @Route("back-admin/{keyboard}", name="back_admin")
     * @param $keyboard
     * @param SpwsiteRepository $spwsiteRepository
     * @param Initbacktor $initbacktor
     * @param WebsiteRepository $websiteRepository
     * @param Messageor $messageor
     * @param DispatchSpaceWebRepository $dispatchSpaceWebRepository
     * @return Response
     */
    public function backOffice($keyboard, SpwsiteRepository $spwsiteRepository, Initbacktor $initbacktor, WebsiteRepository $websiteRepository,Messageor $messageor, DispatchSpaceWebRepository $dispatchSpaceWebRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        if($keyboard!=$this->getParameter('key_admin')) $this->redirectToRoute('app_logout');
/*
        if($this->admin->getModule()->getContactation()){
            $msgs=$messageor->messwebsite($this->getadminWebsite(),$this->getadminPwsite());
        }else{
            $msgs=[];
        }
*/
        $initback['fact']=$initbacktor->init();
        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'boardadmin',
            "boardadmin",
            'all');

        return $this->render('aff_master/home.html.twig', [
            'directory'=>'admin',
            'init'=>$initback,
            'website'=>true,
            'customer'=>$this->dispatch,
            'msgs'=>$msgs??[],
            'admin'=>[true],
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("back-admin/maj-customer/function", name="back_admin_maj_customer")
     * @return Response
     */
    public function majCustomers(): Response
    {
        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'functions',
            "functions",
            'all');

        return $this->render('aff_master/home.html.twig', [
            'directory'=>'customer',
            'website'=>true,
            'customer'=>$this->dispatch,
            'admin'=>[true],
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("back-admin/maj-customer/maj-services", name="back_admin_maj_services")
     * @param EntityManagerInterface $em
     * @param CustomersRepository $customersRepository
     * @param ProductsRepository $productsRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function majServicesCustomer(EntityManagerInterface $em, CustomersRepository $customersRepository, ProductsRepository $productsRepository): Response
    {

        $customers=$customersRepository->findAll();
        dump($customers);
        $listcustomer=[];

        foreach ($customers as $customer){
            $tabService = [];

            $listservices=$customer->getServices();
            if($listservices) {
                foreach ($listservices as $servicelist) {
                    $tavservice[] = $servicelist->getNamemodule();
                }
            }

            foreach (DefaultModules::MODULE_LIST as $list){
                if(!in_array($list,$tabService)){
                    $service= new Services();
                    $service->setProducts($productsRepository->findOneProduct($list));
                    $service->setNamemodule($list);
                    $service->setDatestartAt(new DateTime());
                    $customer->addService($service);
                    $em->persist($customer);
                    $em->flush();
                    $listcustomer[] = $customer->getEmailcontact();
                }
            }
        }

        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'tabresults',
            "validation maj",
            'all');

        return $this->render('aff_master/home.html.twig', [
            'directory'=>'customer',
            'admin'=>[true],
            'list'=>$listcustomer,
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("back-admin/maj-customer/maj-lismodule", name="back_admin_maj-lismodule")
     * @param EntityManagerInterface $em
     * @param WebsiteRepository $websiteRepository
     * @param ModuleListRepository $moduleListRepository
     * @param CustomersRepository $customersRepository
     * @param ProductsRepository $productsRepository
     * @return Response
     */
    public function majListModule(EntityManagerInterface $em,WebsiteRepository $websiteRepository, ModuleListRepository $moduleListRepository,CustomersRepository $customersRepository, ProductsRepository $productsRepository): Response
    {
        $websites=$websiteRepository->findAllAndPwAdmin('superadmin');
      $list=[];
        /** @var  Website $website */
        foreach ($websites as $website){
            $tabmodule=[];
           $listmodules = $website->getListmodules();
            /** @var  ModuleList $module */
            foreach ($listmodules as $module){
                $tabmodule[]=$module->getClassmodule();
            }

            $services=$website->getSpwsites()[0]->getDisptachwebsite()->getCustomer()->getServices();
            /** @var  Services $service */
            foreach ($services as $service){
               if(!in_array($service->getNameModule(),$tabmodule)){
                   $module= new ModuleList();
                   $module->setClassmodule($service->getNameModule());
                   $module->setKeymodule($website->getCodesite());
                   $module->setWebsite($website);
                   $website->addListmodule($module);
                   $em->persist($website);
                   $em->flush();
                   $list[]=$website->getNamewebsite();
               }
           }
        }

        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'tabresults',
            "validation maj",
            'all');

        return $this->render('aff_master/home.html.twig', [
            'directory'=>'customer',
            'admin'=>[true],
            'lists'=>$list,
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("back-admin/websites/{keyboard}", name="back_admin_websites")
     * @param $keyboard
     * @return Response
     */
    public function adminWebsite($keyboard): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        if($keyboard!=$this->getParameter('key_admin')) $this->redirectToRoute('app_logout');
        $websites=$this->wbrepo->findAll();

        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'websites',
            "websites",
            'all');

        return $this->render('aff_master/home.html.twig', [
            'customer'=>$this->dispatch,
            'directory'=>'admin',
            'websites'=>$websites,
            'admin'=>[true],
            'vartwig'=>$vartwig
        ]);

    }






    //todo voir tout le reste

    /**
     * @Route("back-admin/product/{keyboard}", name="back_admin_product")
     * @param $keyboard
     * @param SpwsiteRepository $spwsiteRepository
     * @param Initbacktor $initbacktor
     * @param WebsiteRepository $websiteRepository
     * @param ProductsRepository $productsRepository
     * @param DispatchSpaceWebRepository $dispatchSpaceWebRepository
     * @return Response
     */
    public function adminProduct($keyboard, SpwsiteRepository $spwsiteRepository, Initbacktor $initbacktor, WebsiteRepository $websiteRepository,ProductsRepository $productsRepository, DispatchSpaceWebRepository $dispatchSpaceWebRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        if($keyboard!=$this->getParameter('key_admin')) $this->redirectToRoute('app_logout');
        $website=$websiteRepository->find(3);
        $dispatch=$dispatchSpaceWebRepository->find(1);
        $pw=$spwsiteRepository->find(1);
        $initback['fact']=$initbacktor->init();
        $products=$productsRepository->findAll();
        $vartwig=[
            "title"=>'backoffice',
            "description"=>'backoffice',
            "keyword"=>'backoffice',
            'page'=>"products",
            "tagueries"=>[["name"=> "backinfo website"]],
        ];
        return $this->render('aff_master/home.html.twig', [
            'directory'=>'product',
            'init'=>$initback,
            'website'=>$website,
            'pw'>$pw,
            'products'=>$products,
            'dispatch'=>$dispatch,
            'admin'=>[true],
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("back-admin/orders/{keyboard}", name="back_admin_orders")
     * @param $keyboard
     * @param SpwsiteRepository $spwsiteRepository
     * @param Initbacktor $initbacktor
     * @param WebsiteRepository $websiteRepository
     * @param WbordersRepository $ordersRepository
     * @param DispatchSpaceWebRepository $dispatchSpaceWebRepository
     * @return Response
     */
    public function adminOrders($keyboard, SpwsiteRepository $spwsiteRepository, Initbacktor $initbacktor, WebsiteRepository $websiteRepository,WbordersRepository $ordersRepository, DispatchSpaceWebRepository $dispatchSpaceWebRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        if($keyboard!=$this->getParameter('key_admin')) $this->redirectToRoute('app_logout');
       // $website=$websiteRepository->find(3);
       // $dispatch=$dispatchSpaceWebRepository->find(1);
       // $pw=$spwsiteRepository->find(1);
        $initback['fact']=$initbacktor->init();
        $orders=$ordersRepository->byDateOrders();
        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'order',
            "order",
            'all');

        return $this->render('aff_master/home.html.twig', [
            'init'=>$initback,
            'directory'=>'orders',
            'orders'=>$orders,
            'admin'=>[true],
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("back-admin/customers/{keyboard}", name="back_admin_customers") //todo ??? c'est quoi ??
     * @param $keyboard
     * @param SpwsiteRepository $spwsiteRepository
     * @param Initbacktor $initbacktor
     * @param WebsiteRepository $websiteRepository
     * @param CustomersRepository $customersRepository
     * @param DispatchSpaceWebRepository $dispatchSpaceWebRepository
     * @return Response
     */
    public function adminCustomer($keyboard, SpwsiteRepository $spwsiteRepository, Initbacktor $initbacktor, WebsiteRepository $websiteRepository,CustomersRepository $customersRepository, DispatchSpaceWebRepository $dispatchSpaceWebRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        if($keyboard!=$this->getParameter('key_admin')) $this->redirectToRoute('app_logout');
        $website=$websiteRepository->find(3);
        $dispatch=$dispatchSpaceWebRepository->find(1);
        $pw=$spwsiteRepository->find(1);
        $initback['fact']=$initbacktor->init();
        $customers=$customersRepository->findAllCustoAndUserActive();

        $vartwig=[
            "title"=>'backoffice',
            "description"=>'backoffice',
            "keyword"=>'backoffice',
            "tagueries"=>[["name"=> "backinfo website"]],
        ];
        return $this->render('backoffice/customer/customers.html.twig', [
            'init'=>$initback,
            'website'=>$website,
            'pw'>$pw,
            'customers'=>$this->dispatch,
            'dispatch'=>$dispatch,
            'admin'=>[true],
            'vartwig'=>$vartwig
        ]);
    }



}