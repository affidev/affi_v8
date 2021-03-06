<?php


namespace App\Email;

use App\Entity\User;
use App\Entity\Websites\Website;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NotificationMailer
{

    protected Sender $sender;
    protected UrlGeneratorInterface $router;
    protected $parameters;


    /**
     * Mailer constructor.
     * @param Sender $sender
     * @param UrlGeneratorInterface $router
     */
    public function __construct(Sender $sender, UrlGeneratorInterface  $router)
    {
        $this->sender = $sender;
        $this->router = $router;
    }

    public function notifMailerMessage(User $user)
    {
        $template ='aff_notifications/security/confirmation.email.twig';
        $url = $this->router->generate('registration_confirm', array(
            'token' => $user->getConfirmationToken()),
            UrlGeneratorInterface::ABSOLUTE_URL);
        $this->sender->goSendMessage(
            $template,
            $context=['exp' =>'affichange','dest'=>$user->getEmail(),'confirmationUrl' => $url,'user' => $user, 'msg'=>""],
            'confirmez votre inscription sur affiChange',
            'registration');
    }

    public function oldsendAskConfirmationDispatchEmailMessage(User $user)
    {
        $template ='aff_notifications/contact/invitationweb.email.twig';
        $url = $this->router->generate('dispatch_confirm', array(
            'token' => $user->getConfirmationToken()),
            UrlGeneratorInterface::ABSOLUTE_URL);
        $this->sender->goSendMessage(
            $template,
            $context=['exp' =>'affichange','dest'=>$user->getEmail(),'confirmationUrl' => $url,'user' => $user, 'msg'=>""],
            'Votre invitation a rejoindre affiChange',
            'registration');
    }

    public function oldsendConfirmationEmailMessage(User $user)
    {
        $template ='aff_notifications/security/confirmation.email.twig';
        $url = $this->router->generate('registration_confirm', array(
            'token' => $user->getConfirmationToken()),
            UrlGeneratorInterface::ABSOLUTE_URL);
        $this->sender->goSendMessage(
            $template,
            $context=['exp' =>'affichange','dest'=>$user->getEmail(),'confirmationUrl' => $url,'user' => $user, 'msg'=>""],
            'confirmez votre inscription sur affiChange',
            'registration');
    }

    public function oldsendConfirmationDispatchEmailMessage(User $user,Website $website)
    {
        $template ='aff_notifications/member/invitationweb.email.twig';
        $url = $this->router->generate('dispatch_confirm', array(
            'token' => $user->getConfirmationToken()),
            UrlGeneratorInterface::ABSOLUTE_URL);
        $this->sender->goSendMessage(
            $template,
            $context=[
                'exp' =>'affichange',
                'dest'=>$user->getEmail(),
                'confirmationUrl' => $url,
                'user' => $user,
                'website'=>$website,
                'msg'=>""],
            'Votre invitation a rejoindre AffiChanGe',
            'registration');
    }

    public function oldsendConfirmationClientEmailMessage(User $user,Website $website)
    {
        $template ='aff_notifications/client/byshop.email.twig';
        $url = $this->router->generate('dispatch_confirm', array(
            'token' => $user->getConfirmationToken()),
            UrlGeneratorInterface::ABSOLUTE_URL);
        $this->sender->goSendMessage(
            $template,
            $context=[
                'exp' =>'affichange',
                'dest'=>$user->getEmail(),
                'confirmationUrl' => $url,
                'user' => $user,
                'website'=>$website,
                'msg'=>""],
            'Votre espace est ouvert sur AffiChanGe',
            'registration');
    }

    public function oldsendConfirmationContactEmailMessage(User $user,Website $website)
    {
        $template ='aff_notifications/contact/byconvers.email.twig';
        $url = $this->router->generate('dispatch_confirm', array(
            'token' => $user->getConfirmationToken()),
            UrlGeneratorInterface::ABSOLUTE_URL);
        $this->sender->goSendMessage(
            $template,
            $context=[
                'exp' =>'affichange',
                'dest'=>$user->getEmail(),
                'confirmationUrl' => $url,
                'user' => $user,
                'website'=>$website,
                'msg'=>""],
            'Votre espace est ouvert sur AffiChanGe',
            'registration');
    }


    public function oldsendOnvitationDispatchEmailMessage(User $user,Website $website)
    {
        $template ='aff_notifications/contact/invitationweb.email.twig';
        $url = $this->router->generate('tab_spaceweb', ['slug'=>$website->getSlug()]);
        $this->sender->goSendMessage(
            $template,
            $context=[
                'exp' =>'affichange',
                'dest'=>$user->getEmail(),
                'url'=>$url,
                'user' => $user,
                'website'=>$website,
                'msg'=>""],
            'Votre invitation a rejoindre :'.$website->getNamewebsite().'',
            'website');
    }

    /**
     * {@inheritdoc}
     */
    public function oldsendResettingEmailMessage(User $user) //todo a finir
    {
        $template ='aff_notifications/security/resetmail.email.twig';
        $url = $this->router->generate('resetting_reset', array(
            'token' => $user->getConfirmationToken()),
            UrlGeneratorInterface::ABSOLUTE_URL);
        $this->sender->goSendMessage(
            $template,
            $context=['exp' =>'affichange','dest'=>$user->getEmail(),'resetting_reset' => $url,'user' => $user, 'msg'=>""],
            'reset compte affichange',
            'registration');
    }

    public function oldsendNewPasswordEmailMessage(User $user)
    {
        $template ='aff_notifications/security/reinitpassword.email.twig';
        $url = $this->router->generate('reset_change_password', array(
            'token' => $user->getConfirmationToken()),
            UrlGeneratorInterface::ABSOLUTE_URL);
        $this->sender->goSendMessage(
            $template,
            $context=['exp' =>'affichange','dest'=>$user->getEmail(),'confirmationUrl' => $url,'user' => $user, 'msg'=>""],
            'r??inialisation mot de passe',
            'registration');
    }

    public function oldsendfirstWord($email, $link)
    {
        $template ='aff_notifications/notifs/firstword.email.twig';
        return $this->sender->goSendMessage(
            $template,
            $context=['exp' =>'affichange','dest'=>$email,'id' => $email, 'link'=>$link, 'content' => 'merci pour tout', 'msg'=>""],
            'Premier contact, AffiChange',
            'prospect');
    }
}