<?php


namespace App\Controller\Customer;


use App\Classe\affisession;
use App\Entity\Customer\Customers;
use App\Entity\DispatchSpace\Spwsite;
use App\Event\WebsiteCreatedEvent;
use App\Exeption\RedirectException;
use App\Form\NewSpwType;
use App\Form\ProfilType;
use App\Lib\Links;
use App\Repository\Entity\PrivateConversRepository;
use App\Repository\Entity\SpwsiteRepository;
use App\Service\Messages\PrivateMessageor;
use App\Service\Registration\Sessioninit;
use App\Service\SpaceWeb\SpacewebFactor;
use App\Service\SpaceWeb\Tagatot;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


/**
 * @Route("/customer/profil/")
 * @IsGranted("ROLE_CUSTOMER")
 */

class ProfilCustomerController extends AbstractController
{
    use affisession;

    /**
     * @Route("mon-espace-affichange", name="profil_dispatch")
     * @param PaginatorInterface $paginator
     * @param PrivateConversRepository $privateConversRepository
     * @param PrivateMessageor $messageor
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function profilDispatch(PaginatorInterface $paginator, PrivateConversRepository $privateConversRepository, PrivateMessageor $messageor, Request $request): RedirectResponse|Response
    {
        if(!$this->dispatch) return $this->redirectToRoute('cargo_public');
        $this->activeBoard();
        /*
        $pagination = $paginator->paginate(
            $privateConversRepository->findMsgswebsiteQuery($this->dispatch->getId()),
            $request->query->getInt('page', 1),
            10
        );

        $msgs = $messageor->sortmessage($pagination, $this->dispatch);
*/

        $vartwig=$this->menuNav->newtemplateControlCustomer(
            Links::CUSTOMER_LIST,
            'profilshow',
            "Mon espace AffiChanGe",
            1
        );

        return $this->render('aff_account/home.html.twig', [
            'directory'=>"profil",
           'replacejs'=>$replacejs??null,
            'board'=>$this->board,
            //"route"=>"locate",
            'tfile'=>"namedispatch",
            'dispatch'=>$this->dispatch,
           // 'msgs' => $msgs,
           // 'pagination' => $pagination ?? null,
          //  'wbsitesuser'=>$websituser??null,
            'vartwig'=>$vartwig,
            'permissions'=>$this->permission
        ]);
    }

    /**
     * @Route("mon-compte-infos", name="profil_customer")
     * @param SpwsiteRepository $spwsiteRepository
     * @param PaginatorInterface $paginator
     * @param PrivateConversRepository $privateConversRepository
     * @param PrivateMessageor $messageor
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function profilCustomer(SpwsiteRepository $spwsiteRepository, PaginatorInterface $paginator, PrivateConversRepository $privateConversRepository, PrivateMessageor $messageor, Request $request): RedirectResponse|Response
    {
        if(!$this->dispatch) return $this->redirectToRoute('cargo_public');
        $this->activeBoard();

        $vartwig=$this->menuNav->newtemplateControlCustomer(
            Links::CUSTOMER_LIST,
            'profilcontact',
            "Mes infos de contacts",
            2
        );

        return $this->render('aff_account/home.html.twig', [
            'directory'=>"profil",
            'replacejs'=>$replacejs??null,
            'board'=>$this->board,
            'dispatch'=>$this->dispatch,
            'vartwig'=>$vartwig,
            'permissions'=>$this->permission
        ]);
    }

    /**
     * @Route("modification-nom-compte", name="edit_profil_customer")
     * @param Request $request
     * @param Sessioninit $sessioninit
     * @return RedirectResponse|Response
     */
    public function updateProfilCustomer(Request $request, Sessioninit $sessioninit): RedirectResponse|Response
    {
        if(!$this->dispatch) return $this->redirectToRoute('cargo_public');
        $this->activeBoard();
        if($this->dispatch->getLocality()==null)return $this->redirectToRoute('spaceweblocalize_init');
        $customer=$this->customer;
        $form=$this->createForm(ProfilType::class,$this->dispatch->getCustomer()->getProfil());
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // $spaceWebtor->majDistach( $dispatch, $form); //todo ???? a voir
            $this->em->flush();
            //$this->reClearInit();
            //$sessioninit->initSession($this->dispatch);
            $this->addFlash('success', 'Vos informations ont bien été mises à jour');
            return $this->redirectToRoute('profil_customer');
        }
        $vartwig=$this->menuNav->newtemplateControlCustomer(
            Links::CUSTOMER_LIST,
            'profiledit',
            "Mon compte",
            3  );

        return $this->render('aff_account/home.html.twig', [
            'replacejs'=>$replacejs??null,
            'directory'=>"profil",
            'board'=>$this->board,
            'dispatch'=>$this->dispatch,
            'customer'=>$customer,
            'vartwig'=>$vartwig,
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("infos-contacts", name="update_infocontact")
     * @param Request $request
     * @param SpacewebFactor $spaceWebtor
     * @param Sessioninit $sessioninit
     * @return RedirectResponse|Response
     * @throws RedirectException
     * @throws NonUniqueResultException
     */
    public function updateSpace(Request $request, SpacewebFactor $spaceWebtor, Sessioninit $sessioninit): RedirectResponse|Response
    {
        if(!$this->dispatch) return $this->redirectToRoute('cargo_public');
        $this->activeBoard();
        $customer=$this->dispatch->getCustomer();
        /** @var Customers $customer */
        $identity=$customer->getProfil();
        if($this->dispatch->getLocality()==null)return $this->redirectToRoute('spaceweblocalize_init');
        $form=$this->createForm(ProfilType::class,$identity);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // $spaceWebtor->majDistach( $dispatch, $form); //todo ???? a voir
            $this->em->flush();
            $this->reClearInit();
            $sessioninit->initSession($this->dispatch);
            $this->addFlash('success', 'Vos informations ont bien été mises à jour');
            return $this->redirectToRoute('profil_customer');
        }
        $vartwig=$this->menuNav->newtemplateControlCustomer(
            Links::CUSTOMER_LIST,
            'profiledit',
            "infos contacts",
            4  );

        return $this->render('aff_account/home.html.twig', [
            'directory'=>"profil",
            'replacejs'=>$replacejs??null,
            'twigform'=>'form_update',
            'dispatch'=>$this->dispatch,
            'board'=>$this->board,
            'vartwig'=>$vartwig,
            'form'=>$form->createView(),
            'permissions'=>$this->permission
        ]);
    }


    /**
     * @Route("choice-board", name="choice_board")
     * @return RedirectResponse|Response
     */
    public function choiceBoard(): RedirectResponse|Response
    {
        if(!$this->dispatch) return $this->redirectToRoute('api-error',['err'=>1]);
        $this->activeBoard();
        $vartwig=$this->menuNav->newtemplateControlCustomer(
            Links::CUSTOMER_LIST,
            'choice',
            "Mes préférences",
            4
        );

        return $this->render('aff_account/home.html.twig', [
            'directory'=>"profil",
            'replacejs'=>$replacejs??null,
            'dispatch'=>$this->dispatch,
            'board'=>$this->board,
            'vartwig'=>$vartwig,
            'tag'=>['name'=>$city??null,'active'=>true, 'l_class'=>"customer"], //tag pour localitate (barresearchinfo.twig)
            'permissions'=>$this->permission
        ]);
    }

    /**
     * @Route("change-board/{id}", name="change_board")
     * @param $id
     * @return RedirectResponse|Response
     */
    public function changeDefaultBoard($id): RedirectResponse|Response
    {
        if(!$this->dispatch) return $this->redirectToRoute('api-error',['err'=>1]);
        if(!$test=$this->reinitDefaultBoard($id))return $this->redirectToRoute('api-error',['err'=>1]);
        return $this->redirectToRoute('choice_board');
    }


    /**
     * @Route("add-panneau/", name="add_board")
     * @param Request $request
     * @param SpacewebFactor $spacewebFactor
     * @return RedirectResponse|Response
     */
    public function addBoard(Request $request,Tagatot $tagatot, SpacewebFactor $spacewebFactor): RedirectResponse|Response
    {
        if(!$this->dispatch) return $this->redirectToRoute('api-error',['err'=>1]);
       // $this->activeBoard();

        $spwsite=new Spwsite();
        $form=$this->createForm(NewSpwType::class,$spwsite);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $website = $spacewebFactor->addWebsiteLocality($this->dispatch, $spwsite, $form);
            $tagatot->majTagCat($website);
           // $event= new WebsiteCreatedEvent($website);
           // $dispatcher->dispatch($event, WebsiteCreatedEvent::CREATE);
            return $this->redirectToRoute('website_edit',['id'=>$website->getId()]);
        }

        $vartwig=$this->menuNav->newtemplateControlCustomer(
            Links::CUSTOMER_LIST,
            'customerpass',
            "Création d'un nouveau panneau",
            4
        );

        return $this->render('aff_account/home.html.twig', [
            'directory'=>'gestion',
            'replacejs'=>$replacejs??null,
            //'board'=>$this->board,
            'dispatch'=>$this->dispatch,
            'vartwig'=>$vartwig,
            'form'=>$form->createView(),
            'tag'=>['name'=>$city??null,'active'=>true, 'l_class'=>"addboard"],
        ]);
    }


    /**
     * @Route("identiy-board-self/{id}", name="identifymember")  //todo ern cours
     * @param Request $request
     * @param SpacewebFactor $spacewebFactor
     * @return RedirectResponse|Response
     */
    public function identifySelfBoard(Request $request,Tagatot $tagatot, SpacewebFactor $spacewebFactor,EventDispatcherInterface $dispatcher,$id): RedirectResponse|Response
    {
        if(!$this->dispatch) return $this->redirectToRoute('api-error',['err'=>1]);
        $this->activeBoard();

        $spwsite=new Spwsite();
        $form=$this->createForm(NewSpwType::class,$spwsite);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $website = $spacewebFactor->addWebsiteLocality($this->dispatch, $spwsite, $form);
            $tagatot->majTagCat($website);
            // $event= new WebsiteCreatedEvent($website);
            // $dispatcher->dispatch($event, WebsiteCreatedEvent::CREATE);
            return $this->redirectToRoute('website_edit',['id'=>$website->getId()]);
        }

        $vartwig=$this->menuNav->newtemplateControlCustomer(
            Links::CUSTOMER_LIST,
            'customerpass',
            "Création d'un nouveau panneau",
            4
        );

        return $this->render('aff_account/home.html.twig', [
            'directory'=>'gestion',
            'replacejs'=>$replacejs??null,
            'board'=>$this->board,
            'dispatch'=>$this->dispatch,
            'vartwig'=>$vartwig,
            'form'=>$form->createView(),
            'tag'=>['name'=>$city??null,'active'=>true, 'l_class'=>"addboard"],
        ]);
    }


    /**
     * @Route("change-localize", options={"expose"=true}, name="localize_change")
     * @IsGranted("ROLE_CUSTOMER")
     * @return RedirectResponse|Response
     */
    public function changelocalize(): RedirectResponse|Response
    {
        if(!$this->dispatch) return $this->redirectToRoute('api-error',['err'=>1]);
        $this->activeBoard();
        $vartwig=$this->menuNav->newtemplateControlCustomer(
            Links::CUSTOMER_LIST,
            'localizer',
            "localizer",
            4
        );

        return $this->render('aff_account/home.html.twig', [
            'directory'=>"profil",
            'replacejs'=>$replacejs??null,
            'localizer'=>true,
            'board'=>$this->board,
            'dispatch'=>$this->dispatch,
            'vartwig'=>$vartwig,
            'tag'=>['name'=>$city??null,'active'=>true, 'l_class'=>"customer"], //tag pour localitate (barresearchinfo.twig)
            'permissions'=>$this->permission
        ]);
    }
}
