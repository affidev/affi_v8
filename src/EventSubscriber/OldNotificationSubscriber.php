<?php


namespace App\EventSubscriber;

use App\AffiEvents;
use App\Email\NotificationMailer;
use App\Entity\Notifications\SuiviNotif;
use App\Event\MessageEvent;
use App\Event\PostEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OldNotificationSubscriber implements EventSubscriberInterface
{

    private NotificationMailer $mailer;
    private UrlGeneratorInterface $router;
    private EntityManagerInterface $em;
    private RequestStack $requestStack;


    public function __construct(NotificationMailer $mailer, UrlGeneratorInterface $router, RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->em=$em;
    }
    public static function getSubscribedEvents()
    {
        return [
            AffiEvents::NOTIFICATION_NEW_MESSAGE =>[
                ['addMessage', 10],
            ],
            AffiEvents::NOTIFICATION_CONTACT_NEW_MESSAGE =>[
                ['addMessagecontact', 10],
            ],
            AffiEvents::NOTIFICATION_ADD_COMMENT =>[
                ['addMessage', 10],
            ],
            AffiEvents::NOTIFICATION_NEW_POST =>[
                ['notificationNew', 10],
            ],

            AffiEvents::NOTIFICATION_NEW_OFFRE =>[
                ['notificationOther', 10],
            ],
            AffiEvents::NOTIFICATION_NEW_MODULE =>[
                ['notificationOther2', 10],
            ],
        ];
    }

    /**
     * @param MessageEvent $event
     */
    public function addMessage(MessageEvent $event)
    {
        $notif = new SuiviNotif();
        $notif->setIsmember(true);
        $notif->setClassmodule("message");
        $notif->setClassmoduleid($event->getMessage()->getId());
        $notif->setSubject($event->getMessage()->getSubject());
        $notif->setWebsite($event->getWebsite());
        $notif->setDispatch($event->getDispatch());
        $this->em->persist($notif);
        $this->em->flush();
    }

    public function addMessagecontact(MessageEvent $event)
    {
        $notif = new SuiviNotif();
        $notif->setIsmember(false);
        $notif->setClassmodule("message");
        $notif->setClassmoduleid($event->getMessage()->getId());
        $notif->setSubject($event->getMessage()->getSubject());
        $notif->setWebsite($event->getWebsite());
        $notif->setContact($event->getContact());
        $this->em->persist($notif);
        $this->em->flush();
    }

    public function notificationNew(PostEvent $event)
    {
        $user = $event->getUser();
        $user->setEnabled(false);
        $this->mailer->oldsendConfirmationDispatchEmailMessage($user, $event->getWebsite());
        $this->requestStack->getSession()->set('send_confirmation_email/email', $user->getEmail());
        $url = $this->router->generate('registration_check_email');
        $event->setResponse(new RedirectResponse($url));
    }

    public function notificationOther(OffreEvent $event)
    {
        $user = $event->getUser();
        $user->setEnabled(false);
        $this->mailer->oldsendConfirmationClientEmailMessage($user, $event->getWebsite());
        $this->requestStack->getSession()->set('send_confirmation_email/email', $user->getEmail());
        $url = $this->router->generate('registration_check_email');
        $event->setResponse(new RedirectResponse($url));
    }

    public function notificationOther2(ModuleEvent $event)
    {
        $user = $event->getUser();
        $user->setEnabled(false);
        $this->mailer->oldsendConfirmationContactEmailMessage($user, $event->getWebsite());
        $this->requestStack->getSession()->set('send_confirmation_email/email', $user->getEmail());
        $url = $this->router->generate('registration_check_email');
        $event->setResponse(new RedirectResponse($url));
    }
}