<?php

namespace App\Controller\Security;

use App\Classe\affisessionpublique;
use App\AffiEvents;
use App\Event\FormEvent;
use App\Form\UserType;
use App\Service\Registration\Identificator;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/security/admin")
 */

class NewUserController extends AbstractController
{
    use affisessionpublique;


    /**
     * @Route("/new-Identify", name="new_identify")
     */
    public function controlNewIdentify(): RedirectResponse|Response|null
    {
        $session = $this->requestStack->getSession();
        if ($session->has('identify')) $session->remove('identify');
        return $this->redirectToRoute('new_identify_stape');
    }


    /**
     * @Route("/create-board-stape", name="new_identify_stape")
     * @throws NonUniqueResultException
     */
    public function newIdentify(EventDispatcherInterface $eventDispatcher,Identificator $identificator, Request $request): RedirectResponse|Response|null
    {
        $session = $this->requestStack->getSession();
        $user =$identificator->newUser();
        if(!$session->has('identify'))  $session->set('identify',uniqid($prefix = "identify", $more_entropy = false));
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted()) {
            if ($form->isValid()) {
                $session->remove('identify');
                $identificator->creator($user, $form);
                $event = new FormEvent($form, $request);
                $eventDispatcher->dispatch($event, AffiEvents::REGISTRATION_SUCCESS);
                $this->em->persist($user);
                $this->em->flush();

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('confirmed');
                    $response = new RedirectResponse($url);
                }
                /*
                $eventDispatcher->dispatch( new FilterUserResponseEvent($user, $request, $response),AffiEvents::REGISTRATION_COMPLETED);
                */
                return $response;
            }
            $event = new FormEvent($form, $request);
            $eventDispatcher->dispatch($event, AffiEvents::REGISTRATION_FAILURE );
            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }
        $vartwig=['maintwig'=>"identifyOne",'title'=>""];
        return $this->render('aff_security/home.html.twig', [
            'directory'=>'auto_create',
            'replacejs'=>$replacejs??null,
            'vartwig'=>$vartwig,
            'identify'=>$session->get('identify'),
            'form' => $form->createView()]);
    }



    /**
     * @Route("/create-board-stape-contact/{id}", name="new_identify_stape_contact")
     * @throws NonUniqueResultException
     */
    public function newIdentifyContact(EventDispatcherInterface $eventDispatcher,Identificator $identificator, Request $request, $id): RedirectResponse|Response|null
    {
        $session = $this->requestStack->getSession();
        $user =$identificator->newUser();
        if(!$session->has('identify'))  $session->set('identify',uniqid($prefix = "identify", $more_entropy = false));
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted()) {
            if ($form->isValid()) {
                $session->remove('identify');
                $identificator->creator($user, $form);
                $event = new FormEvent($form, $request);
                $eventDispatcher->dispatch($event, AffiEvents::REGISTRATION_SUCCESS);
                $this->em->persist($user);
                $this->em->flush();

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('confirmed');
                    $response = new RedirectResponse($url);
                }
                /*
                $eventDispatcher->dispatch( new FilterUserResponseEvent($user, $request, $response),AffiEvents::REGISTRATION_COMPLETED);
                */
                return $response;
            }
            $event = new FormEvent($form, $request);
            $eventDispatcher->dispatch($event, AffiEvents::REGISTRATION_FAILURE );
            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }
        $vartwig=['maintwig'=>"identifyOne",'title'=>"Ouverture de votre panneau AffiChanGe"];
        return $this->render('aff_security/home.html.twig', [
            'directory'=>'auto_create',
            'replacejs'=>$replacejs??null,
            'vartwig'=>$vartwig,
            'identify'=>$session->get('identify'),
            'form' => $form->createView()]);
    }
}
