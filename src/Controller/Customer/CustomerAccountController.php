<?php


namespace App\Controller\Customer;

use App\Classe\affisession;
use App\Entity\Admin\Orders;
use App\Entity\Admin\WbOrderProducts;
use App\Entity\Admin\Wborders;
use App\Form\SubmitOrderType;
use App\Form\WbOrderProductType;
use App\Form\WOrderType;
use App\Lib\Links;
use App\Repository\Entity\FacturesCustomerRepository;
use App\Repository\Entity\FacturesRepository;
use App\Repository\Entity\OrdersRepository;
use App\Repository\Entity\ProductsRepository;
use App\Repository\Entity\SpwsiteRepository;
use App\Repository\Entity\WbordersRepository;
use App\Service\Gestion\AutoCommande;
use App\Service\Gestion\Commandar;
use App\Service\Gestion\Facturator;
use App\Service\Gestion\GetFacture;
use App\Module\Modulator;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customer/account/")
 * @IsGranted("ROLE_CUSTOMER")
 */
class CustomerAccountController extends AbstractController
{
    use affisession;

    /**
     * @Route("listfacturation", name="tab_facture_customer")
     * @param FacturesCustomerRepository $facturesRepository
     * @param SpwsiteRepository $spwsiteRepository
     * @return RedirectResponse|Response
     */
    public function factureCustomer(FacturesRepository $facturesWbRepository,FacturesCustomerRepository $facturesRepository, SpwsiteRepository $spwsiteRepository): RedirectResponse|Response
    {
        if(!$this->dispatch) return $this->redirectToRoute('cargo_public');
        $this->activeBoard();
        $spws=$this->dispatch->getSpwsite();
        $facwb=[];
        foreach ($spws as $pw) {
            $rs = $facturesWbRepository->findFactWebsite($pw->getWebsite()->getWbcustomer());
            foreach ($rs as $r) {
                $facwb[] = $r;
            }
        }

        $facts=$facturesRepository->findFactCustomer($this->dispatch->getCustomer()->getId());

        $vartwig=$this->menuNav->newtemplateControlCustomer(
            Links::CUSTOMER_LIST,
            'gestwebsite',
            'Mes factures',
            3
        );

        return $this->render('aff_account/home.html.twig', [
            'directory'=>"gestion",
            'replacejs'=>$replace??null,
            'dispatch'=>$this->dispatch,
            'board'=>$this->board,
            'vartwig'=>$vartwig,
            'permissions'=>$this->permission,
            'facts'=>array_merge($facts,$facwb)
        ]);

    }

    /**
     * @Route("showfacturecustomer/{id}", name="show_facture_customer")
     * @param $id
     * @param FacturesRepository $facturesRepository
     * @param GetFacture $getFacture
     * @return RedirectResponse|void
     * @throws NonUniqueResultException
     */
    public function showFactureCustomer($id, FacturesRepository $facturesRepository, GetFacture $getFacture)
    {
        if(!$this->dispatch) return $this->redirectToRoute('cargo_public');


        // todo a faire pour acces facture website
        /*
            $website=$pw->getWebsite();
            if(!$this->admin){
            $this->addFlash('info', "vous n'avez pas les droits pour acc??der ?? cette rubrique");
            return $this->redirectToRoute('tab_spaceweb',[
                'id'=>$website->getId()]);}
        */

        $facture=$facturesRepository->findOneFacture($id);
        $dompdf=$getFacture->showpdffactureCustomer($facture,$facture->getOrders(),$this->dispatch);
        $dompdf -> stream ();
    }




    /**
     * @Route("customer-wbsite", name="customer_list_wbsite")
     * @param $id
     * @return Response
     */
    public function tabwbsiteBo( $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CUSTOMER');

        $vartwig=$this->dispatch->templateControl(
            Links::CUSTOMER_LIST,
            'website',
            "website",
            'all');

        return $this->render('aff_master/home.html.twig', [
            'directory'=>'website',
            'website'=>$this->dispatch->getSpwsite(),
            'customer'=>$this->dispatch,
            'vartwig'=>$vartwig
        ]);
    }


    /**
     * @Route("new-commande-customer/{md}", name="new_cmd_customer")
     * @param $md
     * @param Request $request
     * @param AutoCommande $autoCommande
     * @return Response
     * @throws NonUniqueResultException
     */
    public function newCmdCustomer($md, Request $request, AutoCommande $autoCommande): Response //todo faire mieux cote template !!!!!
    {

        if(!$dispatch=$this->dispatch) return $this->redirectToRoute('cargo_public');
        $this->activeBoard();
        $client=$dispatch->getCustomer()->getNumclient();
        $services=$dispatch->getCustomer()->getServices();
        foreach ($services as $service){
            if($service->getNamemodule()==$md) return $this->redirectToRoute("list_module_customer");
        }

        $order =New Orders();
        $form = $this->createForm(SubmitOrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $autoCommande->newCmdModule($client,$order,$md);
            return $this->redirectToRoute("show_cmd_customer",['id'=>$order->getId()]);
        }
        $vartwig=$this->menuNav->templatingadmin(
            'cmdModule',
            "commande module",
            $this->board,1);

        return $this->render('aff_account/home.html.twig', [
            'directory'=>'gestion',
            'replacejs'=>$replace??null,
            'form' => $form->createView(),
            'board'=>$this->board,
            'dispatch'=>$dispatch,
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("show-cmd-customer/{id}", name="show_cmd_customer")
     * @param $id
     * @param OrdersRepository $ordersRepository
     * @param Commandar $commandar
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showCmdCustomer($id, OrdersRepository $ordersRepository,Commandar $commandar): Response
    {
        if(!$dispatch=$this->dispatch) return $this->redirectToRoute('cargo_public');
        $order=$ordersRepository->findAllcmd($id);

        $this->activeBoard();
        $order=$commandar->calCmd($order);
        $vartwig=$this->menuNav->templatingadmin(
            'commandedesk',
            "commandedesk",
            $this->board,1);

        return $this->render('aff_account/home.html.twig', [
            'directory'=>'gestion',
            'replacejs'=>$replace??null,
            'customer'=>$dispatch->getCustomer(),
            'order'=> $order,
            'board'=>$this->board,
            'client'=>$order->getNumclient()->getIdcustomer(),
            'dispatch' =>$dispatch,
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("validate-cmd-module/{id}", name="validate_cmd_module")
     * @param $id
     * @param Facturator $facturator
     * @param OrdersRepository $ordersRepository
     * @param Modulator $modulator
     * @return Response
     * @throws NonUniqueResultException
     */
    public function validateCmdModule($id,Facturator $facturator, OrdersRepository $ordersRepository, Modulator $modulator): Response
    {
        if(!$dispatch=$this->dispatch) return $this->redirectToRoute('cargo_public');
        $order=$ordersRepository->findAllcmd($id);
        if(!$order)throw new \Exception('order non valide');
        $this->activeBoard();
        /* todo a voir avant de valier message "pour votre facture merci de renseigner votre compte personnel"
        $adresse=$website->getTemplate()->getSector()->getAdresse();
        if(count($adresse)==0){
            $this->addFlash(
                'notice',
                'commande non trouv??e ou information client pas suffisnat pour ??tablir la facture'
            );
            return $this->redirectToRoute('profil_customer');
        }
        */
        $dompdf=$facturator->newFactureCustomer($order);

        $modulator->addService($order);


        // todo prevoir un message qui propose d'activer ce service sur les board de l'utilisateur

        return $this->redirectToRoute('list_module_customer');
    }

    /**
     * @Route("/board/mes-avantages", name="avantage")
     * @return Response
     */
    public function avantageCustomer(): Response
    {
        $vartwig=$this->dispatch->templateControlCustomer(
            Links::CUSTOMER_LIST,
            "avantage",
            "customer");

        return $this->render('backoffice/home.html.twig', [
            'agent'=>$this->useragent,
            'vartwig'=>$vartwig,

        ]);
    }


    /*---------------------------  je sais pas si c'est actif ou pas ???? -------------------------------*/

    /**
     * @Route("edit-commande-customer/{id}", name="edit_commande_customer")
     * @param $id
     * @param Request $request
     * @param Commandar $commandar
     * @param WbordersRepository $wbordersRepository
     * @param ProductsRepository $productsRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function editCommandCustomer($id, Request $request, Commandar $commandar, WbordersRepository $wbordersRepository, ProductsRepository $productsRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CUSTOMER');
        /** @var Wborders $order */
        $order=$wbordersRepository->findAllOrder($id);
        $wbcli=$order->getWbcustomer();
        $website=$wbcli->getWebsite();
        $form = $this->createForm(WOrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commandar->editPrestaFree($order, $wbcli);
            return $this->redirectToRoute("back_admin_gest_wbsite",['id'=>$website->getId()]);
        }
        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'Wadorder',
            "Wadorder",
            'all');

        return $this->render('aff_master/home.html.twig', [
            'directory'=>'orders',
            'form' => $form->createView(),
            'website'=>$website,
            'customer'=>$this->dispatch,
            'vartwig'=>$vartwig
        ]);
    }




    /**
     * @Route("active-module-gestioncustomer/{id}", name="active_customer_cmd_module-gestion")
     * @param $id
     * @param Request $request
     * @param Commandar $commandar
     * @param ProductsRepository $productsRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function newBlokgestionCustomer($id, Request $request, Commandar $commandar, ProductsRepository $productsRepository ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CUSTOMER');
        $website=$this->wbrepo->findForCmdById($id);
        $wbcli=$website->getWbcustomer();
        $orderprod=New WbOrderProducts();
        $form = $this->createForm(WbOrderProductType::class, $orderprod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $prod=$productsRepository->find(4);  // forfait 12 mois
            $order=$commandar->addprestaAffi($wbcli,$orderprod,$prod);
            return $this->redirectToRoute("back_admin_gest_wbsite",['id'=>$website->getId()]);
        }
        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'addgestionorder',
            "addgestionorder",
            'all');
        return $this->render('aff_master/home.html.twig', [
            'directory'=>'orders',
            'form' => $form->createView(),
            'website'=>$website,
            'customer'=>$this->dispatch,
            'vartwig'=>$vartwig
        ]);
    }


    /**
     * @Route("active-customer-cmd-module-show-commande-desk/{id}", name="active_customer_show_commande-desk")
     * @param $id
     * @param WbordersRepository $wbordersRepository
     * @param Commandar $commandar
     * @param Facturator $facturator
     * @param GetFacture $getFacture
     * @param SpwsiteRepository $spwsiteRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function viewCommandedeskCustomer($id, WbordersRepository $wbordersRepository,Commandar $commandar, Facturator $facturator, GetFacture $getFacture, SpwsiteRepository $spwsiteRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CUSTOMER');
        /** @var Wborders $order */
        $order=$wbordersRepository->findAllOrderForCoammande($id);
        $spw=$spwsiteRepository->findadminwebsite($order->getWbcustomer()->getWebsite());
        $dispatch=$spw[0]->getDisptachwebsite();
        $website=$order->getWbcustomer()->getWebsite();
        $order=$commandar->calCmdWb($order);
        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'commandedesk',
            "commandedesk",
            'all');


        return $this->render('aff_master/home.html.twig', [
            'directory'=>'orders',
            'website'=>$website,
            'customer'=>$this->dispatch,
            'order'=> $order,
            'dispatch' =>$dispatch,
            'vartwig'=>$vartwig
        ]);
    }
}