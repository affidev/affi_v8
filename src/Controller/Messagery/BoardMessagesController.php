<?php


namespace App\Controller\Messagery;


use App\Classe\affisession;
use App\Repository\Entity\MsgWebisteRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Service\Messages\WebsiteMessageor;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/messagery/board/")
 */

class BoardMessagesController extends AbstractController
{
    use affisession;

    /**
     * @Route("messagery-wb/{id}",  name="board_board")
     * @param MsgWebisteRepository $msgWebisteRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param WebsiteMessageor $messageor
     * @param $id
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function messagerySpw(MsgWebisteRepository $msgWebisteRepository, PaginatorInterface $paginator, Request $request, WebsiteMessageor $messageor, $id): RedirectResponse|Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $msgs=[];

        if(!$this->selectedPwBoard($id)) return $this->redirectToRoute('list_msg_board',['id'=>$id]);
        if(!$this->isPwDispatch($this->board))return throw new Exception('droits insuffisants');

        $q=$msgWebisteRepository->findMsgswebsiteQuery($this->board->getId());
        $pagination = $paginator->paginate(
            $q,
            $request->query->getInt('page', 1),
            10
        );
        /** @var PaginatorInterface $pagination */
        $msgs = $messageor->sortmesswebsite($pagination, $this->pw, $this->dispatch);

        $vartwig=$this->menuNav->templatingadmin(
            'conversation',
            $this->board->getNamewebsite(),
            $this->board,
            1
        );

        return $this->render('aff_messagery/home.html.twig', [
            'directory' => 'board',
            'replacejs'=>$replacejs??null,
            'website' => $this->board,
            'board'=>$this->board,
            'msgs' => $msgs,
            'dispatch'=>$this->dispatch,
            'vartwig' => $vartwig,
            'pw' => $this->pw,
            'member' => $this->memberwb,
            'pagination' => $pagination ?? null,
        ]);
    }

    /**
     * @Route("msg-read/{slug}/{id}", name="read_msg_board")
     * @param $id
     * @param $slug
     * @param MsgWebisteRepository $msgWebisteRepository
     * @param WebsiteMessageor $messageor
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function readMessg($id, $slug, MsgWebisteRepository $msgWebisteRepository, WebsiteMessageor $messageor): RedirectResponse|Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if(!$msg=$msgWebisteRepository->findMsgById($id))throw new Exception('message inconnu');
        if(!$this->isPwDispatch($msg->getWebsitedest())) throw new Exception('dispatch inconnu');
        $this->board=$msg->getWebsitedest();
        $msgs=$msg->getMsgs();
        $messageor->majTabNotifs($msgs, $this->dispatch);

        $vartwig=$this->menuNav->templatingadmin(
            'read',
            $this->board->getNamewebsite(),
            $this->board,
            1
        );
        return $this->render('aff_messagery/home.html.twig', [
            'directory'=>'board',
            'replacejs'=>$replacejs??null,
            'vartwig'=>$vartwig,
            'dispatch'=>$this->dispatch,
            'msgwb'=>$msg,
            'msgs'=>$msgs,
            'website'=>$this->board,
            'board'=>$this->board,
            'pw'=>$this->pw,
        ]);
    }


    /**
     * @Route("msg-read-other/{slug}/{id}", name="read_msg_board_other")
     * @param $id
     * @param $slug
     * @param MsgWebisteRepository $msgWebisteRepository
     * @param WebsiteMessageor $messageor
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function readMsgOther($id, $slug, MsgWebisteRepository $msgWebisteRepository, WebsiteMessageor $messageor): RedirectResponse|Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if(!$msg=$msgWebisteRepository->findMsgById($id))throw new Exception('message inconnu');

        $this->board=$msg->getWebsitedest();
        $msgs=$msg->getMsgs();
        $messageor->majTabNotifs($msgs, $this->dispatch);

        $vartwig=$this->menuNav->templatingadmin(
            'read',
            $this->board->getNamewebsite(),
            $this->board,
            1
        );
        return $this->render('aff_messagery/home.html.twig', [
            'directory'=>'board',
            'replacejs'=>$replacejs??null,
            'vartwig'=>$vartwig,
            'dispatch'=>$this->dispatch,
            'msgwb'=>$msg,
            'msgs'=>$msgs,
            'website'=>$this->board,
            'board'=>$this->board,
        ]);
    }

    /**
     * @Route("/message/wb/comebackout/site/{key}",  name="message_comeout_website")
     * @param $key
     * @param WebsiteRepository $websiteRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function messageComeoutWebsite($key, WebsiteRepository $websiteRepository): Response
    {

        $this->board=$websiteRepository->findWbByKey($key);
        if(!isset($this->board))throw new \Symfony\Component\Config\Definition\Exception\Exception('board inconnu');

        $vartwig = $this->menuNav->websiteinfoObj($this->board, 'outmessage', $this->board->getNamewebsite(), 'visitor');


        return $this->render('aff_messagery/home.html.twig', [
            'directory'=>'outcome',
            'dispatch'=>$this->dispatch??null,
            'replacejs'=>false,
            'vartwig'=>$vartwig,
            'website'=>$this->board,
            'board'=>$this->board,
            'pw'=>$this->pw??false,
            'partners'=>$this->board->getWebsitepartner()??[],
        ]);
    }




    /**
     * @Route("new-message-board", name="new_message_board")
     * @param WebsiteMessageor $messageor
     * @param Request $request
     * @param WebsiteRepository $websiteRepository
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function newWebsiteMessage(WebsiteMessageor $messageor,Request $request, WebsiteRepository $websiteRepository): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
            $data = json_decode((string) $request->getContent(), true);
            $website=$websiteRepository->findWbBySlug($data['slug']);
            if(!$website) return new JsonResponse(['success'=>false]);
            if($this->dispatch){
                $client=$this->selectedPwBoard($website->getId());
            }else{
                $client=null;
            }
            $msg=$messageor->newMessageBoard($data, $website, $client,$this->dispatch??null);
            $responseCode = 200;
            http_response_code($responseCode);
            header('Content-Type: application/json');
            return new JsonResponse(['success' => true, 'convers' => $msg->getId()]);
        }
        return new JsonResponse(['success'=>false]);
    }


    /**
     * @Route("add-convers", options={"expose"=true}, name="add_conver_wb")
     * @param Request $request
     * @param MsgWebisteRepository $msgWebisteRepository
     * @param WebsiteMessageor $messageor
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function addConvers(Request $request, MsgWebisteRepository $msgWebisteRepository, WebsiteMessageor $messageor): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($request->isXmlHttpRequest()) {
            if (!$this->dispatch) return new JsonResponse(['success' => false, 'inf' => "dispatch pas reconnu"]);
            $data = json_decode((string)$request->getContent(), true);
            $convers = $msgWebisteRepository->findMsgById($data['msgid']);
            if ($convers) {
                $convers = $messageor->addConvers($data, $convers, $this->dispatch);
                $responseCode = 200;
                http_response_code($responseCode);
                header('Content-Type: application/json');
                return new JsonResponse(['success' => true, 'convers' => $convers->getId()]);
            }
            return new JsonResponse(['success' => false, 'inf' => "message inconnu"]);
        }
        return new JsonResponse(['success' => false]);
    }
}