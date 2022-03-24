<?php


namespace App\Controller\Sales;


use App\Classe\adminsession;
use App\Entity\Admin\Wborders;
use App\Lib\Links;
use App\Repository\Entity\SpwsiteRepository;
use App\Repository\Entity\WbordersRepository;
use App\Service\Gestion\Commandar;
use App\Service\Gestion\Facturator;
use App\Service\Gestion\GetFacture;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order/factured/customer/")
 * @IsGranted("ROLE_CUSTOMER")
 */


class FacturedCustomerController extends AbstractController{

    use adminsession;

    /**
     * @Route("create-facturation/{id}", name="customer_create_facture")
     * @param $id
     * @param GetFacture $getFacture
     * @param WbordersRepository $wbordersRepository
     * @param Facturator $facturator
     * @param SpwsiteRepository $spwsiteRepository
     * @return RedirectResponse
     * @throws NonUniqueResultException
     */
        public function facturedOrderWebsite( $id, GetFacture $getFacture, WbordersRepository $wbordersRepository, Facturator $facturator, SpwsiteRepository $spwsiteRepository): RedirectResponse
        {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        /** @var Wborders $order */
        $order=$wbordersRepository->findAllOrderForCoammande($id);

        $spw=$spwsiteRepository->findadminwebsite($order->getWbcustomer()->getWebsite());
        $dispatch=$spw[0]->getDisptachwebsite();
        $website=$order->getWbcustomer()->getWebsite();
        $adresse=$website->getTemplate()->getSector()->getAdresse();
        if(!$order || count($adresse)==0){
            $this->addFlash(
                'notice',
                'commande non trouvée ou information client pas suffisnat pour établir la facture'
            );
          return $this->redirectToRoute("back_admin_gest_wbsite",['id'=>$website->getId()]);
        }
        $dompdf=$facturator->newFacture($order,$dispatch);
        return $dompdf -> stream ();
    }

    /**
     * @Route("show-commande/{id}", name="customer_show_commande")
     * @param $id
     * @param WbordersRepository $wbordersRepository
     * @param Commandar $commandar
     * @param Facturator $facturator
     * @param GetFacture $getFacture
     * @param SpwsiteRepository $spwsiteRepository
     * @return void
     * @throws NonUniqueResultException
     */
    public function viewCommande($id, WbordersRepository $wbordersRepository,Commandar $commandar, Facturator $facturator, GetFacture $getFacture, SpwsiteRepository $spwsiteRepository){
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        /** @var Wborders $order */
        $order=$wbordersRepository->findAllOrderForCoammande($id);
        $website=$order->getWbcustomer()->getWebsite();
        $spw=$spwsiteRepository->findadminwebsite($website);
        $dispatch=$spw[0]->getDisptachwebsite();
        $order=$commandar->calCmdWb($order);
        $dompdf=$getFacture->newpdfcmd($order,$dispatch);
        $dompdf -> stream ();
    }

    /**
     * @Route("show-commande-desk/{id}", name="customer_show_commande-desk")
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

        $vartwig=$this->dispatch->templateControl(
            null,
            Links::CUSTOMER_LIST,
            'view/commandedesk3', //todo ici test nouvlle page pour pdf
            "view/commandedesk",
            'all');

        return $this->render('backoffice/master/orders/pdfdesk.html.twig', [
            'directory'=>'master/orders',
            'website'=>$website,
            'customer'=>$this->userdispatch,
            'order'=> $order,
            'dispatch' =>$dispatch,
            'vartwig'=>$vartwig
        ]);
    }

}