<?php


namespace App\Controller\Customer;

use App\Classe\affisession;
use App\Lib\Links;
use App\Lib\MsgAjax;
use App\Repository\Entity\OffresRepository;
use App\Repository\Entity\PostEventRepository;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * @IsGranted("ROLE_CUSTOMER")
 */

class FavorisController extends AbstractController
{
    use affisession;

    /**
     * @Route("/favoris/all/board/{page?}", name="favoris_board")
     * @param SuiviNotifRepository $notifRepository
     * @return Response
     * @throws Exception
     */
    public function boardFavoris(SuiviNotifRepository $notifRepository,PostRepository $postRepository, PostEventRepository $postEventRepository, $page=null ): Response
    {
        if(!$this->dispatch) throw new Exception('dispatch inconnu');
        $this->activeBoard();
        //$msgsnoread=$messageor->messnoreadToList($this->userdispatch->getId());
        $notifs=$notifRepository->findBy([
            "member"=>$this->dispatch->getId()
        ]);
// todo faire un compteur pour faire une requete sur 10 entités
        foreach ($this->dispatch->getBulles() as $key => $catch){
            if($catch->getModulebubble()=='blog'){
                $this->catchs['blog'][]=['post'=>$postRepository->find($catch->getIdmodule()),'catch'=>$catch];
            }else{
                $this->catchs['event'][]=['event'=>$postEventRepository->find($catch->getIdmodule()),'catch'=>$catch];
            }
        }

        $vartwig=$this->menuNav->newtemplatingspaceWeb(
            'board',
            $this->board->getNamewebsite(),
            $this->board,
            1
        );

        return $this->render('aff_customer/home.html.twig', [
            'directory'=>"partners",
            'replacejs'=>true,
            'vartwig'=>$vartwig,
            'board'=>$this->board,
            'website'=>$this->board,
            'dispatch'=>$this->dispatch,
            'favoris'=>$this->catchs,
            'notifs'=>$notifs, //remplacera messnoread
           // 'partner'=>$this->board->getPartnergroup()->getWebsites(),
        ]);
    }


}