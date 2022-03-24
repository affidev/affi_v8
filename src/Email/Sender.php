<?php


namespace App\Email;

use Swift_Mailer;
use Twig\Environment;

class Sender
{
    private $subjet;
    private $twingvue;
    private $context;
    private $mailer;
    private $twig;
    private $exp;
    private $sent;

    public function __construct(Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param $twingvue
     * @param $context
     * @param $subjet
     * @param $type
     * @return string
     */
    public function goSendMessage($twingvue, $context, $subjet, $type){ // todo pour faire une verif des contenus et validation
        $this->context=$context;
        $this->twingvue=$twingvue;
        $this->subjet=$subjet;
        $expe=$this->context['exp'];
        $dest=$this->context['dest'];

        switch ($type){
            case 'exp': // envoi du message vers le website
                $this->exp=['noreply@affichange.com'=> $dest->getNamewebsite()];
                $this->sent=$dest->getTemplate()->getEmailspaceweb();
                $this->sendlocal(); //$this->sendMessage();
                break;

            case 'notifmember': // envoi du message vers le website
                $this->exp=['noreply@affichange.com'=> $expe->getNamewebsite()];
                $this->sent=$dest;
                $this->sendlocal(); //$this->sendMessage();
                break;

            case 'expmember': // envoi message d'un spaceweb vers le website // todoa revoir
                $this->exp=['noreply@affichange.com'=>$dest->getNamewebsite()];
                $this->sent=$dest->getTemplate()->getEmailspaceweb();
                $this->sendlocal(); //$this->sendMessage();
                break;

            case 'op_market': // conversprivate - market - option offre -> retourne une confirmation a l'expedieur(email) et au destinataire (email)
                $this->exp=['noreply@affichange.com'=>"affichange"];
                $this->sent=$expe;
                $this->sendlocal(); //$this->sendMessage();
                break;

            case 'notif_affi': // retourne une confirmation au cleint pour le suivi de son achat
                $this->exp=['noreply@affichange.com'=>"affichange"];
                $this->sent=$dest;
                $this->sendlocal(); //$this->sendMessage();
                break;

            case 'website':
            case 'publication':
                $this->exp=['noreply@affichange.com'=>"affichange"];
                $this->sent=$dest;
                $this->sendlocal(); //$this->sendMessage();
                break;

            // les autres pour resa  revoir

            case 'destmember': // retourne une confirmation a l'expedieur
                $this->exp=['noreply@affichange.com'=>"affichange"];
                $this->sent=$expe->getTemplate()->getEmailspaceweb();
                $this->sendlocal(); //$this->sendMessage();
                break;

            case 'dest': // retourne une confirmation a l'expedieur (profil)
                $this->exp=['noreply@affichange.com'=>"affichange"];
                $this->sent=$expe->getEmailfirst();
                $this->sendlocal(); //$this->sendMessage();
                break;

            case 'notif':
                $this->exp=['noreply@affichange.com'=>"affichange"];
                $this->sent=$this->context['spaceweb']['template']['emailspaceweb'];
                $this->sendlocal(); //$this->sendMessage();
                break;

            case 'registration':
                $this->exp=['noreply@affichange.com'=>"affichange"];
                $this->sent=$dest;
                $this->sendlocal(); //$this->sendMessage();
                return 'ok';
                break;

            case 'prospect':
                $this->exp=['noreply@affichange.com'=>"affichange"];
                $this->sent=$dest;
                $this->sendlocal(); //$this->sendMessage();
                return true;
                break;

            default:
                return 'probleme';
            }
        return true;
    }


    public function sendMessage()
    {

        // DKIM version sur server

        $privatekey=file_get_contents(__DIR__ . '/dkim.private.key');
        $signer=new \Swift_Signers_DKIMSigner($privatekey,'affichange.com','default');

        // Message
        $message = (new \Swift_Message($this->subjet)); //todo une variante si user ou nouveau contact
        $message->attachSigner($signer)
            ->setFrom($this->exp)
            ->setTo($this->sent)
            ->setCharset('utf-8')
            ->setContentType('text/html')
            ->setBody($this->twig->render($this->twingvue,$this->context));

        // Envoi
        $this->mailer->send($message);
    }


    public function sendtest($url)
    {
        $privatekey=file_get_contents(__DIR__ . '/dkim.private.key');
        $signer=new \Swift_Signers_DKIMSigner($privatekey,'affichange.com','default');
        // Message
        $message = (new \Swift_Message("test message affi"));
        $message->attachSigner($signer)
            ->setFrom('contact@affichange.com')
            ->setTo($this->sent)
            ->setCharset('utf-8')
            ->setContentType('text/html')
            ->setBody('ceci est un simple test');
        $this->mailer->send($message);
    }

    public function sendlocal()
    {
        $message = (new \Swift_Message($this->subjet))
            ->setFrom($this->exp)
            ->setTo($this->sent)
            ->setCharset('utf-8')
            ->setContentType('text/html')
            ->setBody($this->twig->render($this->twingvue,$this->context));
        $this->mailer->send($message);

    }
}