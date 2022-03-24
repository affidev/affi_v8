<?php


namespace App\EventSubscriber;

use App\AffiEvents;
use App\Email\RegistrationMailer;
use App\Entity\UserMap\Heuristiques;
use App\Event\CustomerEvent;
use App\Event\DisptachEvent;
use App\Event\FormEvent;
use App\Event\InvitMailEvent;
use App\Heuristique\Synapse;
use App\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ConfirmationSubscriber implements EventSubscriberInterface
{

    private RegistrationMailer $mailer;
    private TokenGeneratorInterface $tokenGenerator;
    private UrlGeneratorInterface $router;
    private RequestStack $requestStack;

    public function __construct(RegistrationMailer $mailer, TokenGeneratorInterface $tokenGenerator,
                                UrlGeneratorInterface $router, RequestStack $requestStack)
    {
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->router = $router;
        $this->requestStack = $requestStack;
    }
    public static function getSubscribedEvents()
    {
        return [
            AffiEvents::REGISTRATION_SUCCESS =>[
                ['confirmationUser', 10],
               // ['newheuriheatinscription', 5],
            ],
            AffiEvents::DISPATCH_REGISTRATION_SUCCESS =>[
                ['confirmationDispatch', 10],
                // ['newheuriheatinscription', 5],
            ],
            AffiEvents::DISPATCH_INVIT_WEBSITE =>[
                ['inviteWebsiteDispatch', 10], //todo
            ],
            AffiEvents::ADD_CLIENT_SUCCESS =>[
                ['confirmationclient', 10],
            ],
            AffiEvents::ADD_CONTACT_SUCCESS =>[
                ['confirmationcontact', 10],
            ],
            AffiEvents::INVIT_TOADMIN_BYMAIL =>[
                ['invitmailtobeadminwebsite', 10],
            ],
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function confirmationUser(FormEvent $event)
    {
        $user = $event->getForm()->getData();
        $user->setEnabled(false);
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }
        $this->mailer->sendConfirmationEmailMessage($user);
        $this->requestStack->getSession()->set('send_confirmation_email/email', $user->getEmail());
        $url = $this->router->generate('registration_check_email');
        $event->setResponse(new RedirectResponse($url));
    }

    public function confirmationDispatch(DisptachEvent $event)
    {
        $user = $event->getUser();
        $user->setEnabled(false);
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }
        $this->mailer->sendConfirmationDispatchEmailMessage($user, $event->getWebsite(),$event->getProfil());
        $this->requestStack->getSession()->set('send_confirmation_email/email', $user->getEmail());
        $url = $this->router->generate('registration_check_email');
        $event->setResponse(new RedirectResponse($url));
    }

    public function confirmationclient(DisptachEvent $event)
    {
        $user = $event->getUser();
        $user->setEnabled(false);
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }
        $this->mailer->sendConfirmationClientEmailMessage($user, $event->getWebsite());
        $this->requestStack->getSession()->set('send_confirmation_email/email', $user->getEmail());
        $url = $this->router->generate('registration_check_email');
        $event->setResponse(new RedirectResponse($url));
    }

    public function confirmationcontact(DisptachEvent $event)
    {
        $user = $event->getUser();
        $user->setEnabled(false);
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }
        $this->mailer->sendConfirmationContactEmailMessage($user, $event->getWebsite());
        $this->requestStack->getSession()->set('send_confirmation_email/email', $user->getEmail());
        $url = $this->router->generate('registration_check_email');
        $event->setResponse(new RedirectResponse($url));
    }

    public function newheuriheatinscription(FormEvent $event){

        $user = $event->getForm()->getData();
        $heuristique = new Heuristiques($user);
        $sys=Synapse::INSCRIPTION;
        //$sys=constant('Synapse::'.$source);
        $heuristique->setSem($sys[0]);
        $heuristique->setColor($sys[1]);
        $heuristique->setBinarycolor($sys[2]);
        $event->setHeuristique($heuristique);
    }

    public function inviteWebsiteDispatch(CustomerEvent $event){
        $this->mailer->sendOnvitationDispatchEmailMessage($event->getCustomer(), $event->getWebsite());
    }

    public function invitmailtobeadminwebsite(InvitMailEvent $event){
        $this->mailer->sendOnvitationMailToBeAdmin($event->getMail(), $event->getWebsite());
    }
}