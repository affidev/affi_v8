<?php


namespace App\EventSubscriber;


use App\AffiEvents;
use App\Entity\User;
use App\Event\FormEvent;
use App\Event\GetResponseNullableUserEvent;
use App\Event\GetResponseUserEvent;
use App\Util\PasswordUpdater;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



class ChangePassWordSubscriber implements EventSubscriberInterface
{

    private UrlGeneratorInterface $router;
    private int $tokenTtl;
    private PasswordUpdater $passwordupdater;

    public function __construct(UrlGeneratorInterface $router, PasswordUpdater $passwordUpdater)
    {
        $this->router = $router;
        $this->tokenTtl = 3600;
        $this->passwordupdater=$passwordUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Affievents::RESETTING_RESET_INITIALIZE => 'onResettingPasswordInitialize' ,
            Affievents::RESETTING_RESET_SUCCESS => 'onResettingPasswordSuccess' ,
            Affievents::RESETTING_RESET_REQUEST => 'onResettingPasswordRequest' ,
            Affievents::CHANGE_PASSWORD_TEST => 'onResettingPasswordTest',
            //Affievents::CHANGE_PASSWORD_INITIALIZE => 'onResettingPasswordInitialize',
            //Affievents::CHANGE_PASSWORD_SUCCESS => 'onResettingPasswordSuccess',
           // Affievents::CHANGE_PASSWORD_COMPLETED => 'onResettingPasswordRequest',
           // AffiEvents::CHANGE_PASSWORD_REQUEST => 'onResettingPasswordRequest',
        );
    }
    /**
     * @param GetResponseNullableUserEvent $event
     */
    public function onResettingPasswordTest(GetResponseNullableUserEvent $event)
    {
        if(!$event->getUser()){
            $event->setResponse(new RedirectResponse($this->router->generate('no_email_find')));
        }
    }

    /**
     * @param GetResponseUserEvent $event
     */
    public function onResettingPasswordInitialize(GetResponseUserEvent $event)
    {
        if (!$event->getUser()->isPasswordRequestNonExpired($this->tokenTtl)) {
            $event->setResponse(new RedirectResponse($this->router->generate('forget_password_request')));
        }
    }
    /**
     * @param FormEvent $event
     */
    public function onResettingPasswordSuccess(FormEvent $event)
    {
        /** @var $user User */
        $user = $event->getForm()->getData();
        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);
        $user->setEnabled(true);
        $this->passwordupdater->hashPasswordstring($user, $user->getPlainPassword());
    }

    /**
     * @param GetResponseUserEvent $event
     */
    public function onResettingPasswordRequest(GetResponseUserEvent $event)
    {
        if (!$event->getUser()->isAccountNonLocked()) {
            $event->setResponse(new RedirectResponse($this->router->generate('forget_password_request')));
        }
    }
}