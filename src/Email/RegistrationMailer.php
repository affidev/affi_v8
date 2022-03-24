<?php


namespace App\Email;

use App\Entity\Customer\Customers;
use App\Entity\User;
use App\Entity\Websites\Website;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationMailer
{

    protected Sender $sender;
    protected UrlGeneratorInterface $router;
    protected array $parameters;


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

    public function sendAskConfirmationEmailMessage(User $user)
    {
        $template ='aff_notification/security/confirmation.email.twig';
        $url = $this->router->generate('registration_confirm', array(
            'token' => $user->getConfirmationToken()),
            UrlGeneratorInterface::ABSOLUTE_URL);
        $this->sender->goSendMessage(
            $template,
            $context=['exp' =>'affichange','dest'=>$user->getEmail(),'confirmationUrl' => $url,'user' => $user, 'msg'=>""],
            'confirmez votre inscription sur affiChange',
            'registration');
    }

    public function sendAskConfirmationDispatchEmailMessage(User $user)
    {
        $template ='aff_notification/contact/invitationweb.email.twig';
        $url = $this->router->generate('dispatch_confirm', array(
            'token' => $user->getConfirmationToken()),
            UrlGeneratorInterface::ABSOLUTE_URL);
        $this->sender->goSendMessage(
            $template,
            $context=['exp' =>'affichange','dest'=>$user->getEmail(),'confirmationUrl' => $url,'user' => $user, 'msg'=>""],
            'Votre invitation a rejoindre affiChange',
            'registration');
    }

    public function sendConfirmationEmailMessage(User $user)
    {
        $template ='aff_notification/security/confirmation.email.twig';
        $url = $this->router->generate('registration_confirm', array(
            'token' => $user->getConfirmationToken()),
            UrlGeneratorInterface::ABSOLUTE_URL);
        $this->sender->goSendMessage(
            $template,
            $context=['exp' =>'affichange','dest'=>$user->getEmail(),'confirmationUrl' => $url,'user' => $user, 'msg'=>""],
            'confirmez votre inscription sur affiChange',
            'registration');
    }

    public function sendConfirmationDispatchEmailMessage(User $user,Website $website, ProfilUser $profil)
    {
        $template ='aff_notification/member/invitationweb.email.twig';
        $url = $this->router->generate('dispatch_confirm', array(
            'token' => $user->getConfirmationToken()),
            UrlGeneratorInterface::ABSOLUTE_URL);
        $this->sender->goSendMessage(
            $template,
            $context=[
                'exp' =>'affichange',
                'dest'=>$user->getEmail(),
                'confirmationUrl' => $url,
                'mdp'=>$profil->getMdpfirst(),
                'user' => $user,
                'website'=>$website,
                'msg'=>""],
            'Votre invitation a rejoindre AffiChanGe',
            'registration');
    }

    public function sendConfirmationClientEmailMessage(User $user,Website $website)
    {
        $template ='aff_notification/client/byshop.email.twig';
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

    public function sendConfirmationContactEmailMessage(User $user,Website $website)
    {
        $template ='aff_notification/contact/byconvers.email.twig';
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


    public function sendOnvitationDispatchEmailMessage(Customers $customer,Website $website)
    {
        $template ='aff_notification/board/invit-tobecome_admin.email.twig';    // ancien :contact/invitationweb.email.twig';
        $url = $this->router->generate('app_login',[],UrlGeneratorInterface::ABSOLUTE_URL);
        $this->sender->goSendMessage(
            $template,
            $context=[
                'exp' =>'affichange',
                'dest'=>$customer->getEmailcontact(),
                'url'=>$url,
                'mail'=>$customer->getEmailcontact(),
                'user' => $customer,
                'website'=>$website,
                'msg'=>""],
            'Votre invitation a rejoindre :'.$website->getNamewebsite().'',
            'website');
    }

    public function sendOnvitationMailToBeAdmin($mail,Website $website)
    {
        $template ='aff_notification/board/invit-tobecome_admin.email.twig';
        $url = $this->router->generate('new_identify',[],UrlGeneratorInterface::ABSOLUTE_URL);
        $this->sender->goSendMessage(
            $template,
            $context=[
                'exp' =>'affichange',
                'dest'=>$mail,
                'url'=>$url,
                'user' => "",
                'mail'=>$mail,
                'website'=>$website,
                'msg'=>""],
            'Votre invitation a rejoindre :'.$website->getNamewebsite().'',
            'website');
    }


    public function sendResettingEmailMessage(User $user) //todo a finir
    {
        $template ='aff_notification/security/resetmail.email.twig';
        $url = $this->router->generate('resetting_reset', array(
            'token' => $user->getConfirmationToken()),
            UrlGeneratorInterface::ABSOLUTE_URL);
        $this->sender->goSendMessage(
            $template,
            $context=['exp' =>'affichange','dest'=>$user->getEmail(),'resetting_reset' => $url,'user' => $user, 'msg'=>""],
            'reset compte affichange',
            'registration');
    }

    public function sendNewPasswordEmailMessage(User $user)
    {
        $template ='aff_notification/security/reinitpassword.email.twig';
        $url = $this->router->generate('reset_change_password', array(
            'token' => $user->getConfirmationToken()),
            UrlGeneratorInterface::ABSOLUTE_URL);
        $this->sender->goSendMessage(
            $template,
            $context=['exp' =>'affichange','dest'=>$user->getEmail(),'confirmationUrl' => $url,'user' => $user, 'msg'=>""],
            'réinialisation mot de passe',
            'registration');
    }

    public function sendfirstWord($email, $link): bool|string
    {
        $template ='aff_notification/notifs/firstword.email.twig';
        return $this->sender->goSendMessage(
            $template,
            $context=['exp' =>'affichange','dest'=>$email,'id' => $email, 'link'=>$link, 'content' => 'merci pour tout', 'msg'=>""],
            'Premier contact, AffiChange',
            'prospect');
    }
}