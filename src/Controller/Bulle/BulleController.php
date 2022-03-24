<?php


namespace App\Controller\Bulle;


use App\Bulle\Bullator;
use App\Classe\affisession;
use App\Lib\MsgAjax;
use App\Service\Bulle\Bullatorette;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_CUSTOMER")
 */

class BulleController extends AbstractController
{
    use affisession;


    /**
     * Create a bubble.
     *
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     * @Route("bulle/blowbubble-ajx", name="add_catch_ajx")
     * @param Request $request
     * @param Bullator $bullator
     * @return JsonResponse
     * @throws Exception
     */
    public function blowBubbleAjx(Request $request, Bullator $bullator): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
            if(!$this->dispatch) return new JsonResponse(MsgAjax::MSG_ERR3);
            $result['id']=$request->request->get('entity');
            $result['module']=$request->request->get('typ');
            $result['dispatch']=$this->dispatch;
            $issue=$bullator->newBulle($result);
            return new JsonResponse($issue);
        }else{
            return new JsonResponse(MsgAjax::MSG_ERR4);
        }
    }

    /**
     * delete a bubble.
     *
     * @Route("bulle/delete-bubble-ajx", name="delete__catch_ajx")
     * @param Request $request
     * @param Bullator $bullator
     * @return JsonResponse
     * @throws Exception
     */
    public function deleteBubbleAjx(Request $request, Bullator $bullator): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
            if(!$this->dispatch) return new JsonResponse(MsgAjax::MSG_ERR3);
            $issue=$bullator->deleteBulle($request->request->get('idbulle'),$this->dispatch);
            return new JsonResponse($issue);
        }else{
            return new JsonResponse(MsgAjax::MSG_ERR4);
        }
    }

    /**
     * add bullette.
     *
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")  //todo
     * @Route("addbullette-ajx/", name="add_bullette_catch_ajx")
     * @param Request $request
     * @param Bullatorette $bullatorette
     * @return JsonResponse
     */
    public function addBulletteAjx(Request $request, Bullatorette $bullatorette): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
            if(!$this->dispatch) return new JsonResponse(MsgAjax::MSG_ERR3);
            $param['author']=$this->dispatch;
            $param['bulle']=$request->request->get('bulle');
            $param['content']=$request->request->get('content');
            $bubblette=$bullatorette->newBullette($param);
            return new JsonResponse($bubblette->getId()); //todo voir a faire diffusion, resouffler, notification ???

        }else{
            return new JsonResponse(MsgAjax::MSG_ERR4);
        }
    }
}