<?php

namespace App\Controller\Dispatch;

use App\Classe\initdispatch;
use App\Entity\Customer\Customers;
use App\Entity\DispatchSpace\Spwsite;
use App\Entity\User;
use App\Exeption\RedirectException;
use App\Form\NewSpwType;
use App\Lib\Links;
use App\Repository\Entity\WebsiteRepository;
use App\Repository\UserRepository;
use App\Service\Dispatch\DispatchFactor;
use App\Service\SpaceWeb\SpacewebFactor;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


/**
 * @Route("/customer/initlocalizer/")
 * @IsGranted("ROLE_CUSTOMER")
 */

class InitDispatchController extends AbstractController
{
    use initdispatch;


    /**
     * @Route("init-locate-customer", options={"expose"=true},  name="init_locate_customer")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param DispatchFactor $dispatchFactor
     * @param SpacewebFactor $spaceWebtor
     * @return Response
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws NonUniqueResultException
     * @throws RedirectException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function initiLocateCustomer(Request $request,UserRepository $userRepository,DispatchFactor $dispatchFactor,SpacewebFactor $spaceWebtor): Response
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
                $dispatchFactor->confirmDispatch($dispatch,$coord);
            }else{
                $dispatch=$dispatchFactor->NewDispatch($customer,$coord);
                $spaceWebtor->createFirstWebsite($customer,$dispatch);
            }

           if (!$this->requestStack->getSession()->has('idcustomer')) $this->sessioninit->initCustomer($user);// todo a savoir pourquoi on test cela ??

            return $this->redirectToRoute('cargo_public');
            //return $this->redirectToRoute('intit_board_default');
        }else{
            return $this->redirectToRoute('confirmed');  // si pas de loc on retourne a la page de selection de ville
        }
    }

    /**
     * @Route("confirm-invit-admin-website/{id}",  name="confirm_invit_admin_website")
     * @param $id
     * @param UserRepository $userRepository
     * @param SpacewebFactor $spaceWebtor
     * @param DispatchFactor $dispatchFactor
     * @param WebsiteRepository $websiteRepository
     * @return Response
     * @throws ClientExceptionInterface
     * @throws NonUniqueResultException
     * @throws RedirectException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function confirInvitAdminWebsite($id,UserRepository $userRepository,SpacewebFactor $spaceWebtor,DispatchFactor $dispatchFactor, WebsiteRepository $websiteRepository): Response
    {
        /** @var User $user */
        $user=$userRepository->findCustomerAndProfilUser($this->security->getUser());
        /** @var Customers $customer */
        $customer=$user->getCustomer();
        $website=$websiteRepository->find($id);
        if(!$user->getCharte()) {   //todo faire la validatioçn d'une vrai charte - actuellement mis a true directement a la creation
            $user->setCharte(true);
            $this->em->persist($user);
            $this->em->flush();
        }
        if($dispatch=$customer->getDispatchspace()){
            $spaceWebtor->confirmLocByWebsite($dispatch,$website);
        }else{
            $dispatch=$dispatchFactor->NewDispatchByWebsite($customer,$website);
            $spaceWebtor->createFirstWebsite($customer,$dispatch);
        }
        if (!$this->requestStack->getSession()->has('idcustomer')) $this->sessioninit->initCustomer($user);// todo a savoir pourquoi on test cela ??

        $vartwig = ['maintwig' => "checkInvitadmin", 'title' => "confirmation admin panneau"];
        return $this->render('aff_security/home.html.twig', [
            'directory' => 'registration',
            'replacejs' => $replacejs ?? null,
            'vartwig' => $vartwig,
            'user' => $user,
            'website' => $website,
        ]);

    }



    // todo atention. Cette action était renvoyée par le intidispatch pour la creation d'office du panneau local de l'utilisateur - supprimé
    /**
     * @Route("intilaisation-de-votre-panneau", name="intit_board_default")
     * @param Request $request
     * @param SpacewebFactor $spaceWebtor
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function newWebsite(Request $request, SpacewebFactor $spaceWebtor): RedirectResponse|Response
    {
        if(!$this->dispatch) throw new Exception('dispatch inconnu');
        $spwsite=new Spwsite();
        $spwsite->setIsdefault(true);
        $form=$this->createForm(NewSpwType::class,$spwsite);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $website=$spaceWebtor->newWebsite($this->customer,$this->dispatch, $spwsite, $form);
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
