<?php


namespace App\Controller\Partners;

use App\Classe\affisession;
use App\Lib\Links;
use App\Lib\MsgAjax;
use App\Repository\Entity\PostRepository;
use App\Repository\Entity\SpwsiteRepository;
use App\Repository\Entity\SuiviNotifRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Service\Messages\PrivateMessageor;
use App\Service\Search\Listpublications;
use App\Service\SpaceWeb\SpacewebFactor;
use App\Util\DefaultModules;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @IsGranted("ROLE_CUSTOMER")
 */

class PartnersController extends AbstractController
{
    use affisession;


    /**
     * @Route("/board/partner/list", name="search_partner")
     * @param SuiviNotifRepository $notifRepository
     * @param SpwsiteRepository $spwsiteRepository
     * @param Listpublications $listpublications
     * @param PrivateMessageor $messageor
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function searchPartner(SuiviNotifRepository $notifRepository,SpwsiteRepository $spwsiteRepository, Listpublications $listpublications,  PrivateMessageor $messageor, Request $request): Response
    {
        if(!$this->dispatch) throw new Exception('dispatch inconnu');

        $vartwig=$this->menuNav->templatingspaceWeb(
            'search',
            $this->board->getNamewebsite(),
            $this->board
        );
        /*
        $bulles=[];
        foreach ($vartwig['tabActivities'] as $module) {
            $bulles[]=['name'=>DefaultModules::TAB_MODULES_NAME[$module],'url'=>$this->generateUrl(DefaultModules::TAB_MODULES_URL[$module])];
        }
*/
        return $this->render('aff_customer/home.html.twig', [
            'directory'=>"search",
            'website'=>$this->board,
            'replacejs'=>null,
            'dispatch'=>$this->dispatch,
            'wbsitesuser'=>$this->board->getWebsitepartner(),
           // 'cargo'=>json_encode($bulles),
            'vartwig'=>$vartwig,
        ]);
    }

    /**
     * add partner.
     *
     * @Route("/partner/board/apiajax/add-partner-ajx", name="add_partner_ajx_byapi")
     * @param Request $request
     * @param SpacewebFactor $factor
     * @param WebsiteRepository $websiteRepository
     * @return JsonResponse
     */
    public function addPartnerAjxByApi(Request $request,SpacewebFactor $factor, WebsiteRepository $websiteRepository): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
           if(!$this->dispatch) return new JsonResponse(MsgAjax::MSG_ERRORRQ);
            $data = json_decode((string) $request->getContent(), true);

            if(!$this->selectedPwBoard($data['board']))return new JsonResponse(MsgAjax::MSG_ERRORRQ);
            $partner=$websiteRepository->find($data['partner']);
            $issue=$factor->addPartner($this->board, $partner);
            return new JsonResponse($issue);
        }else{
            return new JsonResponse(MsgAjax::MSG_ERRORRQ);
        }
    }

    /**
     * @Route("partners/all/board", name="partners_board")
     * @param SuiviNotifRepository $notifRepository
     * @param PostRepository $postationRepository
     * @param $city
     * @param $nameboard
     * @param null $id
     * @return Response
     * @throws Exception
     */
    public function ospacePartners(SuiviNotifRepository $notifRepository, PostRepository $postationRepository, $city=null, $nameboard=null, $id=null ): Response
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
        $vartwig=$this->menuNav->newtemplatingspaceWeb(
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
            'notifs'=>$notifs, //remplacera messnoread
            'partner'=>$this->board->getPartnergroup()->getWebsites(),
            'permissions'=>$this->permission
        ]);
    }

    /**
     * @Route("/partners/list", name="list_partners")
     * @param SpwsiteRepository $spwsiteRepository
     * @return Response
     * @throws Exception
     */
    public function listPartners(SpwsiteRepository $spwsiteRepository): Response
    {
        if(!$this->dispatch) throw new Exception('dispatch inconnu');
        $this->activeBoard();

        $vartwig=$this->menuNav->newtemplateControlCustomer(
            Links::CUSTOMER_LIST,
            'listwebsite',
            "Mes panneaux",
            1
        );
        return $this->render('aff_customer/home.html.twig', [
            'directory'=>"website",
            'replacejs'=>$replacejs??null,
            'vartwig'=>$vartwig,
            'dispatch'=>$this->dispatch,
            'board'=>$this->board,
            'permissions'=>$this->permission
        ]);
    }



}