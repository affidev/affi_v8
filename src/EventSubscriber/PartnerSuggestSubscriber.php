<?php


namespace App\EventSubscriber;

use App\Entity\LogMessages\Tbmsgs;
use App\Entity\Notifications\Notifdispatch;
use App\Event\SuggestPartnerEvent;
use App\Infrastructure\Search\Typesense\TypesenseIndexer;
use App\Service\Modules\Mailator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PartnerSuggestSubscriber implements EventSubscriberInterface
{

    private TypesenseIndexer $indexer;
    private NormalizerInterface $normalizer;
    private Mailator $mailer;
    private EntityManagerInterface $em;

    public function __construct(TypesenseIndexer $indexer, NormalizerInterface $normalizer,Mailator $mailer, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->em=$em;
        $this->indexer = $indexer;
        $this->normalizer = $normalizer;
    }

    public static function getSubscribedEvents()
    {
        return [
            SuggestPartnerEvent::DISPATCH => 'advertDispatchToCreatePartner', // peu probable qu'un dispatch ,n'est pas le board dejà crée ?? todo appronfondir la reflexion
            SuggestPartnerEvent::CONTACT => 'advertContactToCreatePartner',  // idem, peu probable qu'un contact ,n'est pas le board dejà crée ?? todo appronfondir la reflexion
            SuggestPartnerEvent::NEWCONTACT => 'advertNewContactToCreatePartner'
            ];
    }

    /**
     * @throws ExceptionInterface
     */
    public function indexNewWebsite($partner){
        $data=$this->normalizer->normalize($partner, 'search');
        $this->indexer->indexOne((array) $data);
    }

    /**
     * @param SuggestPartnerEvent $event
     */
    public function advertDispatchToCreatePartner(SuggestPartnerEvent $event) //todo
    {
      //  $exp=$event->getDispatch();
      //  $tonotif=$this->sortdest($event->getBoard(),$exp);
     //   $this->addnotif($tonotif,$event->getMessage(),$event->getBoard());
    }

    public function advertContactToCreatePartner(SuggestPartnerEvent $event) //todo
    {
       // $tonotif=$this->sortdest($event->getBoard(),null);
       // $this->addnotif($tonotif,$event->getMessage(),$event->getBoard());
       // $this->mailer->confirAddCommentWebsiteToContact( $event->getContact(), $event->getMessage());
    }

    public function advertNewContactToCreatePartner(SuggestPartnerEvent $event)
    {
       // $dest=$event->getContact();
       // $invitor=$event->getBoard();
       // $tonotif=$this->sortdestanswerdispatch($event->getBoard(),$exp,$author);
      //  $this->addnotif($tonotif,$event->getMessage(),$event->getBoard());
        $this->mailer->informCreatePartnerToContact( $event);
    }


    private function addnotif($tonotif, $msg,$board){
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
            $tabnotif->setSubject("nouveau commentaire sur :".$board->getNamewebsite());
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