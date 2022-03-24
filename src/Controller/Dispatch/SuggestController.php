<?php

namespace App\Controller\Dispatch;

use App\Classe\affisession;
use App\Entity\Customer\Customers;
use App\Entity\DispatchSpace\Spwsite;
use App\Entity\User;
use App\Entity\Users\Contacts;
use App\Entity\Users\ProfilUser;
use App\Entity\Websites\Tbsuggest;
use App\Entity\Websites\Template;
use App\Entity\Websites\Website;
use App\Event\SuggestPartnerEvent;
use App\Event\WebsiteCreatedEvent;
use App\Exeption\RedirectException;
use App\Form\NewSpwType;
use App\Lib\Links;
use App\Lib\MsgAjax;
use App\Repository\Entity\ContactRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Repository\UserRepository;
use App\Service\Localisation\LocalisationServices;
use App\Service\SpaceWeb\SpacewebFactor;
use App\Util\Canonicalizer;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


/**
 * @Route("/customer/suggest-boardpartner/")
 * @IsGranted("ROLE_CUSTOMER")
 */

class SuggestController extends AbstractController
{
    use affisession;


    /**
     * @Route("/new-suggest", options={"expose"=true}, name="new_suggest")
     * @param Request $request
     * @param SpacewebFactor $factor
     * @param LocalisationServices $locate
     * @param EventDispatcherInterface $dispatcher
     * @param NormalizerInterface $normalizer
     * @param WebsiteRepository $websiteRepository
     * @param UserRepository $userRepository
     * @param ContactRepository $contactRepository
     * @param Canonicalizer $emailCanonicalizer
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function SuggestWebsSite(Request $request,SpacewebFactor $factor,LocalisationServices $locate,EventDispatcherInterface $dispatcher,
                                    NormalizerInterface $normalizer, WebsiteRepository $websiteRepository, UserRepository $userRepository,
                                    ContactRepository $contactRepository, Canonicalizer $emailCanonicalizer): JsonResponse
    {

        if($request->isXmlHttpRequest())
        {

            $data = json_decode((string) $request->getContent(), true);
            if($wb=$websiteRepository->findOneBy(['namewebsite'=>$data['name']])) return new JsonResponse(['success'=> false, 'wb'=>$normalizer->normalize($wb,null,['groups' => 'edit_event']) ]);
            if(!$this->selectedPwBoard($data['board']))return new JsonResponse(MsgAjax::MSG_ERRORRQ);

            $tabsuggets=new Tbsuggest();

            $partner=new Website();
            $template=new Template();
            $partner->setTemplate($template);
            $partner->setNamewebsite($data['name']);
            $partner->setAttached(false);
            if($data['ville']!=""){
                $gps = $locate->findLocate($data['ville']);
                if($gps==null) return new JsonResponse(['success'=> false ]);
                $partner->setLocality($gps);
            }
            $this->em->persist($partner);
            $this->em->flush();

            $tabsuggets->setPrewebsite($partner);
            $tabsuggets->setInvitor($this->board);
            $issue=$factor->addPartner($this->board, $partner);//todo

            $testuser=$userRepository->findOneBy(array('email'=> $data['email']));
            $contact = $contactRepository->findOneBy(array('emailCanonical' => $data['email']));

            if(!$testuser && !$contact){
                $to = new Contacts();
                $identity = new profilUser();
                $to->setValidatetop(true);
                $identity->setFirstname("");
                $identity->setLastname("");
                $identity->setEmailfirst($data['email']);
                $to->setEmailCanonical($emailCanonicalizer->canonicalize($data['email'])); //todo voir si necessaire de garder
                $to->setUseridentity($identity);
                $this->em->persist($to);
                $tabsuggets->setContact($to);
                $this->em->persist($tabsuggets);
                $this->em->flush();
                $event=New SuggestPartnerEvent($tabsuggets);
                $dispatcher->dispatch($event, SuggestPartnerEvent::NEWCONTACT);
            }elseif ($testuser){  // peu probable mais il faut traiter ce cas todo
                $to=$testuser->getCustomer()->getDispatchspace();
                $tabsuggets->setDispatch($to);
                $this->em->persist($tabsuggets);
                $this->em->flush();
                $event=New SuggestPartnerEvent($tabsuggets);
                $dispatcher->dispatch($event, SuggestPartnerEvent::DISPATCH);
            }elseif ($contact){  // iem peu probable mais il faut traiter ce cas  todo
                $to=$contact;
                $tabsuggets->setContact($to);
                $this->em->persist($tabsuggets);
                $this->em->flush();
                $event=New SuggestPartnerEvent($tabsuggets);
                $dispatcher->dispatch($event, SuggestPartnerEvent::CONTACT);
            }else{
                return new JsonResponse(['success'=> false ]);
            }
            /*  supprimer car remplacé par le Attached état
            $pw=New Spwsite();
            $pw->setDisptachwebsite($this->getDispatch());
            $pw->setRole("member");
            $pw->setIsadmin(false);
            $website->addSpwsite($pw);
            */

            return new JsonResponse(true);
        }else{
            return new JsonResponse(MsgAjax::MSG_ERRORRQ);
        }
    }

    /**
     * @Route("suggestBoardpartner/{id}", options={"expose"=true},  name="suggest_boardpartner")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param SpacewebFactor $spaceWebtor
     * @return Response
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws NonUniqueResultException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface|RedirectException
     */
    public function attacheSuggets(Request $request,UserRepository $userRepository,SpacewebFactor $spaceWebtor): Response //todo completement
    {
        /** @var User $user */
        $user=$userRepository->findCustomerAndProfilUser($this->security->getUser());
        /** @var Customers $customer */
        $customer=$user->getCustomer();

        $lat=$request->query->get('lat');
        $lon=$request->query->get('lon');

        if($lat && $lon) {
            $coord = ['lat' => $lat, 'lon' => $lon];
            if(!$user->getCharte()) {   //todo faire la validatioçn d'une vrai charte - actuellement mis a true directement a la creation
                $user->setCharte(true);
                $this->em->persist($user);
                $this->em->flush();
            }
            if($dispatch=$customer->getDispatchspace()){
                $spaceWebtor->confirmDispatch($dispatch,$coord);
            }else{
                $spaceWebtor->NewDispatch($customer,$coord);
            }

           if (!$this->requestStack->getSession()->has('idcustomer')) $this->sessioninit->initCustomer($user);// todo a savoir pourquoi on test cela ??
            return $this->redirectToRoute('intit_board_default');
        }else{
            return $this->redirectToRoute('confirmed');  // si pas de loc on retourne a la page de selection de ville
        }
    }



    // todo atention. Cette action est renvoyée par le intidispatch pour la creation d'office du panneau local de l'utilisateur
    /**
     * @Route("intilaisation-de-votre-panneau", name="intit_board_default")
     * @param Request $request
     * @param SpacewebFactor $spaceWebtor
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function newWebsite(Request $request,EventDispatcherInterface $dispatcher, SpacewebFactor $spaceWebtor): RedirectResponse|Response
    {
        if(!$this->iddispatch) throw new Exception('dispatch inconnu');
        $this->dispatch=$this->repodispacth->findForInit($this->iddispatch);
        $spwsite=new Spwsite();
        $spwsite->setIsdefault(true);
        $form=$this->createForm(NewSpwType::class,$spwsite);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $website=$spaceWebtor->newWebsite($this->dispatch->getCustomer(),$this->dispatch, $spwsite, $form);
            $event= new WebsiteCreatedEvent($website);
            $dispatcher->dispatch($event, WebsiteCreatedEvent::CREATE);
            return $this->redirectToRoute('cargo_public');
        }
        $vartwig=$this->menuNav->templateControl(
            Links::PUBLIC,
            'initboard',
            "creation de votre panneau de publication",
            'all');

        return $this->render('aff_account/home.html.twig', [
            'directory'=>"registration",
            'dispatch'=>$this->dispatch,
            'replacejs'=>$replacejs??null,
            'vartwig'=>$vartwig,
            'website'=>null,
            'form'=>$form->createView(),
        ]);
    }

}
