<?php


namespace App\Controller\Localizer;


use App\Classe\affisession;
use App\Entity\Sector\Adresses;
use App\Repository\Entity\DispatchSpaceWebRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Service\Localisation\LocalisationServices;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @Route("/geolocate/op/")
 */

class LocalizerController extends AbstractController
{
    use affisession;

    /**
     * imputation des adresses à un webspace
     *
     * @Route("localize/{id}", name="spaceweblocalize_init")
     * @IsGranted("ROLE_CUSTOMER")
     * @param WebsiteRepository $websiteRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function localizeWebsite(WebsiteRepository $websiteRepository, $id): Response
    {
        if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin )$this->redirectToRoute('cargo_public');

        $vartwig=$this->menuNav->templatingadmin(
            'localizer',  //main_spaceweb/website/adressWp',
            'parametres du panneau',
            $this->board,3);

            return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'parameters',
            'replacejs'=>false,
            'vartwig' => $vartwig,
            'pw'=>$this->pw,
            'board'=>$this->board,
            'website'=>$this->board,
            'dispatch'=>$this->dispatch,
            'admin'=>[$this->admin,$this->permission],
            'city'=>$this->board->getLocality()->getCity()
        ]);
    }

    /**
     * @Route("newadress", options={"expose"=true}, name="newadress", methods={"POST"})
     * @param Request $request
     * @param LocalisationServices $localisation
     * @param WebsiteRepository $websiteRepository
     * @return JsonResponse
     */
    public function newAdressWebsite(Request $request, LocalisationServices $localisation, WebsiteRepository $websiteRepository): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
            $data = json_decode((string) $request->getContent(), true);
            $website=$websiteRepository->find($data['id']);
            if(!$website) return new JsonResponse(['success'=>false,'error'=>'merdum ici : id =>'.$data['id']]);
            $adress=$localisation->newAdress($data,  $website, 1);
            if($adress!=null){
                $website->setStatut(true);
                $this->em->persist($website);
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
     * @Route("deleteadress/{id}", options={"expose"=true}, name="deleteadress", methods={"DELETE"})
     * @param Request $request
     * @param WebsiteRepository $websiteRepository
     * @param $id
     * @return JsonResponse
     */
    public function deleteAdressWebsite(Request $request, WebsiteRepository $websiteRepository, $id): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
            $idwebsite=$request->request->get('website');
            $website=$websiteRepository->find($idwebsite);
            if(!$website)  return new JsonResponse(['success'=>false,"error"=>"id spaceweb non reconnu"]);
            $adresses=$website->getTemplate()->getSector()->getAdresse();
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
        return new JsonResponse(['success'=>false,"error"=>"requete ajax non reconnue"]);
    }



    //appel ajax pour change locality via bullercity page cargo_public

    /**
     * @Route("newlocate/{city}/{code}", options={"expose"=true}, name="new_locate", methods={"GET"})
     * @param Request $request
     * @param LocalisationServices $localisation
     * @param SerializerInterface $serializer
     * @param null $code
     * @param null $city
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function newLocate(Request $request, LocalisationServices $localisation, SerializerInterface $serializer, $code=null, $city=null): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
            $space=null;
            $gps = $localisation->changeLocate($space, $code,$city);
            if($gps){
                $jasonlocate = $serializer->serialize($gps, 'json');
                $responseCode = 200;
                http_response_code($responseCode);
                header('Content-Type: application/json');
                return new JsonResponse(['success'=>true, "locate"=>$jasonlocate]);
            }
            return new JsonResponse(['success'=>false]);
        }
        return new JsonResponse(['success'=>false]);
    }

    //appel ajax pour init/change locality de dispatch

    /**
     * @Route("newlocatedispatch", options={"expose"=true}, name="new_locate-dispatch", methods={"GET"})
     * @param DispatchSpaceWebRepository $dispatchRepository
     * @param Request $request
     * @param LocalisationServices $localisation
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function newLocateDispatch(DispatchSpaceWebRepository $dispatchRepository,Request $request, LocalisationServices $localisation, SerializerInterface $serializer): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
            $city=$request->query->get("city");
            $code=$request->query->get("code");
            $id=$request->query->get("id");
            if($id!="null"){
                $dispatch=$dispatchRepository->find($id);
            }else{
                $dispatch=null;
            }
            $gps = $localisation->changeLocate($dispatch, $code,$city);
            if($gps){

                $jasonlocate = $serializer->serialize($gps, 'json');
                $responseCode = 200;
                http_response_code($responseCode);
                header('Content-Type: application/json');

                return new JsonResponse(['success'=>true, "locate"=>$jasonlocate]);
            }
            return new JsonResponse(['success'=>false]);
        }
        return new JsonResponse(['success'=>false]);
    }
}