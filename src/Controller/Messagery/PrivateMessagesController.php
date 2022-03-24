<?php


namespace App\Controller\Messagery;


use App\Classe\customersession;
use App\Entity\LogMessages\PrivateConvers;
use App\Entity\Websites\Website;
use App\Form\PrivateConversFormType;
use App\Lib\Links;
use App\Lib\Tools;
use App\Repository\Entity\ContactationRepository;
use App\Repository\Entity\MsgWebisteRepository;
use App\Repository\Entity\PrivateConversRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Service\Messages\PrivateMessageor;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/member/messagery/private/")
 * @IsGranted("ROLE_CUSTOMER")
 */

class PrivateMessagesController extends AbstractController  //todo completement
{
    use customersession;

    /**
     * @Route("list/", name="list_private_convers_dp")
     * @param PrivateConversRepository $privateConversRepository
     * @param MsgWebisteRepository $msgWebisteRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param PrivateMessageor $messageor
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function privateConversDp(PrivateConversRepository $privateConversRepository,MsgWebisteRepository $msgWebisteRepository, PaginatorInterface $paginator, Request $request, PrivateMessageor $messageor): RedirectResponse|Response
    {
        $dispatch=$this->test();
        $contacts= $dispatch->getDispatchlinks();

        $pagination = $paginator->paginate(
            $privateConversRepository->findMsgsdispatchQuery($dispatch->getId()),
            $request->query->getInt('page', 1),
            5
        );
        /** @var PaginatorInterface $pagination */
        $msgsprivate = $messageor->sortmessage($pagination, $dispatch);

        $pagination2 = $paginator->paginate(
            $msgWebisteRepository->findMsgswebsiteForOneDispatchQuery($dispatch),
            $request->query->getInt('page', 1),
            5
        );

        /** @var PaginatorInterface $pagination2 */
        $msgswebsite = $messageor->sortmessage($pagination2, $dispatch);
        $msgs=array_merge($msgsprivate,$msgswebsite);
        $privatemessage = new PrivateConvers();
        $form = $this->createForm(PrivateConversFormType::class, $privatemessage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $messageor->newPrivateConvers([
                'form' => $form,
                'message' => $privatemessage,
                'dispatch' => $dispatch,
            ]);
            return $this->redirectToRoute('list_private_convers_dp');

        }
        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'privateconvers',
            "Toutes vos conversations privées",
            'all');

        return $this->render('aff_customer/home.html.twig', [
            'replacejs'=>$replacejs??null,
            'directory'=>"privateconvers",
            'customer'=>$this->customer,
            'contacts'=>$contacts,
            'dispatch'=>$dispatch,
            'msgs' => $msgs,
            'pagination' => $pagination ?? null,
            'vartwig'=>$vartwig,
            'form' => $form->createView(),
            'permissions'=>$this->permission
        ]);
    }

    /**
     * @Route("newprivate-convers/{id}", name="new_private_convers_dp")
     * @param Request $request
     * @param PrivateMessageor $messageor
     * @param $id
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function newprivateConversDp(Request $request, PrivateMessageor $messageor, $id): RedirectResponse|Response
    {
        $dispatch=$this->test();
        $contact= $this->repodispacth->find($id);
        /** @var PaginatorInterface $pagination2 */
        $privatemessage = new PrivateConvers();
        $privatemessage->setDispatchopen($dispatch);
        $privatemessage->addDispatchdest($contact);
        $form = $this->createForm(PrivateConversFormType::class, $privatemessage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $messageor->newPrivateConversdispatch($form,$privatemessage,$dispatch,$contact);
            return $this->redirectToRoute('read_private_convers_dp',['id'=>$privatemessage->getId()]);
        }
        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'newprivateconvers',
            "new privateconvers",
            'all');

        return $this->render('aff_customer/home.html.twig', [
            'replacejs'=>$replacejs??null,
            'directory'=>"privateconvers",
            'customer'=>$this->customer,
            'dispatch'=>$dispatch,
            'contact'=>$contact,
            'convers'=>$privatemessage,
            'vartwig'=>$vartwig,
            'form' => $form->createView(),
            'permissions'=>$this->permission
        ]);
    }

    /**
     * @Route("read/{id}", name="read_private_convers_dp")
     * @param $id
     * @param PrivateConversRepository $privateconversrepo
     * @param PrivateMessageor $privatemessageor
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function readPrivateConvers($id, PrivateConversRepository $privateconversrepo, PrivateMessageor $privatemessageor): RedirectResponse|Response
    {
        if(!$dispatch=$this->userdispatch) return $this->redirectToRoute('api-error',['err'=>1]);
        $customer=$dispatch->getCustomer();
        $convers=$privateconversrepo->findPrivateConversAndMsgById($id);
        if(!$convers)  return $this->redirectToRoute('list_private_convers_dp');

        $privatemessageor->readersconvers($convers, $dispatch);
        $vartwig=$this->menuNav->templateControlCustomer(
            Links::CUSTOMER_LIST,
            'read',
            ""
        );

        return $this->render('aff_customer/home.html.twig', [
            'replacejs'=>$replacejs??null,
            'customer'=>$customer,
            'dispatch'=>$dispatch,
            'vartwig'=>$vartwig,
            'convers'=>$convers,
            'directory'=>"privateconvers",
            'permissions'=>$this->permission
        ]);
    }


    /**
     * @Route("add-convers", options={"expose"=true}, name="add_private-convers_dp")
     * @param Request $request
     * @param PrivateConversRepository $privateconversrepo
     * @param PrivateMessageor $privatemessageor
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function addPrivateConvers(Request $request, PrivateConversRepository $privateconversrepo, PrivateMessageor $privatemessageor): JsonResponse
    {

        if ($request->isXmlHttpRequest()) {
            if ($dispatch = $this->dispatch) {
                $customer = $dispatch->getCustomer();
                $convers = $privateconversrepo->findPrivateConversAndMsgById($request->request->get('msgwb'));
                if ($convers) {
                    $content = Tools::cleanspecialcaractere($request->request->get('content'));
                    $convers = $privatemessageor->addConvers($convers, $dispatch, $content);
                    $responseCode = 200;
                    http_response_code($responseCode);
                    header('Content-Type: application/json');
                    return new JsonResponse(['success' => true, 'convers' => $convers->getId()]);
                }
                return new JsonResponse(['success' => false,'inf' => "message inconnu"]);
            }
            return new JsonResponse(['success' => false,'inf'=> "dispatch pas reconnu"]);
        }
        return new JsonResponse(['success' => false]);
    }

/*--------------------------------------- non verif -------------------------------------*/


    // varainte avec recherche sur msg //todo a verfi avant de mettre en service

    /**
     * @Route("messages-disatch/{slug}", name="messages_dispatch")
     * @param $slug
     * @param WebsiteRepository $websiteRepository
     * @param MsgWebisteRepository $messagerepo
     * @param ContactationRepository $contactationRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function dispatchMessg($slug, WebsiteRepository $websiteRepository, MsgWebisteRepository $messagerepo, ContactationRepository $contactationRepository): Response
    {
        /** @var Website $website */
        $website=$websiteRepository->findWbQ2($slug, $this->dispatch);
        if(!$website) return $this->redirectToRoute('cargo_public');
        $contactation=$contactationRepository->findCTQ1($website->getModule()->getId());
// todo choisir ici la meilleur approche de requete
        $msgs=$messagerepo->findAllForOneDispatchThanWebsite($this->dispatch,$contactation->getId());

        if(!$contactation){
            $bullesmsg=[];
        }else{
            $bullesmsg=$contactation->getMessages();
        }
        $vartwig=$this->menuNav->templatingspaceWeb(
            'main_spaceweb/msgwebsite/message',
            'Privatemsg',
            $website);
        return $this->render('layout/layout_wb.html.twig', [
            'replacejs'=>$replacejs??null,
            'agent'=>$this->useragent,
            'vartwig'=>$vartwig,
            'msgs'=>$bullesmsg,
            'website'=>$website,
            'admin'=>[$website->getSpwsites()[0]->isAdmin(),$this->permission]
        ]);
    }

}