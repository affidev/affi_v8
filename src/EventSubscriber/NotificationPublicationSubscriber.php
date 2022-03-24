<?php


namespace App\EventSubscriber;

use App\AffiEvents;
use App\Entity\LogMessages\TbmsgP;
use App\Entity\Notifications\Notifdispatch;
use App\Event\MessagePublicationEvent;
use App\Service\Modules\Mailator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class NotificationPublicationSubscriber implements EventSubscriberInterface
{

    private Mailator $mailer;
    private EntityManagerInterface $em;



    public function __construct(Mailator $mailer, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->em=$em;
    }
    public static function getSubscribedEvents()
    {
        return [

            AffiEvents::NOTIFICATION_ADD_COMMENT_POST_DISPATCH =>[
                ['addpostcommentdispatch', 10],
            ],
            AffiEvents::NOTIFICATION_ADD_COMMENT_POST_CONTACT =>[
                ['addpostcommentcontact', 10],
            ],
            AffiEvents::NOTIFICATION_ADD_COMMENT_OFFRE_DISPATCH =>[
                ['addoffrecommentdispatch', 10],
            ],
            AffiEvents::NOTIFICATION_ADD_COMMENT_OFFRE_CONTACT =>[
                ['addoffrecommentcontact', 10],
            ],
            AffiEvents::NOTIFICATION_ANSWER_COMMENT_POST_DISPATCH =>[
                ['answerpostcommentdispatch', 10],
            ],
            AffiEvents::NOTIFICATION_ANSWER_COMMENT_POST_CONTACT =>[
                ['answerpostcommentcontact', 10],
            ],
            AffiEvents::NOTIFICATION_ANSWER_COMMENT_OFFRE_DISPATCH =>[
                ['answeroffrecommentdispatch', 10],
            ],
            AffiEvents::NOTIFICATION_ANSWER_COMMENT_OFFRE_CONTACT =>[
                ['answeroffrecommentcontact', 10],
            ],

        ];
    }

    //------------------------------post
    /**
     * @param MessagePublicationEvent $event
     */
    public function addpostcommentdispatch(MessagePublicationEvent $event)
    {
        $exp=$event->getDispatch();
        $tonotif=$this->sortdestPost($event->getBoard(),$exp,$event->getPublication());
        $this->addnotif($event->getPublication(),$tonotif,$event->getMessage(),"blog");
    }

    public function addpostcommentcontact(MessagePublicationEvent $event)
    {
        $tonotif=$this->sortdestPost($event->getBoard(),null,$event->getPublication());
        $this->addnotif($event->getPublication(),$tonotif,$event->getMessage(),"blog");
        $this->mailer->confirAddCommentPublicationToContact($event->getPublication(), $event->getContact(), $event->getMessage());
    }
    // answer post
    public function answerpostcommentdispatch(MessagePublicationEvent $event)
    {
        $exp=$event->getDispatch();
        $author=$event->getAuthor(); //dispatch
        $tonotif=$this->sortdestanswerdispatch($event->getBoard(),$exp,$event->getPublication(),$author);
        $this->addnotif($event->getPublication(),$tonotif,$event->getMessage(),"blog");
    }

    public function answerpostcommentcontact(MessagePublicationEvent $event)
    {
        $exp=$event->getDispatch();
        $author=$event->getAuthor(); //contact
        $tonotif=$this->sortdestanswercontact($event->getBoard(),null,$event->getPublication(),$author);
        $this->addnotif($event->getPublication(),$tonotif,$event->getMessage(),"blog");
        $this->mailer->confirAddCommentPublicationToContact($event->getPublication(), $event->getContact(), $event->getMessage());
    }




    private function addnotif($publication,$tonotif, $msgpublication, $typepublication){
        foreach ($tonotif as $ent){
            $tabread=new TbmsgP();
            $tabnotif=new Notifdispatch();
            $tabread->setIdmessage($msgpublication);
            $tabread->setIdispatch($ent->getId());
            $tabread->setTabnotifs($tabnotif);
            $tabread->setIsRead(false);
            $tabnotif->setDispatch($ent);
            $tabnotif->setClassmodule($typepublication);
            $tabnotif->setIdmodule($publication->getId());
            $tabnotif->setSubject("nouveau commentaire sur :".$publication->getTitre());
            $this->em->persist($tabread);
            $this->em->persist($tabnotif);
        }
        $this->em->flush();
    }


    // Post add:
    private function sortdestPost($board,$exp,$publication): array
    {
        $tonotif=[];
        if($publication->isAllmember()){
            $pws=$board->getSpwsites();
        }else{
            $pws=$publication->getMembers();
        }
        if($exp){   //si exp est un dispatch
            foreach ($pws as $pw){
                if($pw->isSuper() || $pw->getRole()=="admin" && $pw->getDisptachwebsite()->getId()!==$exp->getId()) $tonotif[]=$pw->getDisptachwebsite();
            }
        }else {
            foreach ($pws as $pw) {
                if ($pw->isSuper() || $pw->getRole()=="admin") $tonotif[] = $pw->getDisptachwebsite();
            }
        }
        return $tonotif;
    }

    private function sortdestanswerdispatch($board,$exp,$publication,$author): array
    {
        $tonotif=[];
        $tbVerifAuthor=[];
        if($publication->isAllmember()){
            $pws=$board->getSpwsites();
        }else{
            $pws=$publication->getMembers();
        }
        foreach ($pws as $pw){
            if($pw->isSuper() || $pw->getRole()=="admin" && $pw->getDisptachwebsite()->getId()!==$exp->getId()){
                $tonotif[]=$pw->getDisptachwebsite();
                $tbVerifAuthor[]=$pw->getDisptachwebsite()->getId();
            }
        }
        if(!in_array($author->getId(),$tbVerifAuthor))$tonotif[]=$author;
        return $tonotif;
    }

    private function sortdestanswercontact($board,$exp,$publication,$author): array //todo verif exp
    {
        $tonotif=[];
        if($publication->isAllmember()){
            $pws=$board->getSpwsites();
        }else{
            $pws=$publication->getMembers();
        }
        if($exp){
            foreach ($pws as $pw){
                if($pw->isSuper() || $pw->getRole()=="admin" && $pw->getDisptachwebsite()->getId()!==$exp->getId()) $tonotif[]=$pw->getDisptachwebsite();
            }
        }else {
            foreach ($pws as $pw) {
                if ($pw->isSuper() || $pw->getRole()=="admin") $tonotif[] = $pw->getDisptachwebsite();
            }
        }
        return $tonotif;
    }


    // Offre :


    // -----------------------------------offre
    public function addoffrecommentdispatch(MessagePublicationEvent $event)
    {
        $exp=$event->getDispatch();// todo faire cette opération mais pour les autres membres du board
        $tonotif=$this->sortdestOffre($event->getBoard(),$exp);
        $this->addnotif($event->getPublication(),$tonotif,$event->getMessage(),"shop");
    }

    public function addoffrecommentcontact(MessagePublicationEvent $event)
    {
        $tonotif=$this->sortdestOffre($event->getBoard(),null);
        $this->addnotif($event->getPublication(),$tonotif,$event->getMessage(),"shop");
        $this->mailer->confirAddCommentPublicationToContact($event->getPublication(), $event->getContact(), $event->getMessage());
    }

    // answer offre
    public function answeroffrecommentdispatch(MessagePublicationEvent $event)
    {
        $exp=$event->getDispatch();// todo faire cette opération mais pour les autres membres du board
        $tonotif=$this->sortdestOffre($event->getBoard(),$exp);
        $this->addnotif($event->getPublication(),$tonotif,$event->getMessage(),"shop");
    }

    public function answeroffrecommentcontact(MessagePublicationEvent $event)
    {
        $tonotif=$this->sortdestOffre($event->getBoard(),null);
        $this->addnotif($event->getPublication(),$tonotif,$event->getMessage(),"shop");
        $this->mailer->confirAddCommentPublicationToContact($event->getPublication(), $event->getContact(), $event->getMessage());
    }


    private function sortdestOffre($board,$exp): array
    {
        $tonotif=[];
        $pws=$board->getSpwsites();
        if($exp){
            foreach ($pws as $pw){
                if($pw->isAdmin() && $pw->getDisptachwebsite()->getId()!==$exp->getId()) $tonotif[]=$pw->getDisptachwebsite();
            }
        }else {
            foreach ($pws as $pw) {
                if ($pw->isAdmin()) $tonotif[] = $pw->getDisptachwebsite();
            }
        }
        return $tonotif;
    }
}