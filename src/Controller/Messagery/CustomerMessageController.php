<?php


namespace App\Controller\Messagery;

use App\Classe\affisession;
use App\Lib\Links;
use App\Repository\Entity\MsgWebisteRepository;
use App\Repository\Entity\PublicationConversRepository;
use App\Repository\Entity\TballmessageRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Service\Messages\PublicationMessageor;
use App\Service\Messages\SortMessageor;
use App\Service\Messages\WebsiteMessageor;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @IsGranted("ROLE_CUSTOMER")
 * @Route("/messagery/customer/")
 */

class CustomerMessageController extends AbstractController
{
    use affisession;



    /**
     * @Route("all/list", name="all_message_customer")
     * @param TballmessageRepository $tballmessageRepo
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param SortMessageor $sortMessageor
     * @return Response
     * @throws Exception
     */
    public function allMessageList(TballmessageRepository $tballmessageRepo, PaginatorInterface $paginator, Request $request, SortMessageor $sortMessageor): Response
    {
        if(!$this->dispatch) throw new Exception('dispatch inconnu');
        $this->activeBoard();

        $pagination = $paginator->paginate(
            $tballmessageRepo->findAllMsgQuery($this->dispatch->getId())->getResult(),
            $request->query->getInt('page', 1),
            5
        );

        /** @var PaginatorInterface $pagination2 */
        $taballs=$pagination->getItems();

        $tabcode=$this->getTabCodeWb();

        foreach ($taballs as $tbmsg) {
            if($tbmsg->getTballmsgp()!=null){
                $tabmessage1[]=$sortMessageor->sortmesgTabAllPublication($tbmsg->getTballmsgp(), $this->dispatch,$tabcode);
            }elseif ($tbmsg->getTballmsgs()!=null) {
                $tabmessage2[]=$sortMessageor->sortmesgTabAll($tbmsg->getTballmsgs(), $this->dispatch,$tabcode);
            }elseif ($tbmsg->getTballmsgd()!=null){
                $tabmessage3[]=$sortMessageor->sortmesgTabAll($tbmsg->getTballmsgd(), $this->dispatch,$tabcode);
            }
        }
        $allmsgs=array_merge( $tabmessage1??[],$tabmessage2??[],$tabmessage3??[]);


        $vartwig=$this->menuNav->newtemplateControlCustomer(
            Links::CUSTOMER_LIST,
            'allmessagecustomer',
            "Ma messagerie",
            1
        );

        return $this->render('aff_messagery/home.html.twig', [
            'directory'=>"customer",
            'replacejs'=>false,
            'vartwig'=>$vartwig,
            'board'=>$this->board,
            'dispatch'=>$this->dispatch,
            'pagination'=>$pagination,
            'msgs'=>$allmsgs,
        ]);
    }

    // todo readmessage private du customer

    /**
     * @Route("msg-read/noboard/custo/{id}", name="read_msg_noboard_custo")
     * @param $id
     * @param MsgWebisteRepository $msgWebisteRepository
     * @param WebsiteMessageor $messageor
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function readMessgNoBoardCustomer($id, MsgWebisteRepository $msgWebisteRepository, WebsiteMessageor $messageor): RedirectResponse|Response
    {
        if(!$this->dispatch) throw new Exception('dispatch inconnu');
        if(!$msg=$msgWebisteRepository->findMsgById($id))throw new Exception('message inconnu');
        $this->activeBoard();

        $msgs=$msg->getMsgs();
        $messageor->majTabNotifs($msgs, $this->dispatch);

        $vartwig=$this->menuNav->newtemplateControlCustomer(
            Links::CUSTOMER_LIST,
            'readmessageboard',
            "Message - board",
            1
        );

        return $this->render('aff_messagery/home.html.twig', [
            'replacejs'=>false,
            'directory'=>"customer",
            'msgwb'=>$msg,
            'dispatch'=>$this->dispatch,
            'vartwig'=>$vartwig,
            'board'=>$this->board
        ]);
    }


    /**
     * @Route("msg-read/nopubli/custo/{id}", name="read_msg_nopubli_custo")
     * @param $id
     * @param WebsiteRepository $websiteRepository
     * @param PublicationConversRepository $publicationConversRepository
     * @param PublicationMessageor $messageor
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function readMessgnoPubliCustomer($id,WebsiteRepository $websiteRepository, PublicationConversRepository $publicationConversRepository, PublicationMessageor $messageor): RedirectResponse|Response
    {
        if (!$this->dispatch) throw new Exception('dispatch inconnu');
        $this->activeBoard();
        if (!$msg = $publicationConversRepository->findMsgById($id)) throw new Exception('message inconnu');
        $msgs = $msg->getMsgs();


        $messageor->majTabNotifs($msgs, $this->dispatch);

        if ($msg->getTabpublication()->getPost()) {
            $key=$msg->getTabpublication()->getPost()->getKeymodule();
            $website=$websiteRepository->findWbByKey($key);
            $vartwig = $this->menuNav->newtemplateControlCustomer(
                Links::CUSTOMER_LIST,
                'readpublicationpost',
                "Message - post",
                1
            );

            return $this->render('aff_messagery/home.html.twig', [
                'replacejs' => false,
                'directory' => "customer",
                'msgwb' => [$website,$msg],
                'board' => $this->board,
                'dispatch' => $this->dispatch,
                'vartwig' => $vartwig,
            ]);
        }
        if ($msg->getTabpublication()->getOffre()){
            $key=$msg->getTabpublication()->getOffre()->getKeymodule();
            $website=$websiteRepository->findWbByKey($key);
            $vartwig = $this->menuNav->newtemplateControlCustomer(
                Links::CUSTOMER_LIST,
                'readpublicationoffre',
                "Message - offre",
                1
            );

            return $this->render('aff_messagery/home.html.twig', [
                'replacejs' => false,
                'directory' => "customer",
                'msgwb' => [$website,$msg],
                'board' => $this->board,
                'dispatch' => $this->dispatch,
                'vartwig' => $vartwig,
            ]);
        }

        return $this->redirectToRoute('all_message_customer');

    }


    /* -------------------------------old--------------------------------------------------------------------------
     /**
     * @Route("/board/list/", name="list_msg_board")
     * @param TballmessageRepository $tballmessageRepo
     * @return Response
     * @throws Exception
     */
    /*
    public function listMsgBoard(TballmessageRepository $tballmessageRepo): Response
    {
        if(!$this->dispatch) throw new Exception('dispatch inconnu');
        $this->activeBoard();
        $allmessageboard=$tballmessageRepo->findByDispatchAndBoardMsg($this->dispatch->getId());

        $vartwig=$this->menuNav->newtemplateControlCustomer(
            Links::CUSTOMER_LIST,
            'ospaceboardnotif',
            "Mes notification - board",
            1
        );

        return $this->render('aff_messagery/home.html.twig', [
            'replacejs'=>false,
            'directory'=>"customer",
            'board'=>$this->board,
            'notifs'=>$allmessageboard,
            'dispatch'=>$this->dispatch,
            'vartwig'=>$vartwig,
        ]);

    }





    */

}