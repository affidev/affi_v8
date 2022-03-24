<?php


namespace App\EventSubscriber;


use App\AffiEvents;
use App\Email\RegistrationMailer;
use App\Event\UserEvent;
use App\Util\TokenGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InforregistrationSubscriber implements EventSubscriberInterface
{

    private $mailer;
    private $tokenGenerator;
    private $router;
    private $session;
    /**
     * @var EntityManagerInterface
     */
    private $manager;


    public function __construct(RegistrationMailer $mailer, TokenGeneratorInterface $tokenGenerator,
                                UrlGeneratorInterface $router, SessionInterface $session, EntityManagerInterface $manager)
    {
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->router = $router;
        $this->session = $session;
        $this->manager = $manager;
    }

    public static function getSubscribedEvents()
    {
        return [
            AffiEvents::ADD_REGISTRATION_SUCCESS => [
                ['addUserSpwsite', 10],
            ],
            AffiEvents::ADD_DISPATCH_SUCCESS => [
                ['addUserDispatch', 10],
            ],
        ];
    }

    /**
     * @param UserEvent $event
     */
    public function addUserSpwsite(UserEvent $event)
    {
        $user = $event->getUser();
        $user->setEnabled(false);
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }
       // $this->mailer->sendConfirmationEmailMessage($user);
        //$this->session->set('send_info_regsitration_email/email', $user->getEmail());
        //$url = $this->router->generate('registration_check_email');
       // $event->setResponse(new RedirectResponse($url));
    }

    public function addUserDispatch(UserEvent $event)
    {
        $user = $event->getUser();
        $user->setEnabled(false);
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }
        //$this->mailer->sendConfirmationDispatchEmailMessage($user);
        //$this->session->set('send_info_regsitration_email/email', $user->getEmail());
        //$url = $this->router->generate('registration_check_email');
        // $event->setResponse(new RedirectResponse($url));
    }

}