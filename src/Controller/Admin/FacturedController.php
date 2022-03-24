<?php


namespace App\Controller\Admin;


use App\Classe\adminsession;
use App\Entity\Admin\Wborders;
use App\Lib\Links;
use App\Repository\Entity\FacturesRepository;
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
 * @Route("/board-v5-1/gest-wbsite/")
 * @IsGranted("ROLE_SUPER_ADMIN")
 */


class FacturedController extends AbstractController{

    use adminsession;

    /**
     * @Route("admin-gestion-facturation/{id}", name="back_admin_gest_factured")
     * @param $id
     * @param WbordersRepository $wbordersRepository
     * @param Facturator $facturator
     * @param SpwsiteRepository $spwsiteRepository
     * @throws NonUniqueResultException
     */
        public function facturedOrderWebsite( $id, WbordersRepository $wbordersRepository, Facturator $facturator, SpwsiteRepository $spwsiteRepository)
        {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        /** @var Wborders $order */
        $order=$wbordersRepository->findAllOrderForCoammande($id);
            if(!$order){
                $this->addFlash(
                    'notice',
                    'commande non trouvée'
                );
                return $this->redirectToRoute("back_admin");
            }
        $spw=$spwsiteRepository->findadminwebsite($order->getWbcustomer()->getWebsite());
        $dispatch=$spw[0]->getDisptachwebsite();
        $website=$order->getWbcustomer()->getWebsite();
        $adresse=$website->getTemplate()->getSector()->getAdresse();
        if(count($adresse)==0){
            $this->addFlash(
                'notice',
                'commande non trouvée ou information client pas suffisnat pour établir la facture'
            );
          return $this->redirectToRoute("back_admin_gest_wbsite",['id'=>$website->getId()]);
        }
        $dompdf=$facturator->newFacture($order,$dispatch);
        return $dompdf->stream();
    }

    /**
     * @Route("admin-gestion-refacturation/{id}", name="back_admin_gest_refactured")
     * @param $id
     * @param FacturesRepository $facturesRepository
     * @param WbordersRepository $wbordersRepository
     * @param Facturator $facturator
     * @param SpwsiteRepository $spwsiteRepository
     * @return RedirectResponse
     * @throws NonUniqueResultException
     */
    public function reEditFacturedOrderWebsite($id, FacturesRepository $facturesRepository,  WbordersRepository $wbordersRepository, Facturator $facturator, SpwsiteRepository $spwsiteRepository): RedirectResponse
    {
        $order=$wbordersRepository->findAllOrderForCoammande($id);
        $facture=$facturesRepository->findByCommand($id);
        $spw=$spwsiteRepository->findadminwebsite($order->getWbcustomer()->getWebsite());
        $dispatch=$spw[0]->getDisptachwebsite();
        $website=$order->getWbcustomer()->getWebsite();
        $adresse=$website->getTemplate()->getSector()->getAdresse();
        if( count($adresse)==0){
            $this->addFlash(
                'notice',
                'commande non trouvée ou information client pas suffisante pour établir la facture'
            );
            return $this->redirectToRoute("back_admin_gest_wbsite",['id'=>$website->getId()]);
        }
        $dompdf=$facturator->replaceFacture($order,$dispatch, $facture);
        return $dompdf->stream();
    }

    /**
     * @Route("cmd-module-show-commande/{id}", name="back_admin_show_commande")
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
            'view/commandedesk3', //todo ici test nouvlle page pour pdf
            "view/commandedesk",
            'all');

        return $this->render('aff_master/orders/pdfdesk.html.twig', [
            'directory'=>'orders',
            'website'=>$website,
            'customer'=>$this->dispatch,
            'order'=> $order,
            'dispatch' =>$dispatch,
            'vartwig'=>$vartwig
        ]);
    }

}