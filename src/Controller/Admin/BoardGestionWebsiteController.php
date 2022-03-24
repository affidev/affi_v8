<?php


namespace App\Controller\Admin;


use App\Classe\adminsession;
use App\Entity\Admin\WbOrderProducts;
use App\Entity\Admin\Wborders;
use App\Form\WbOrderProductType;
use App\Form\WOrderType;
use App\Lib\Links;
use App\Repository\Entity\ProductsRepository;
use App\Repository\Entity\SpwsiteRepository;
use App\Repository\Entity\WbordersRepository;
use App\Service\Gestion\Commandar;
use App\Service\Gestion\Facturator;
use App\Service\Gestion\GetFacture;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/board-v5-1/gest-wbsite/")
 * @IsGranted("ROLE_SUPER_ADMIN")
 */

class BoardGestionWebsiteController extends AbstractController
{
    use adminsession;

    /**
     * @Route("wbsite/{id}", name="back_admin_gest_wbsite")
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function tabwbsiteBo( $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $website=$this->wbrepo->findWebsiteAdmin($id);

        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'website',
            "website",
            'all');

        return $this->render('aff_master/home.html.twig', [
            'directory'=>'website',
            'website'=>$website,
            'replacejs'=>$replace??null,
            'customer'=>$this->dispatch,
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("wbsite-liste-commandes/{id}", name="back_admin_wbsite_commande_list")
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function listCommandeWebsiteAdmin( $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $website=$this->wbrepo->findWebsiteAdmin($id);

        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'listcommande',
            "website",
            'all');

        return $this->render('aff_master/home.html.twig', [
            'directory'=>'website',
            'website'=>$website,
            'replacejs'=>$replace??null,
            'customer'=>$this->dispatch,
            'vartwig'=>$vartwig
        ]);
    }


    /**
     * @Route("cmd-wbsite/{id}", name="back_admin_cmd_wbsite")
     * @param $id
     * @param Request $request
     * @param Commandar $commandar
     * @return Response
     * @throws NonUniqueResultException
     */
    public function newCmdwbsite($id, Request $request, Commandar $commandar): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $website=$this->wbrepo->findForCmdById($id);
        $order =New Wborders();
        $form = $this->createForm(WOrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commandar->addprestaFreeAffi($order, $website->getWbcustomer());
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
            'replacejs'=>$replace??null,
            'customer'=>$this->dispatch,
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("edit-command/{id}", name="edit_command")
     * @param $id
     * @param Request $request
     * @param Commandar $commandar
     * @param WbordersRepository $wbordersRepository
     * @param ProductsRepository $productsRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function editCommand($id, Request $request, Commandar $commandar, WbordersRepository $wbordersRepository, ProductsRepository $productsRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
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
            'replacejs'=>$replace??null,
            'customer'=>$this->dispatch,
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("cmd-module-gestionWb/{id}", name="back_admin_cmd_module-gestionWb")
     * @param $id
     * @param Request $request
     * @param Commandar $commandar
     * @param ProductsRepository $productsRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function newBlokgestionWb($id, Request $request, Commandar $commandar, ProductsRepository $productsRepository ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
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
            'replacejs'=>$replace??null,
            'customer'=>$this->dispatch,
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("cmd-module-coamine/{id}", name="back_admin_cmd_module-domaine")
     * @param $id
     * @param Request $request
     * @param Commandar $commandar
     * @param ProductsRepository $productsRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function newBlokDomaine($id, Request $request, Commandar $commandar, ProductsRepository $productsRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $website=$this->wbrepo->findForCmdById($id);
        $wbcli=$website->getWbcustomer();
        $orderprod=New WbOrderProducts();
        $form = $this->createForm(WbOrderProductType::class, $orderprod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $prod=$productsRepository->find(4);
            $order=$commandar->addprestaAffi($wbcli,$orderprod,$prod);
            return $this->redirectToRoute("back_admin_gest_wbsite",['id'=>$website->getId()]);
        }
        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'adddomaineorder',
            "adddomaineorder",
            'all');
        return $this->render('aff_master/home.html.twig', [
            'directory'=>'orders',
            'form' => $form->createView(),
            'website'=>$website,
            'replacejs'=>$replace??null,
            'customer'=>$this->dispatch,
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("cmd-module-adword/{id}", name="back_admin_cmd_module-adword")
     * @param $id
     * @param Request $request
     * @param Commandar $commandar
     * @param ProductsRepository $productsRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function newBlokadword($id, Request $request, Commandar $commandar,ProductsRepository $productsRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $website=$this->wbrepo->findForCmdById($id);
        $wbcli=$website->getWbcustomer();
        $orderprod=New WbOrderProducts();
        $form = $this->createForm(WbOrderProductType::class, $orderprod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $prod=$productsRepository->find(4);
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
            'replacejs'=>$replace??null,
            'customer'=>$this->dispatch,
            'vartwig'=>$vartwig
        ]);
    }



    /**
     * @Route("cmd-module-show-commande-desk/{id}", name="back_admin_show_commande-desk")
     * @param $id
     * @param WbordersRepository $wbordersRepository
     * @param Commandar $commandar
     * @param Facturator $facturator
     * @param GetFacture $getFacture
     * @param SpwsiteRepository $spwsiteRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function viewCommandedesk($id, WbordersRepository $wbordersRepository,Commandar $commandar, Facturator $facturator, GetFacture $getFacture, SpwsiteRepository $spwsiteRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
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
            'replacejs'=>$replace??null,
            'dispatch' =>$dispatch,
            'vartwig'=>$vartwig
        ]);
    }
}