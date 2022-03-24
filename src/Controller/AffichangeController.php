<?php


namespace App\Controller;


use App\Classe\affisession;
use App\Entity\Sector\Gps;
use App\Lib\Links;
use App\Service\Localisation\LocalisationServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AffichangeController extends AbstractController
{
    use affisession;


    /**
     * @Route("public/afficher-sur/{slugcity?}", options={"expose"=true},  name="public_to_affiche")
     * @param $slugcity
     * @param LocalisationServices $locateService
     * @return Response
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function toAffichange( $slugcity, LocalisationServices $locateService): Response
    {
        if($this->iddispatch) return $this->redirectToRoute('//todo');

        /** @var Gps $locate */
        if($slugcity){
            $locate=$locateService->findLocate($slugcity);
        }elseif($this->requestStack->getSession()->has('city')){
            $city =$this->requestStack->getSession()->get('city');
            $locate=$locateService->findLocate($city);
            if(!$locate){  // todo je le laisse pour l'instant e attente de verif si utile et si la recherche par city sur slugcity est ok
                $this->locate=['lat'=>floatval($this->requestStack->getSession()->get('lat')),'lon'=>floatval($this->requestStack->getSession()->get('long'))];
                $locate = $locateService->findLocateByCenter($this->locate);
            }
            if(!$locate){
                $city = null;
                $locate=null;
            }
        }else {
            $city = null;
            $locate=$locateService->findLocate("affichange");
        }

        $vartwig=$this->menuNav->templateControl(
            Links::PUBLIC,
            'became_affichanger',
            "AffiChanGe, web local",
            'all');

        return $this->render('public/home.html.twig', [
            'locate'=>$locate,
            'replacejs'=>false,
            'city'=>$city??null,
            'directory'=>"page/toaffiche",
            'vartwig'=>$vartwig,
        ]);
    }

    /**
     * @Route("public/buller-sur/{slugcity?}", options={"expose"=true},  name="public_to_bulle")
     * @param $slugcity
     * @param LocalisationServices $locateService
     * @return Response
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function tobuller( $slugcity, LocalisationServices $locateService): Response
    {
        if($this->iddispatch) return $this->redirectToRoute('//todo');

        /** @var Gps $locate */
        if($slugcity){
            $locate=$locateService->findLocate($slugcity);
            $city = $locate->getSlugcity();
        }elseif($this->requestStack->getSession()->has('city')){
            $city =$this->requestStack->getSession()->get('city');
            $locate=$locateService->findLocate($city);
            if(!$locate){  // todo je le laisse pour l'instant e attente de verif si utile et si la recherche par city sur slugcity est ok
                $this->locate=['lat'=>floatval($this->requestStack->getSession()->get('lat')),'lon'=>floatval($this->requestStack->getSession()->get('long'))];
                $locate = $locateService->findLocateByCenter($this->locate);
            }
            if(!$locate){
                $city = null;
                $locate=null;
            }
        }else {
            $city = null;
            $locate=$locateService->findLocate("affichange");
        }

        $vartwig=$this->menuNav->templateControl(
            Links::PUBLIC,
            'became',
            "AffiChanGe, web local",
            'all');

        return $this->render('aff_public/home.html.twig', [
            'replacejs'=>$replace??null,
            'locate'=>$locate,
            'city'=>$city,
            'directory'=>"page/toaffiche",
            'vartwig'=>$vartwig,
        ]);
    }

    /**
     * @Route("/affiche/nouveau-panneau/", name="public_new_panneau") // todo pour mettre un lien sur page public
     * @return RedirectResponse
     */
    public function newPanneau(): RedirectResponse
    {
        if(!$this->dispatch){
            return $this->redirectToRoute('identify');
        }else{
            return $this->redirectToRoute('list_website');
        }

    }

}