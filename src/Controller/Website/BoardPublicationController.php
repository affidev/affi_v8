<?php

namespace App\Controller\Website;

use App\Classe\affisession;
use App\Repository\Entity\BulleRepository;
use App\Service\Messages\PublicationMessageor;
use App\Service\Search\Searchmodule;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Exception;


/**
 * @IsGranted("ROLE_CUSTOMER")
 */

class BoardPublicationController extends AbstractController
{
    use affisession;



    /**
     * @Route("/affiche/board/event/{id?}", name="board_event")
     * @param Searchmodule $searchmodule
     * @param BulleRepository $bulleRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showmsgEvent(Searchmodule $searchmodule,BulleRepository $bulleRepository, $id): Response
    {
        $post=$searchmodule->findOnePostAndMsgs($id);
        $bulle=$bulleRepository->findBulleOfPublication($post->getId(),'blog');
        $this->initBoardByKey($post->getKeymodule());
        if(!$this->admin) throw new Exception('dispatch ne dispose pas des droits erreur log notification');
        if($post->getTbmessages()){   // todo en attendant que toutes les publication aient un tabmessage
            $msgP=$post->getTbmessages()->getIdmessage();
            foreach ($msgP as $msg){
                if(!$msg->getIsdispatchsender()){
                    $tab['contact'][]=$msg->getContact();
                }else{
                    $tab['dispatch'][]=$msg->getDispatch();
                }
            }
        }

        $notifs=$this->dispatch->getTbnotifs();
        $vartwig=$this->menuNav->templatingadmin(
            'boardblog',
            $this->board->getNamewebsite(),
            $this->board,
            2
        );

        return $this->render('aff_customer/home.html.twig', [
            'directory'=>"publications",
            'replacejs'=>false,
            'vartwig'=>$vartwig,
            'board'=>$this->board,
            'post'=>$post,
            'bulles'=>$bulle,
            'contacts'=>$tab??[],
            'msgps'=>$msgP??[],
            'website'=>$this->board,
            'dispatch'=>$this->dispatch,
            'msgpublication'=>$msgP??[],
            'notifs'=>$notifs,
        ]);
    }

    /**
     * @Route("/affiche/board/blog/{id}-{notif?}", name="board_blog")
     * @param Searchmodule $searchmodule
     * @param BulleRepository $bulleRepository
     * @param PublicationMessageor $messageor
     * @param $id
     * @param null $notif
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showmsgBlog(Searchmodule $searchmodule,BulleRepository $bulleRepository,PublicationMessageor $messageor, $id, $notif=null): Response
    {
        $tabpost=$searchmodule->searchOnePostAndMsgP($id);

        $bulle=$bulleRepository->findBulleOfPublication($tabpost['post']->getId(),'blog');
        if(!$this->initBoardByKey($tabpost['post']->getKeymodule())) {
            $messageor->annulTabNotifs($notif, $this->dispatch,$this->em); //si ereur de droit annule la notif ?? todo pas terrible mais a voir pour mieux
            $this->redirectToRoute('cargo_public');
        }

        if($tabpost['post']->getTbmessages()){   // todo en attendant que toutes les publication aient un tabmessage
            $msgPs=$tabpost['post']->getTbmessages()->getIdmessage();
            $msgPs = $messageor->sortMsgPublication($msgPs, $this->pw, $this->dispatch);

            foreach ($msgPs as $msgp){
                $taball=$msgp->getTballmsgp();
                if(!$msgp->getIsdispatchsender()){
                    $tab['contact'][]=$taball->getContact();
                }else{
                    $tab['dispatch'][]=$taball->getDispatch();
                }
            }
        }
        $notifs=$this->dispatch->getTbnotifs();
        $vartwig=$this->menuNav->templatingadmin(
            'boardblog',
            $this->board->getNamewebsite(),
            $this->board,
            2
        );

        return $this->render('aff_customer/home.html.twig', [
            'directory'=>"publications",
            'replacejs'=>false,
            'vartwig'=>$vartwig,
            'board'=>$this->board,
            'entity'=>$tabpost,
            'bulles'=>$bulle,
            'contacts'=>$tab??[],
            'msgps'=>$msgPs??[],
            'back'=> $this->generateUrl('show_blog',['id'=>$this->board->getId()]),
            'website'=>$this->board,
            'dispatch'=>$this->dispatch,
            'msgpublication'=>$msgPs??[],
            'notifs'=>$notifs,
        ]);
    }

    /**
     * @Route("/affiche/board/shop/{id?}", name="board_shop")
     * @param Searchmodule $searchmodule
     * @param BulleRepository $bulleRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showmsgShop(Searchmodule $searchmodule,BulleRepository $bulleRepository, $id): Response
    {
        $post=$searchmodule->searchOnePostAndListAndMsg($id);
        $bulle=$bulleRepository->findBulleOfPublication($post->getId(),'blog');
        $this->initBoardByKey($post->getKeymodule());
        if(!$this->admin) throw new Exception('dispatch ne dispose pas des droits erreur log notification');
        if($post->getTbmessages()){   // todo en attendant que toutes les publication aient un tabmessage
            $msgP=$post->getTbmessages()->getIdmessage();
            foreach ($msgP as $msg){
                if(!$msg->getIsdispatchsender()){
                    $tab['contact'][]=$msg->getContact();
                }else{
                    $tab['dispatch'][]=$msg->getDispatch();
                }
            }
        }
        $notifs=$this->dispatch->getTbnotifs();
        $vartwig=$this->menuNav->templatingadmin(
            'boardblog',
            $this->board->getNamewebsite(),
            $this->board,
            2
        );

        return $this->render('aff_customer/home.html.twig', [
            'directory'=>"publications",
            'replacejs'=>false,
            'vartwig'=>$vartwig,
            'board'=>$this->board,
            'post'=>$post,
            'bulles'=>$bulle,
            'contacts'=>$tab??[],
            'msgps'=>$msgP??[],
            'website'=>$this->board,
            'dispatch'=>$this->dispatch,
            'msgpublication'=>$msgP??[],
            'notifs'=>$notifs,
        ]);
    }


    /**
     * @Route("/affiche/board/found/{id?}", name="board_found")
     * @param Searchmodule $searchmodule
     * @param BulleRepository $bulleRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showmsgFound(Searchmodule $searchmodule,BulleRepository $bulleRepository, $id): Response
    {
        $post=$searchmodule->findOnePostAndMsgs($id);

        $this->initBoardByKey($post->getKeymodule());
        if(!$this->admin) throw new Exception('dispatch ne dispose pas des droits erreur log notification');
        if($post->getTbmessages()){   // todo en attendant que toutes les publication aient un tabmessage
            $msgP=$post->getTbmessages()->getIdmessage();
            foreach ($msgP as $msg){
                if(!$msg->getIsdispatchsender()){
                    $tab['contact'][]=$msg->getContact();
                }else{
                    $tab['dispatch'][]=$msg->getDispatch();
                }
            }
        }

        dump($post, $bulle,$tab??[]);
        $notifs=$this->dispatch->getTbnotifs();
        $vartwig=$this->menuNav->templatingadmin(
            'boardblog',
            $this->board->getNamewebsite(),
            $this->board,
            2
        );

        return $this->render('aff_customer/home.html.twig', [
            'directory'=>"publications",
            'replacejs'=>false,
            'vartwig'=>$vartwig,
            'board'=>$this->board,
            'post'=>$post,
            'bulles'=>$bulle,
            'contacts'=>$tab??[],
            'msgps'=>$msgP??[],
            'website'=>$this->board,
            'dispatch'=>$this->dispatch,
            'msgpublication'=>$msgP??[],
            'notifs'=>$notifs,
        ]);
    }
}