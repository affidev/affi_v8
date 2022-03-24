<?php

namespace App\Controller\MainPublic;

use App\Classe\affisession;
use App\Entity\Sector\Gps;
use App\Entity\Websites\Website;
use App\Lib\Links;
use App\Repository\Entity\PostEventRepository;
use App\Repository\Entity\PostRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Service\Localisation\LocalisationServices;
use App\Service\Search\Listpublications;
use App\Util\DefaultModules;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


class AffichangeController extends AbstractController
{

 use affisession;

    /**
     * @Route("/locate", options={"expose"=true},  name="locate_affi")
     * @param Request $request
     * @param LocalisationServices $locateService
     * @return Response
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function routeAffichange(Request $request,LocalisationServices $locateService): Response
    {
        /** @var Gps $locate */
        $lat=$request->query->get('lat');
        $lon=$request->query->get('lon');
;        if($lat && $lon) {
            $locate = $locateService->defineCity($lat, $lon);
            return $this->redirectToRoute('cargo_public',['slugcity' => $locate->getSlugcity()],302);
        }else{
            return $this->redirectToRoute('cargo_public',[],302);
        }
    }

    /**
     * @Route("/webloc/{city}/{board}",  name="de_locate_affi")
     * @param Request $request
     * @param null $city
     * @param null $board
     * @return Response
     */
    public function derouteAffichange(Request $request,$city=null, $board=null): Response
    {
        return $this->redirectToRoute('cargo_public',[],302);
    }


    /**
     * @Route("{slugcity?}", options={"expose"=true},  name="cargo_public")
     * @param LocalisationServices $locateService
     * @param Listpublications $listpublications
     * @param SluggerInterface $slugger
     * @param null $slugcity
     * @return Response
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function indexAffichange(LocalisationServices $locateService, Listpublications $listpublications,SluggerInterface $slugger, $slugcity=null): Response
    {
        //$bulles=false;

        if ($this->security->isGranted('ROLE_CUSTOMER')) {
            if(!$this->dispatch) throw new Exception('dispatch inconnu');

            $notifdispatch=$this->dispatch->getTbnotifs();

            if($slugcity){
                $locate=$locateService->findLocate($slugcity);
                if(!$locate instanceof Gps) throw new Exception('ville inconnue');
            }else {
                $locate = $this->dispatch->getLocality();
            }

            $this->activeBoard();
            $vartwig=$this->menuNav->templatingspaceWeb(
                'affipublic',
                $this->board->getNamewebsite(),
                $this->board
            );

          //  $bulles=$this->intiTabbulle($locate->getSlugcity(), $vartwig['tabActivities']);

           // $this->intisimplCatchs();

        }else{
            if($slugcity){
                $slugcity = (string) $slugger->slug((string) $slugcity)->lower();
                $locate=$locateService->findLocate($slugcity);
                if(!$locate instanceof Gps) throw $this->createNotFoundException('ville inconnue');
            }else {
                $locate = false;
            }

        $vartwig=$this->menuNav->templateControl(
            Links::PUBLIC,
            $locate ?'affipublic':"affihome",
            "AffiChanGe, web local",
            $locate?$locate->getSlugcity():$locate);
        }

        $lastnotices=[];

        if($locate){
            $wbsitesloc = $listpublications->listBoardByHitsThanCity($locate);
            $events=$listpublications->findAllEventsofCityBeforeOneWeek($locate->getId(), $wbsitesloc['listboard']);
            if(empty($lastnotices=$listpublications->listPublicationsThanCity($locate->getId(), $wbsitesloc['listboard']))){
                $lastnotices=$listpublications->listPublicationsThanCityArround($locate, $wbsitesloc['listboard']);
            }
        }

        return $this->render('aff_public/home.html.twig', [
            'directory'=>$locate ?'locate':"nolocate",
            'replacejs'=>!empty($lastnotices),
            'vartwig'=>$vartwig,
            'locate'=>$locate,
            'city'=>$locate?$locate->getCity():false,
            'wbsiteloc'=>$wbsitesloc['wbs']??null,
            'orderwbs'=>$wbsitesloc['orderwbs']??null,
            'events'=>$events??[],
            'official'=>$tabloc??null,
            'board'=>$this->board??[],
            'dispatch'=>$this->dispatch??null,
            'lastsnotice'=>$lastnotices??[],
            'scores'=>$wbsitesloc['score']??[],
            'listscore'=>$wbsitesloc['listscore']??[],
            "pws"=>$tabwebsite??[],
            "notifs"=>$notifdispatch??[],
            //'cargo'=>$bulles?json_encode($bulles):null,
            'tag'=>['name'=>$city??null,'active'=>true],
        ]);
    }



    /**
     * @Route("panneaux/{slugcity?}", options={"expose"=true},  name="panneaux_public")
     * @param LocalisationServices $locateService
     * @param Listpublications $listpublications
     * @param PostRepository $postRepository
     * @param WebsiteRepository $websiteRepository
     * @param $slugcity
     * @return Response
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function indexBoardPublic(LocalisationServices $locateService, Listpublications $listpublications,PostRepository  $postRepository,WebsiteRepository $websiteRepository, $slugcity=null): Response
    {
        $bulles=false;

        if ($this->security->isGranted('ROLE_CUSTOMER')) {
            if(!$this->dispatch) throw new Exception('dispatch inconnu');

            //$notifdispatch=$this->dispatch->getTbnotifs();

            if($slugcity){
                $locate=$locateService->findLocate($slugcity);
                if(!$locate instanceof Gps) throw new Exception('ville inconnue');
            }else {
                $locate = $this->dispatch->getLocality();
            }
            $this->activeBoard();
            $vartwig=$this->menuNav->templatingspaceWeb(
                'affipublic',
                $this->board->getNamewebsite(),
                $this->board
            );

            //  $bulles=$this->intiTabbulle($locate->getSlugcity(), $vartwig['tabActivities']);
           // $this->intisimplCatchs();

        }else{
            if($slugcity){
                $locate=$locateService->findLocate($slugcity);
                if(!$locate instanceof Gps) throw new Exception('ville inconnue');
            }else {
                $locate = false;
            }

            $vartwig=$this->menuNav->templateControl(
                Links::PUBLIC,
                $locate ?'affipublic':"affihome",
                "AffiChanGe, web local",
                'all');
        }

        if($locate){
            /** @var Website $wbsitesloc */
            $wbsitesloc = $websiteRepository->findWebsiteOfLocate($locate);

            foreach ($wbsitesloc as $wb){
                $listboard[$wb['codesite']]=$wb;
                if( $wb['statut']) $tabloc[]=$wb;// pour identification du board de la marie locale
            }
        }

        return $this->render('aff_public/home.html.twig', [
            'directory'=>$locate ?'locate':"nolocate",
            'replacejs'=>!empty($lastnotices),
            'vartwig'=>$vartwig,
            'locate'=>$locate,
            'city'=>$locate?$locate->getCity():false,
            'wbsiteloc'=>$wbsitesloc??null,
            'wbsiteelse'=>[], //todo pour l'instant à voir si besoin mais est dans le template
            'official'=>$tabloc??null,
            'board'=>$this->board??[],
            'dispatch'=>$this->dispatch??null,
            //'lastsnotice'=>$lastnotices??[],
            'catchs'=>$this->catchs,
            "pws"=>$tabwebsite??[],
            "notifs"=>$notifdispatch??[],
            //'cargo'=>$bulles?json_encode($bulles):null,
            'tag'=>['name'=>$city??null,'active'=>true,'l_class'=>"cargo"],  // l_class pour la redirection du plugin localitate (choix posible : cargo,customer, init)
        ]);
    }

    /**
     * @Route("search-autour/{slugcity}", options={"expose"=true},  name="cargo_public_arround") //todo complet -> recheche sur tag, autour de ...etc
     * @param LocalisationServices $locateService
     * @param Listpublications $listpublications
     * @param PostRepository $postRepository
     * @param WebsiteRepository $websiteRepository
     * @param $slugcity
     * @return Response
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function searchArround(LocalisationServices $locateService, Listpublications $listpublications,PostRepository  $postRepository,WebsiteRepository $websiteRepository, $slugcity): Response
    {
        $locate=$locateService->findLocate($slugcity);
        if(!$locate instanceof Gps) throw new Exception('ville inconnue');

        if ($this->security->isGranted('ROLE_CUSTOMER')) {
            if(!$this->dispatch) throw new Exception('dispatch inconnu');
            $this->activeBoard();
            $vartwig=$this->menuNav->templatingspaceWeb(
                'affipublic',
                $this->board->getNamewebsite(),
                $this->board
            );
            foreach ($vartwig['tabActivities'] as $module) {
                $bulles[]=['name'=>DefaultModules::TAB_MODULES_NAME[$module],'url'=>$this->generateUrl(DefaultModules::TAB_MODULES_URL_ID[$module],['id'=>$this->board->getId()],UrlGeneratorInterface::ABSOLUTE_URL)];
            }
        }else{

            $vartwig=$this->menuNav->templateControl(
                Links::PUBLIC,
                'affipublic',
                "AffiChanGe, web local",
                'all');
        }


            /** @var Website $wbsitesloc */
            $wbsitesloc = $websiteRepository->findWebsiteOfLocate($locate);
            foreach ($wbsitesloc as $wb){
                if( $wb['statut']){
                    $tabloc[]=$wb;
                    break;
                }
            }

            $lastnotices=[];
            if($replace=empty($lastnotices=$listpublications->listPublicationsThanCity($locate->getId()))){
                $replace=empty($arroundnotices=$postRepository->findAroundlastBycity($locate));
            }

        return $this->render('aff_public/home.html.twig', [
            'directory'=>"affichange",
            'replacejs'=>!$this->useragent?$this->useragent:$replace,
            'vartwig'=>$vartwig,
            'locate'=>$locate,
            'city'=>$city??null,
            'wbsiteloc'=>$wbsitesloc??null,
            'official'=>$tabloc??null,
            'board'=>$this->board??[],
            'dispatch'=>$this->dispatch??[],
            'lastsnotice'=>$lastnotices??[],
            'books'=>[],
            'cargo'=>isset($bulles)?json_encode($bulles):[],
            'tag'=>['name'=>$city??null,'active'=>true,'l_class'=>"cargo"],  // l_class pour la redirection du plugin localitate (choix posible : cargo,customer, init)
        ]);
    }

}
