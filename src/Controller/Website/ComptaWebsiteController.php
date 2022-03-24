<?php


namespace App\Controller\Website;

use App\Classe\affisession;
use App\Entity\Admin\Factures;
use App\Entity\DispatchSpace\Spwsite;
use App\Entity\Websites\Website;
use App\Repository\Entity\FacturesRepository;
use App\Service\Gestion\GetFacture;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/web/admin/")
 * @IsGranted("ROLE_CUSTOMER")
 */

class ComptaWebsiteController extends AbstractController
{
    use affisession;

    /**
     * @Route("gestion/{id}", options={"expose"=true}, name="tab_compta")
     * @param FacturesRepository $facturesRepository
     * @param $id
     * @return RedirectResponse|Response
     */
    public function websiteCompta(FacturesRepository $facturesRepository, $id): RedirectResponse|Response
    {
        if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin )$this->redirectToRoute('cargo_public');
        $facts=$facturesRepository->findFactWebsite($id);


        $vartwig=$this->menuNav->templatingspaceWeb(
            'admin/gestwebsite',
            'Compte',
            $this->website);

        return $this->render('aff_websiteadmin/home.html.twig', [
            'vartwig' => $vartwig,
            'website'=>$this->website,
            'iddispath'=>$this->iddispatch,
            'facts'=>$facts,
            'directory'=>'member',
            'admin'=>[$this->admin,$this->permission]
        ]);

    }

    /**
     * @Route("show-facture/{id}", name="show_facture")
     * @param $id
     * @param FacturesRepository $facturesRepository
     * @param GetFacture $getFacture
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showFacture($id, FacturesRepository $facturesRepository, GetFacture $getFacture){
        /** @var Spwsite $pw */
        $pw=$this->getUserspwsiteOfWebsite($id);
        /** @var Website $website */
        $website=$pw->getWebsite();
        if(!$this->admin){
            $this->addFlash('info', "vous n'avez pas les droits pour accéder à cette rubrique");
            return $this->redirectToRoute('tab_spaceweb',[
                'id'=>$website->getId()]);

        }
        /** @var Factures $facture */
        $facture=$facturesRepository->findOneFacture($id);
        $dompdf=$getFacture->showpdffacture($facture,$facture->getOrders(),$pw->getDisptachwebsite());
        $dompdf -> stream ();
    }
}