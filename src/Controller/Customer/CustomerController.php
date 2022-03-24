<?php


namespace App\Controller\Customer;

use App\Classe\affisession;
use App\Lib\Calopen;
use App\Lib\Links;
use App\Repository\Entity\FormulesRepository;
use App\Repository\Entity\OffresRepository;
use App\Repository\Entity\PostEventRepository;
use App\Repository\Entity\PostRepository;
use App\Repository\Entity\SpwsiteRepository;
use App\Repository\Entity\SuiviNotifRepository;
use App\Service\Search\Listpublications;
use App\Service\Search\Searchcarte;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @IsGranted("ROLE_CUSTOMER")
 */

class CustomerController extends AbstractController
{
    use affisession;

    /**
     * @Route("/disptach/board/{city}/{nameboard}", name="ospace")
     * @Route("/board/sucess/wp/show/{id?}", name="show_wp")
     * @param null $city
     * @param null $nameboard
     * @param null $id
     * @return Response
     * @throws Exception
     */
    public function ospaceShow($city=null, $nameboard=null, $id=null ): Response
    {
        if(!$this->dispatch) throw new Exception('dispatch inconnu');
        $this->activeBoard($nameboard);
    //    $listmodule=$this->board->getListmodules();
     //   $notices=$listpublications->listPublicationsAndModules($this->board, $listmodule);
        //$msgsnoread=$messageor->messnoreadToList($this->userdispatch->getId());

     //   $notifs=$notifRepository->findBy([
    //        "member"=>$this->dispatch->getId()
     //   ]);

        $vartwig=$this->menuNav->templatingadmin(
            'ospace',
            $this->board->getNamewebsite(),
            $this->board,
            1
            );


        return $this->render('aff_customer/home.html.twig', [
            'directory'=>"board",
            'replacejs'=>false,
            'vartwig'=>$vartwig,
            'board'=>$this->board,
            'website'=>$this->board,
            'dispatch'=>$this->dispatch,
           // 'notices'=>$notices,
          //  'notifs'=>$notifs, //remplacera messnoread
            'partners'=>$this->board->getWebsitepartner()??[],
         //   'modules'=>$listmodule,
          //  'openday'=>$this->board->getTabopendays()? $calopen->cal($this->board->getTabopendays()):"",
         //   'permissions'=>$this->permission
        ]);
    }

    /**
     * @Route("/board/blog/{city}/{nameboard}", name="module_blog")
     * @Route("/board/sucess/blog/show/{id?}", name="show_blog")
     * @param SuiviNotifRepository $notifRepository
     * @param PostRepository $postationRepository
     * @param $city
     * @param $nameboard
     * @param null $id
     * @return Response
     * @throws Exception
     */
    public function ospaceBlog(SuiviNotifRepository $notifRepository, PostRepository $postationRepository, $city=null, $nameboard=null, $id=null ): Response
    {
        if($id){
            if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin )$this->redirectToRoute('cargo_public');
        }else{
            if(!$this->dispatch) throw new Exception('dispatch inconnu');
            $this->activeBoard($nameboard);
        }
        $notifs=$notifRepository->findBy([
            "member"=>$this->dispatch->getId()
        ]);
        $posts=$postationRepository->findPstKey($this->board->getCodesite());
        $vartwig=$this->menuNav->templatingadmin(
            'ospaceblog',
            $this->board->getNamewebsite(),
            $this->board,
            2
        );

        return $this->render('aff_customer/home.html.twig', [
            'directory'=>"board",
            'replacejs'=>false,
            'vartwig'=>$vartwig,
            'board'=>$this->board,
            'website'=>$this->board,
            'dispatch'=>$this->dispatch,
            'posts'=>array_reverse($posts),
        ]);
    }

    /**
     * @Route("/board/Events/{city}/{nameboard}", name="module_event")
     * @param SuiviNotifRepository $notifRepository
     * @param PostEventRepository $eventRepository
     * @param $city
     * @param $nameboard
     * @return Response
     * @throws Exception
     */
    public function ospaceEvent(SuiviNotifRepository $notifRepository, PostEventRepository $eventRepository, $city, $nameboard ): Response
    {
        if(!$this->dispatch) throw new Exception('dispatch inconnu');
        $this->activeBoard($nameboard);
        $notifs=$notifRepository->findBy([
            "member"=>$this->dispatch->getId()
        ]);
        $events=$eventRepository->findEventKey($this->board->getCodesite());
        $vartwig=$this->menuNav->templatingadmin(
            'ospaceevent',
            $this->board->getNamewebsite(),
            $this->board,
            3
        );
        return $this->render('aff_customer/home.html.twig', [
            'directory'=>"board",
            'replacejs'=>false,
            'vartwig'=>$vartwig,
            'board'=>$this->board,
            'website'=>$this->board,
            'dispatch'=>$this->dispatch,
            'events'=>array_reverse($events),
        ]);
    }

    /**
     * @Route("/board/shop/{city}/{nameboard}", name="module_shop")
     * @Route("/board/sucess/shop/show/{id?}", name="show_shop")
     * @param SuiviNotifRepository $notifRepository
     * @param OffresRepository $offresRepository
     * @param $city
     * @param $nameboard
     * @return Response
     * @throws Exception
     */
    public function ospaceShop(SuiviNotifRepository $notifRepository, OffresRepository $offresRepository, $city=null, $nameboard=null, $id=null ): Response
    {
        if($id){
            if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin )$this->redirectToRoute('cargo_public');
        }else{
            if(!$this->dispatch) throw new Exception('dispatch inconnu');
            $this->activeBoard($nameboard);
        }
        $notifs=$notifRepository->findBy([
            "member"=>$this->dispatch->getId()
        ]);
        $shops=$offresRepository->findOffreKey($this->board->getCodesite());

        $vartwig=$this->menuNav->templatingadmin(
            'ospaceshop',
            $this->board->getNamewebsite(),
            $this->board,
            4
        );
        return $this->render('aff_customer/home.html.twig', [
            'directory'=>"board",
            'replacejs'=>false,
            'vartwig'=>$vartwig,
            'board'=>$this->board,
            'website'=>$this->board,
            'dispatch'=>$this->dispatch,
            'shops'=>array_reverse($shops)
        ]);
    }



    /**
     * @Route("/board/Found/{city}/{nameboard}", name="module_found")
     * @param SuiviNotifRepository $notifRepository
     * @param FormulesRepository $formulesRepository
     * @param $city
     * @param $nameboard
     * @return Response
     * @throws Exception
     */
    public function ospaceFound(SuiviNotifRepository $notifRepository, FormulesRepository $formulesRepository, $city, $nameboard ): Response
    {
        if(!$this->dispatch) throw new Exception('dispatch inconnu');
        $this->activeBoard($nameboard);
        $notifs=$notifRepository->findBy([
            "member"=>$this->dispatch->getId()
        ]);
        $formules=$formulesRepository->findByKey($this->board->getCodesite());
        $vartwig=$this->menuNav->templatingadmin(
            'ospacefound',
            $this->board->getNamewebsite(),
            $this->board,
            5
        );

        return $this->render('aff_customer/home.html.twig', [
            'directory'=>"board",
            'replacejs'=>false,
            'vartwig'=>$vartwig,
            'board'=>$this->board,
            'website'=>$this->board,
            'dispatch'=>$this->dispatch,
            'formules'=>array_reverse($formules)
        ]);
    }


    /**
     * @Route("/board/carte/{city}/{nameboard}", name="module_carte")
     * @param Searchcarte $searchcarte
     * @param SuiviNotifRepository $notifRepository
     * @param $nameboard
     * @param $city
     * @return Response
     * @throws Exception
     */
    public function ospaceCarte(Searchcarte $searchcarte,SuiviNotifRepository $notifRepository, $nameboard, $city ): Response
    {
        if(!$this->dispatch) throw new Exception('dispatch inconnu');
        $this->activeBoard($nameboard);
        $notifs=$notifRepository->findBy([
            "member"=>$this->dispatch->getId()
        ]);
        $tabcarte=$searchcarte->findCarte($this->board->getCodesite());
        $vartwig=$this->menuNav->templatingadmin(
            'ospacecarte',
            $this->board->getNamewebsite(),
            $this->board,
            5
        );
        return $this->render('aff_customer/home.html.twig', [
            'directory'=>"board",
            'replacejs'=>false,
            'vartwig'=>$vartwig,
            'board'=>$this->board,
            'website'=>$this->board,
            'dispatch'=>$this->dispatch,
            'articles'=>$tabcarte
        ]);
    }


    /**
     * @Route("/customer/board/mes_panneaux", name="list_board")
     * @param SpwsiteRepository $spwsiteRepository
     * @return Response
     * @throws Exception
     */
    public function listBoard(SpwsiteRepository $spwsiteRepository): Response
    {
        if(!$this->dispatch) throw new Exception('dispatch inconnu');
        $this->activeBoard();

        $vartwig=$this->menuNav->newtemplateControlCustomer(
            Links::CUSTOMER_LIST,
            'listboard',
            "Mes panneaux",
            1
        );
        return $this->render('aff_customer/home.html.twig', [
            'directory'=>"website",
            'replacejs'=>$replacejs??null,
            'vartwig'=>$vartwig,
            'dispatch'=>$this->dispatch,
            'board'=>$this->board,
            'website'=>$this->board,
        ]);
    }



}