<?php


namespace App\Controller\Modules;

use App\Classe\affisession;
use App\Entity\DispatchSpace\Spwsite;
use App\Entity\Module\Formules;
use App\Form\DeleteType;
use App\Lib\MsgAjax;
use App\Module\Formulator;
use App\Normalizer\Normalizer;
use App\Repository\Entity\FormulesRepository;
use App\Repository\Entity\ModuleListRepository;
use App\Repository\Entity\PostEventRepository;
use App\Module\Evenator;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/member/wb/event")
 * @IsGranted("ROLE_CUSTOMER")
 */

class EventController extends AbstractController
{
    use affisession;


    /**
     * Create event.
     *
     * @Route("/add-event-ajx", options={"expose"=true}, name="add_event_ajx")
     * @param Request $request
     * @param Evenator $evenator
     * @return JsonResponse
     * @throws Exception
     */
    public function addEventAjx(Request $request, Evenator $evenator): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
            $data = json_decode((string) $request->getContent(), true);
            if(!$this->getUserspwsiteOfWebsite($data['board']) || !$this->admin ) return new JsonResponse(MsgAjax::MSG_ERRORRQ);
            $issue=$evenator->newEvent($data,$this->dispatch, $this->board);
            return new JsonResponse($issue);
        }else{
            return new JsonResponse(MsgAjax::MSG_ERRORRQ);
        }
    }


    /**
     * @Route("/new-event/", name="new_generic_event")
     * @Route("/new-event/{id}",  name="new_event")
     * @param $id
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function newEvent($id=null): RedirectResponse|Response
    {
        if($id){
            if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin )$this->redirectToRoute('cargo_public');
        }else{
            if(!$this->dispatch) throw new Exception('dispatch inconnu');
            $this->activeBoard();
        }
        $vartwig=$this->menuNav->templatingadmin(
            'new',
            $this->board->getNamewebsite(),
            $this->board,
            3
        );

        return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'event',
            'replacejs'=>false,
            'website'=>$this->board,
            'board' => $this->board,
            'dispatch'=>$this->dispatch,
            'event'=>null,
            'vartwig'=>$vartwig,
            'admin'=>[$this->admin,$this->permission],
            'city'=>$this->board->getLocality()->getCity()
        ]);
    }

    /**
     * @Route("/edit-event/{id}", options={"expose"=true}, name="edit_event")
     * @param Normalizer $normalizer
     * @param PostEventRepository $postEventRepository
     * @param ModuleListRepository $moduleListRepository
     * @param $id
     * @return RedirectResponse|Response
     * @throws ExceptionInterface
     * @throws NonUniqueResultException
     */
    public function editEvent(NormalizerInterface $normalizer,PostEventRepository $postEventRepository,   ModuleListRepository $moduleListRepository, $id): RedirectResponse|Response
    {
        $event = $postEventRepository->findEventById($id);
        $tab=[];
        if(!$this->getUserspwsiteOfWebsiteByKey($event->getKeymodule()) || !$this->admin )$this->redirectToRoute('cargo_public');

        $json = $normalizer->normalize($event,null,['groups' => 'edit_event']);
        $partners=$event->getPartners();

        foreach ($partners as $partner){
            $tab[]=['id'=>$partner->getId(), 'title'=>$partner->getNamewebsite(), 'pict'=>'/spaceweb/template/'.$partner->getTemplate()->getLogo()->getNamefile()];
        }

        $vartwig=$this->menuNav->templatingadmin(
            'edit',
            "edition event",
            $this->board,3);

        return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'event',
            'replacejs'=>false,
            'board' => $this->board,
            'dispatch'=>$this->dispatch,
            'website'=>$this->board,
            'event'=>$json,
            'partners'=>$tab,
            'vartwig'=>$vartwig,
            'admin'=>[$this->admin,$this->permission],
            'city'=>$this->board->getLocality()->getCity(),
            'back'=> $this->generateUrl('module_event',['city'=>$this->board->getLocality()->getCity(),'nameboard' => $this->board->getSlug()]),
        ]);
    }


    /**
     * @Route("/form-delete-event/{id}", name="form-delete_event")
     * @param Request $request
     * @param PostEventRepository $postEventRepository
     * @param Evenator $evenator
     * @param $id
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function deleteEvent(Request $request,PostEventRepository $postEventRepository, Evenator $evenator, $id): RedirectResponse|Response
    {
        if(!$event=$postEventRepository->findEventById($id)) throw new Exception('event introuvable');
        if(!$this->getUserspwsiteOfWebsiteByKey($event->getKeymodule()) || !$this->admin )$this->redirectToRoute('cargo_public');

        $form = $this->createForm(DeleteType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $evenator->removeEvent($event);
        return $this->redirectToRoute('module_event', ['city'=>$this->board->getLocality()->getCity(),'nameboard' => $this->board->getSlug()]);
        }
        $vartwig = $this->menuNav->templatingadmin(
        'delete',
        'delete event',
            $this->board,3);

        return $this->render('aff_websiteadmin/home.html.twig', [
        'directory'=>'event',
        'replacejs'=>false,
        'form' => $form->createView(),
        'website' => $this->board,
        'board' => $this->board,
        'dispatch'=>$this->dispatch,
        'vartwig' => $vartwig,
        'author'=>true,
        'admin'=>[$this->admin,$this->permission],
        'city'=>$this->board->getLocality()->getCity()
        ]);
    }

    /**
     * @Route("/form-publied-event/{id}", name="form-publied_event")
     * @param PostEventRepository $postEventRepository
     * @param Evenator $evenator
     * @param $id
     * @return RedirectResponse|Response
     */
    public function publiedEvent(PostEventRepository $postEventRepository,Evenator $evenator, $id): RedirectResponse|Response
    {
        /** @var Formules $formule */
        $event = $postEventRepository->find($id);
        $this->initBoardByKey($event->getKeymodule());
        $evenator->publiedOneEvent($event);
        return $this->redirectToRoute('module_event', ['city'=>$this->board->getLocality()->getCity(),'nameboard' => $this->board->getSlug()]);
    }

}