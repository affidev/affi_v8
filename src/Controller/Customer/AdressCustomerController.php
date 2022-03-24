<?php

namespace App\Controller\Customer;

use App\Classe\affisession;
use App\Entity\Sector\Adresses;
use App\Lib\Links;
use App\Lib\MsgAjax;
use App\Repository\Entity\AdressesRepository;
use App\Service\Localisation\LocalisationServices;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/customer/profil/adress/")
 */
class AdressCustomerController extends AbstractController
{
    use affisession;


    /**
     * @Route("choice-adresse-fact-customer", name="edit_adress")
     * @param AdressesRepository $adressesRepository
     * @return Response
     */
    public function editAdress(AdressesRepository $adressesRepository): Response
    {
        if(!$dispatch=$this->dispatch) return $this->redirectToRoute('cargo_public');
        $this->activeBoard();

        if($dispatch->getSector()) $adresses=$adressesRepository->findAllAdresses($dispatch->getSector()->getId());



        $vartwig=$this->menuNav->newtemplateControlCustomer(
            Links::CUSTOMER_LIST,
            'editadress',
            "Mon adresse",
            5  );

        return $this->render('aff_account/home.html.twig', [
            'directory'=>"profil",
            'replacejs'=>$replacejs??null,
            'twigform'=>'changeadresse',
            'dispatch'=>$dispatch,
            'board'=>$this->board,
            'customer'=> $dispatch->getCustomer(),
            'identity'=>$dispatch->getCustomer()->getProfil(),
            'adresses'=>$adresses??[],
            'vartwig'=>$vartwig,

        ]);
    }

    /**
     * @Route("newadress", options={"expose"=true}, name="newadress_customer", methods={"POST"})
     * @IsGranted("ROLE_CUSTOMER")
     * @param Request $request
     * @param LocalisationServices $localisation
     * @return JsonResponse
     */
    public function newAdressSpaceweb(Request $request, LocalisationServices $localisation): JsonResponse
    {

        if($request->isXmlHttpRequest())
        {
            if(!$this->dispatch) return new JsonResponse(MsgAjax::MSG_ERRORRQ);
            $data = json_decode((string) $request->getContent(), true);
            $adress=$localisation->newAdressDispatch($data,  $this->dispatch, true);
            if($adress!=null){
                $this->em->persist($this->dispatch);
                $this->em->flush();
                $responseCode = 200;
                http_response_code($responseCode);
                header('Content-Type: application/json');
                return new JsonResponse(['success'=>true, "label"=>$data['properties']['label']]);
            }
            return new JsonResponse(['success'=>false,"error"=>"adresse pas enregistrée"]);
        }
        return new JsonResponse(['success'=>false,"error"=>"requete erreur"]);
    }

    /**
     * @Route("deleteadress/{id}", options={"expose"=true}, name="deleteadress_customer", methods={"DELETE"})
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function deleteAdressSpaceweb(Request $request, $id): JsonResponse
    {
        if(!$this->dispatch) return new JsonResponse(['success'=>false,'error'=>'merdum ici']);
        if($request->isXmlHttpRequest())
        {
            $adresses=$this->dispatch->getSector()->getAdresse();
            /** @var Adresses $adress */
            foreach ($adresses as $adress) {
                if ($adress->getId() == $id) {
                    $this->em->remove($adress);
                    $this->em->flush();
                    $responseCode = 200;
                    http_response_code($responseCode);
                    header('Content-Type: application/json');
                    return new JsonResponse(['success'=>true]);
                }
            }
        }
        return new JsonResponse(['success'=>false,"error"=>"requete aax non reconnue"]);
    }

    /**
     * @Route("deleteadresscustomer/{id}", name="delete_adress_custo_fac")
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAdresscustomer(Request $request, $id): RedirectResponse
    {
        if(!$dispatch=$this->dispatch) return $this->redirectToRoute('cargo_public');
        $this->activeBoard();

            $adresses=$this->dispatch->getSector()->getAdresse();
            /** @var Adresses $adress */
            foreach ($adresses as $adress) {
                if ($adress->getId() == $id) {
                    $this->em->remove($adress);
                    $this->em->flush();
                }
            }

        return $this->redirectToRoute('edit_adress');
    }

}