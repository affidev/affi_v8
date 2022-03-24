<?php


namespace App\EventSubscriber;

use App\AffiEvents;
use App\Entity\LogMessages\Tbmsgs;
use App\Entity\Notifications\Notifdispatch;
use App\Event\MessageWebsiteEvent;
use App\Service\Modules\Mailator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class NotificationMsgWebsiteSubscriber implements EventSubscriberInterface
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

            AffiEvents::NOTIFICATION_ADD_MSG_WEBSITE_DISPATCH =>[
                ['addwebsitecommentdispatch', 10],
            ],
            AffiEvents::NOTIFICATION_ADD_MSG_WEBSITE_CONTACT =>[
                ['addwebsitecommentcontact', 10],
            ],
            AffiEvents::NOTIFICATION_ANSWER_MSG_WEBSITE_DISPATCH =>[
                ['answerwebsitecommentdispatch', 10],
            ],
            AffiEvents::NOTIFICATION_ANSWER_MSG_WEBSITE_CONTACT =>[
                ['answerwebsitecommentcontact', 10],
            ],

        ];
    }

    /**
     * @param MessageWebsiteEvent $event
     */
    public function addwebsitecommentdispatch(MessageWebsiteEvent $event)
    {
        $exp=$event->getDispatch();
        $tonotif=$this->sortdest($event->getBoard(),$exp);
        $this->addnotif($tonotif,$event->getMessage(),$event->getBoard(),1);
    }

    public function addwebsitecommentcontact(MessageWebsiteEvent $event)
    {
        $tonotif=$this->sortdest($event->getBoard(),null);
        $this->addnotif($tonotif,$event->getMessage(),$event->getBoard(),2);
        $this->mailer->confirAddCommentWebsiteToContact($event->getBoard(), $event->getContact(), $event->getMessage());
    }

    public function answerwebsitecommentdispatch(MessageWebsiteEvent $event)
    {
        $exp=$event->getDispatch();
        $author=$event->getAuthor(); //dispatch
        $tonotif=$this->sortdestanswerdispatch($event->getBoard(),$exp,$author);
        $this->addnotif($tonotif,$event->getMessage(),$event->getBoard(),3);
    }

    public function answerwebsitecommentcontact(MessageWebsiteEvent $event)
    {
        $exp=$event->getDispatch();
        $author=$event->getAuthor(); //contact
        $tonotif=$this->sortdestanswercontact($event->getBoard(),null,$author);
        $this->addnotif($tonotif,$event->getMessage(),$event->getBoard(),4);
        $this->mailer->confirAddCommentWebsiteToContact( $event->getBoard(),$event->getContact(), $event->getMessage());
    }

    private function addnotif($tonotif, $msg,$board,$typ){
        foreach ($tonotif as $ent){
            $tabread=new Tbmsgs();
            $tabnotif=new Notifdispatch();
            $tabread->setIdmessage($msg);
            $tabread->setIdispatch($ent->getId());
            $tabread->setTabnotifs($tabnotif);
            $tabread->setIsRead(false);
            $tabnotif->setDispatch($ent);
            $tabnotif->setClassmodule("board");
            $tabnotif->setIdmodule($board->getId());
            switch ($typ){
                case 1:
                    $tabnotif->setSubject("nouveau message sur :".$board->getNamewebsite());
                    break;
                case 2:
                    $tabnotif->setSubject("nouveau contact :".$board->getNamewebsite());
                    break;
                case 3:
                    $tabnotif->setSubject("reponse sur :".$board->getNamewebsite());
                    break;
                case 4:
                    $tabnotif->setSubject("reponse contact :".$board->getNamewebsite()); //todo pas sur necesaire de faire une notif
                    break;
            }

            $this->em->persist($tabread);
            $this->em->persist($tabnotif);
        }
        $this->em->flush();
    }


    private function sortdest($board,$exp): array
    {
        $tonotif=[];
        $pws=$board->getSpwsites();
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

    private function sortdestanswerdispatch($board,$exp,$author): array
    {
        $tonotif=[];
        $tbVerifAuthor=[];
        $pws=$board->getSpwsites();
        foreach ($pws as $pw){
            if($pw->isSuper() || $pw->getRole()=="admin" && $pw->getDisptachwebsite()->getId()!==$exp->getId()){
                $tonotif[]=$pw->getDisptachwebsite();
                $tbVerifAuthor[]=$pw->getDisptachwebsite()->getId();
            }
        }
        if(!in_array($author->getId(),$tbVerifAuthor))$tonotif[]=$author;
        return $tonotif;
    }

    private function sortdestanswercontact($board,$exp, $author): array
    {
        $tonotif=[];
        $pws=$board->getSpwsites();
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
}