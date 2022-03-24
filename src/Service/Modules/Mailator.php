<?php


namespace App\Service\Modules;


use App\Email\Sender;
use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\LogMessages\PrivateConvers;
use App\Entity\Marketplace\Offres;
use App\Entity\Websites\Website;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class Mailator
{
    private Sender $sender;
    private UrlGeneratorInterface $router;


    public function __construct( UrlGeneratorInterface $router,  Sender $sender)
    {
        $this->router = $router;
        $this->sender = $sender;
    }

    /**
     * @param $wb Website
     * @param $expe
     * @param $member
     * @param $msg
     */
    public function newMail($wb, $expe, $member, $msg){

        // generation du lien dans le mail pour ouvrir la page de messagerie et visualiser le message

        $url = $this->router->generate('read_msg', [
            'slug'=>$wb->getSlug(),
            'id' => $msg->getId()],
            //'name' => urlencode($parametre['form']->get('name')->getData(true))],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        // preparation du mail (paramètres) et envoie via sender->gosendmessage

        if($member){
            $this->sender->goSendMessage(
            'aff_notification/contact/notifmsgcontact.email.twig',
            $context=['exp' =>$expe,'dest'=>$wb,'linkmsg' => $url, 'msg'=>$msg->getSubject()],
            'vous avez reçu un nouveau message de la part de : '.$expe->getName(),
            'expmember');
            return ;
        }else{
            $this->sender->goSendMessage(
            'aff_notification/contact/notifmsgcontact.email.twig',
            $context=['exp' =>$expe,'dest'=>$wb,'linkmsg' => $url, 'msg'=>$msg->getSubject()],
            'vous avez reçu un nouveau message de la part de : '.$expe->getFirstname(),
            'exp');
            return ;
        }
    }

    /**
     * @param $website Website
     * @param $expediteur array
     * @param $message
     */
    public function notifMessageMemberwebsite($website, $expediteur, $message){
        $url = $this->router->generate('read_msg', [
            'slug'=>$website->getSlug(),
            'id' => $message->getId()],
            //'name' => urlencode($parametre['form']->get('name')->getData(true))],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $this->sender->goSendMessage(
            'aff_notification/notifs/newMsgwebsiteMembers.email.twig',
            $context=['exp' =>$website,'dest'=>$expediteur,'linkmsg' => $url, 'msg'=>$message->getSubject()],
            'Un nouveau message sur : '.$website->getNamewebsite(),
            'notifmember');
        return ;
    }

    /**
     * @param $webitemsg
     */
    public function reponseMailContact($webitemsg, $msg){
        $url = $this->router->generate('show_website', [
            'city'=>$webitemsg->getWebsitedest()->getLocality()->getSlugcity(),
            'nameboard' => $webitemsg->getWebsitedest()->getSlug()],
            //'name' => urlencode($parametre['form']->get('name')->getData(true))],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        //goSendMessage($twingvue, $context, $subjet, $type)
        $this->sender->goSendMessage(
            'aff_notification/contact/reponse.email.twig',
            $context=['dest' =>$webitemsg->getWebsitedest(),'exp'=>$webitemsg->getContactexp()->getUseridentity(),'linkmsg' => $url, 'msg'=>$msg],
            'vous avez reçu un nouveau message de la part de : '.$webitemsg->getWebsitedest()->getNamewebsite(),
            'dest');
        return ;

    }

    /**
     * @param $website Website
     * @param $message
     * @param $client DispatchSpaceWeb
     */
    public function reponseMailToClient($website, $message, $client){
        $url = $this->router->generate('read_msg', [
            'slug'=>$website->getSlug(),
            'id' => $message->getId()],
            //'name' => urlencode($parametre['form']->get('name')->getData(true))],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        //goSendMessage($twingvue, $context, $subjet, $type)
        $this->sender->goSendMessage(
            'aff_notification/notifs/newMsgwebsiteMembers.email.twig',
            $context=['dest' =>$client->getCustomer()->getProfil()->getEmailfirst(),'exp'=>$website,'linkmsg' => $url, 'msg'=>""],
            'vous avez reçu un nouveau message de la part de : '.$website->getNamewebsite(),
            'notifmember');
        return ;
    }

    /**
     * @param $offre Offres
     * @param $client DispatchSpaceWeb
     * @param $convers PrivateConvers
     */
    public function notifiNewConversDestAndExpe($offre, $client, $convers){

        $url = $this->router->generate('read_private_convers_dp', [
            'id' => $convers->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $dest=$offre->getIsotherdest()?$offre->getDestinataire():$offre->getAuthor()->getCustomer()->getEmailcontact();
        $this->sender->goSendMessage(
            'aff_notification/market/notifmsgseller.email.twig',
            ['exp' =>$client->getCustomer()->getEmailcontact(),'dest'=>$dest,'linkmsg' => $url,'offre'=>$offre, 'msg'=>$convers->getSubject()],
            'vous avez reçu un nouveau message concerant votre offre : '.$offre->getTitre(),
            'op_market');

        $this->sender->goSendMessage(
            'aff_notification/market/notifmsgclient.email.twig',
            ['exp' =>'','dest'=>$client->getCustomer()->getEmailcontact(),'linkmsg' => $url, 'offre'=>$offre,'msg'=>$convers->getSubject()],
            'Suivi de votre conversation sur : '.$offre->getTitre(),
            'notif_affi');
        return ;
    }

    /**
     * @param $expe DispatchSpaceWeb
     * @param $dest DispatchSpaceWeb
     * @param $convers PrivateConvers
     */
    public function notifiNewPrivateConversDestAndExpe($expe, $dest, $convers){

        $url = $this->router->generate('read_private_convers_dp', [
            'id' => $convers->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $dest=$dest->getCustomer()->getEmailcontact();
        $this->sender->goSendMessage(
            'aff_notification/market/notifmsgseller.email.twig',
            ['exp' =>$expe->getCustomer()->getEmailcontact(),'dest'=>$dest,'linkmsg' => $url,'msg'=>$convers->getSubject()],
            'vous avez reçu un nouveau message privé ',
            'private');

    }

    /**
     * @param $publication
     * @param $contact
     * @param $message
     */
    public function confirAddCommentPublicationToContact($publication, $contact, $message){

        $url = $this->router->generate('new_identify_stape_contact', [
            'id' => $contact->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $this->sender->goSendMessage(
            'aff_notification/publications/confirm-first-comment-contact.email.twig',
            ['exp' =>$publication,'dest'=>$contact->getEmailCanonical(),'url' => $url,'msg'=>$message],
            "Votre message sur AffiChange",
            'publication');
    }

    public function confirAddCommentWebsiteToContact($board,$contact, $message){

        $url = $this->router->generate('new_identify_stape_contact', [
            'id' => $contact->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $this->sender->goSendMessage(
            'aff_notification/board/anserw-comment-contact.email.twig',
            ['exp' =>$board->getNamewebsite(),'dest'=>$contact->getEmailCanonical(),'url' => $url,'msg'=>$message],
            "Votre message sur AffiChange",
            'website');
    }


    public function informCreatePartnerToContact( $event){

        $url = $this->router->generate('suggest_boardpartner', [
            'id' => $event->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $this->sender->goSendMessage(
            'aff_notification/partner/info-create-partner.email.twig',
            ['exp' =>"",'dest'=>$event->getContact()->getEmailCanonical(),'url' => $url,'invitor'=>$event->getBoard(), 'partner'=>$event->getPartner(), 'tabsuggets'=>$event],
            "".$event->getBoard()->getNamewebsite()."souhaite ajouter le panneau ".$event->getPartner()->getNamewebsite()." comme Partenaire",
            'partner');
    }
}