<?php


namespace App\EventSubscriber;


use App\AffiEvents;
use App\Entity\User;
use App\Event\FormEvent;
use App\Event\GetResponseUserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class ResettingSubscriber implements EventSubscriberInterface
{

    private UrlGeneratorInterface $router;
    private int $tokenTtl;

    public function __construct(UrlGeneratorInterface $router, $tokenTtl)
    {
        $this->router = $router;
        $this->tokenTtl = $tokenTtl;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Affievents::RESETTING_RESET_INITIALIZE => 'onResettingResetInitialize',
            Affievents::RESETTING_RESET_SUCCESS => 'onResettingResetSuccess',
            Affievents::RESETTING_RESET_REQUEST => 'onResettingResetRequest',
        );
    }

    /**
     * @param GetResponseUserEvent $event
     */
    public function onResettingResetInitialize(GetResponseUserEvent $event)
    {
        if (!$event->getUser()->isPasswordRequestNonExpired($this->tokenTtl)) {
            $event->setResponse(new RedirectResponse($this->router->generate('resetting_request')));
        }
    }

    /**
     * @param FormEvent $event
     */
    public function onResettingResetSuccess(FormEvent $event)
    {
        /** @var $user User */
        $user = $event->getForm()->getData();
        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);
        $user->setEnabled(true);
    }

    /**
     * @param GetResponseUserEvent $event
     */
    public function onResettingResetRequest(GetResponseUserEvent $event)
    {
        if (!$event->getUser()->isAccountNonLocked()) {
            $event->setResponse(new RedirectResponse($this->router->generate('resetting_request')));
        }
    }
}