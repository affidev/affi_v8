<?php


namespace App\Controller\MainPublic;

use App\Classe\affisession;
use App\Entity\Websites\Website;
use App\Event\WebsiteCreatedEvent;
use App\Lib\Calopen;
use App\Repository\Entity\WebsiteRepository;
use App\Service\Search\Listpublications;
use App\Util\DefaultModules;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PublicWebsiteController extends AbstractController
{
    use affisession;

    /**
     * @Route("/website/{slug}", name="oldwebsite")
     * @param $slug
     * @param WebsiteRepository $websiteRepository
     * @return Response
     */
    public function oldroute($slug,WebsiteRepository $websiteRepository): Response
    {
        /** @var Website $website */
        $website=$websiteRepository->findWbBySlugAndMembers($slug);
        return $this->redirectToRoute('show_website', ['slugcity'=>$website->getLocality()->getCity(),'slug' =>$slug], 301);
    }

    /**
     * @Route("/webloc/{city}/{slug}", name="old_webloc")
     * @param $slug
     * @param null $city
     * @return Response
     */
    public function oldwebloc($slug, $city=null): Response
    {
        return $this->redirectToRoute('show_website', ['slugcity'=>$city,'slug' =>$slug], 301);
    }

    /**
     * @Route("/comptoir/{slug}", name="oldroute_comptoir")
     * @param Request $request
     * @param WebsiteRepository $websiteRepository
     * @param $slug
     * @return Response
     */
    public function oldcomptoire(Request $request, WebsiteRepository $websiteRepository, $slug): Response
    {
       if($slugcity=$request->query->get('slugcity')){
           return $this->redirectToRoute('show_website', ['slugcity'=>$slugcity,'slug' =>$slug], 301);
       }else{
           /** @var Website $website */
           $website=$websiteRepository->findWbBySlugAndMembers($slug);
           return $this->redirectToRoute('show_website', ['slugcity'=>$website->getLocality()->getCity(),'slug' =>$slug], 301);
       }
    }

    /**
     * @Route("/{city}/{nameboard}", options={"expose"=true}, name="show_website", defaults={"pg"=0})
     * @param $city
     * @param $nameboard
     * @param Listpublications $listpublications
     * @param WebsiteRepository $websiteRepository
     * @param Calopen $calopen
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showWebsite($city,$nameboard, Listpublications $listpublications, WebsiteRepository $websiteRepository,  Calopen $calopen,EventDispatcherInterface $dispatcher): Response
    {
        $bulles=false;
        $this->board=$websiteRepository->findWbBySlug($nameboard);
        if(!isset($this->board))throw new Exception('board inconnu');
        $event= new WebsiteCreatedEvent($this->board);
        $dispatcher->dispatch($event, WebsiteCreatedEvent::SHOW_WEBSITE);
        $locate=$this->board->getLocality();
        $listmodule=$this->board->getListmodules();

        $notices=$listpublications->listPublicationsAndModules($this->board, $listmodule);

        if($this->isGranted('ROLE_CUSTOMER') && $this->dispatch) {
            $vartwig=$this->menuNav->templatingspaceWeb(
                'showWb',
                $this->board->getNamewebsite(),
                $this->board
            );

            if($this->isPwDispatch($this->board)) {
                /*
                foreach ($vartwig['tabActivities'] as $module) {
                    $bulles[] = ['name' => DefaultModules::TAB_MODULES_NAME[$module], 'url' => $this->generateUrl(DefaultModules::TAB_MODULES_URL_ID[$module], ['id' => $this->board->getId()], UrlGeneratorInterface::ABSOLUTE_URL)];
                }
                */
                $follow=false;
            }else{
                $follow=true;
            }

           // $bulles=$this->intiTabbulle($locate->getSlugcity(), $vartwig['tabActivities']);

        }else {
            $follow=true;
            $vartwig = $this->menuNav->websiteinfoObj($this->board, 'showWb', $this->board->getNamewebsite(), 'visitor');
        }

        return $this->render('aff_website/home.html.twig', [
            'directory'=>'website',
            'dispatch'=>$this->dispatch??null,
            'replacejs'=>false,
            'vartwig'=>$vartwig,
            'isfollow'=>$follow,
            'notices'=>$notices,
            'website'=>$this->board,
            'board'=>$this->board,
            'infowb'=>true,
            'pw'=>$this->pw??false,
            'partners'=>$this->board->getWebsitepartner()??[],
            'modules'=>$listmodule,
           // 'cargo'=>$bulles?json_encode($bulles):null,
            'openday'=>$this->board->getTabopendays()? $calopen->cal($this->board->getTabopendays()):"",
        ]);
    }


    /**
     * @Route("/panneau/{slugcity}/{slug}", options={"expose"=true}, name="board")
     * @param Listpublications $listpublications
     * @param WebsiteRepository $websiteRepository
     * @param $slug
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showBoard(Listpublications $listpublications,  WebsiteRepository $websiteRepository, $slug): Response
    {
        $this->board=$websiteRepository->findWbBySlug($slug);
        if(!isset($this->board))return $this->redirectToRoute('cargo_public'); //todo adpater mieux
        $notices=$listpublications->listPublicationsboard($this->board);

        if($this->isGranted('ROLE_CUSTOMER') && $this->dispatch) {
            $vartwig=$this->menuNav->templatingspaceWeb(
                'showboard',
                $this->board->getNamewebsite(),
                $this->board
            );
            /*
            if($this->isPwDispatch($this->board)) {
                foreach ($vartwig['tabActivities'] as $module) {
                    $bulles[] = ['name' => DefaultModules::TAB_MODULES_NAME[$module], 'url' => $this->generateUrl(DefaultModules::TAB_MODULES_URL_ID[$module], ['id' => $this->board->getId()], UrlGeneratorInterface::ABSOLUTE_URL)];
                }
            }
            */
        }else {
            $vartwig=$this->menuNav->websiteinfoObj($this->board,'showboard',$this->board->getNamewebsite(),'visitor');
        }

        return $this->render('aff_website/home.html.twig', [
            'directory'=>'boards',
            'replacejs'=>!empty($notices),
            'vartwig' => $vartwig,
            'dispatch'=>$this->dispatch??false,
            'notices'=>$notices,
            'pw'=>$this->pw??false,
            'website'=>$this->board,
            'board'=>$this->board,
        ]);
    }


    /**
     * @Route("/shop/{slugcity}/{slug}", options={"expose"=true}, name="shop", requirements={"slug":"[a-z0-9\-]*"})
     * @param Listpublications $listpublications
     * @param WebsiteRepository $websiteRepository
     * @param $slug
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showShop(Listpublications $listpublications,  WebsiteRepository $websiteRepository, $slug): Response
    {
        $this->board=$websiteRepository->findWbBySlug($slug);
        if(!isset($this->board))return $this->redirectToRoute('cargo_public');
        $locate=$this->board->getLocality();
        $notices=$listpublications->listOffres($this->board->getCodesite());

        if($this->isGranted('ROLE_CUSTOMER') && $this->dispatch) {
            $vartwig=$this->menuNav->templatingspaceWeb(
                'showshop',
                $this->board->getNamewebsite(),
                $this->board
            );
            /*
            if($this->isPwDispatch($this->board)) {
                foreach ($vartwig['tabActivities'] as $module) {
                    $bulles[] = ['name' => DefaultModules::TAB_MODULES_NAME[$module], 'url' => $this->generateUrl(DefaultModules::TAB_MODULES_URL_ID[$module], ['id' => $this->board->getId()], UrlGeneratorInterface::ABSOLUTE_URL)];
                }
            }
            */
        }else {
            $vartwig = $this->menuNav->websiteinfoObj($this->board, 'showshop', 'boutique', 'visitor');
        }
        return $this->render('aff_website/home.html.twig', [
            'directory'=>'boards',
            'replacejs'=>!empty($notices),
            'vartwig' => $vartwig,
            'offres'=>$notices,
            'pw'=>$this->pw??false,
            'website'=> $this->board,
            'board'=>$this->board,
            'city'=>$locate->getCity()
        ]);
    }
}