<?php


namespace App\Service\SpaceWeb;


use App\AffiEvents;
use App\Entity\Admin\Wbcustomers;
use App\Entity\Customer\Customers;
use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\DispatchSpace\Spwsite;
use App\Entity\DispatchSpace\TabDotWb;
use App\Entity\Media\Background;
use App\Entity\Media\Pict;
use App\Entity\Sector\Sectors;
use App\Entity\UserMap\Taguery;
use App\Entity\Websites\Template;
use App\Entity\Websites\Website;
use App\Event\CustomerEvent;
use App\Event\InvitMailEvent;
use App\Lib\MsgAjax;
use App\Lib\Tools;
use App\Repository\Entity\GpsRepository;
use App\Repository\Entity\TabDotWbRepository;
use App\Repository\Entity\TagueryRepository;
use App\Service\Gestion\Numerator;
use App\Service\Localisation\LocalisationServices;
use App\Service\Media\Uploadator;
use App\Module\Modulator;
use App\Service\Registration\CreatorUser;
use App\Service\Registration\Sessioninit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


class SpacewebFactor
{
    private EntityManagerInterface $em;
    private Uploadator $uploadator;
    private Sessioninit $sessioninit;
    private TagueryRepository $tagueryRepository;
    private GpsRepository $gpsRepository;
    private Numerator $numerator;
    private CreatorUser $creator;
    private EventDispatcherInterface $eventDispatcher;
    private LocalisationServices $localise;
    private Modulator $modulator;
    private TabDotWbRepository $tabdotwbrepo;


    public function __construct(TagueryRepository $tagueryRepository, EntityManagerInterface $em,CreatorUser $creator,
                                Uploadator $uploadator, Sessioninit $sessioninit,Numerator $numerator,GpsRepository $gpsRepository,
                                EventDispatcherInterface $eventDispatcher, LocalisationServices $localise, Modulator $modulator,TabDotWbRepository $tabDotWbRepository){

        $this->em = $em;
        $this->uploadator = $uploadator;
        $this->sessioninit = $sessioninit;
        $this->tagueryRepository = $tagueryRepository;
        $this->numerator = $numerator;
        $this->creator = $creator;
        $this->eventDispatcher = $eventDispatcher;
        $this->localise = $localise;
        $this->gpsRepository = $gpsRepository;
        $this->modulator=$modulator;
        $this->tabdotwbrepo=$tabDotWbRepository;
    }


    /**
     * creation et ajout d'un membre a un website par une conversation
     * @param $tabmember
     * @return DispatchSpaceWeb
     * @throws \Exception
     */
    public function addwebsiteclient($tabmember): DispatchSpaceWeb
    {  //contact (ou null),type, website, mail, pass, name,
        $customer=$this->creator->createUserByConversToJoinWebsite($tabmember);
        return $this->newDispatchclient($customer,$tabmember);
    }

    /**
     * invitation-ajout membre à un website par le mail
     * @param $tabmember
     * @return DispatchSpaceWeb
     * @throws \Exception
     */
    public function addWebsiteNewMember($tabmember): DispatchSpaceWeb
    {  //contact (ou null),type, website, mail, pass, name,
        $customer=$this->creator->createUserByMailToInvitWebsite($tabmember);
        return  $this->newDispatchmember($customer,$tabmember);
    }

    /**
     * invitation-ajout d'un admin à un website par l'admin
     * @param $tabmember
     * @return DispatchSpaceWeb
     * @throws \Exception
     */
    public function addWebsiteNewadmin($tabmember): DispatchSpaceWeb
    {  //contact (ou null),type, website, mail, pass, name,
        $customer=$this->creator->createUserByMailToInvitWebsite($tabmember);
        return  $this->newDispatchAdmin($customer,$tabmember);
    }

    /**
     * invitation-ajout d'un admin à un website par l'admin
     * @param $mail
     * @param $website
     * @return TabDotWb
     */
    public function invitMailToAdmin($mail, $website): TabDotWb
    {
        $tabdot=new TabDotWb();
        $tabdot->setWebsite($website);
        $tabdot->setEmail($mail);
        $this->em->persist($tabdot);
        $this->em->flush();
        $event= new InvitMailEvent($mail, $website);
        $this->eventDispatcher->dispatch($event, AffiEvents::INVIT_TOADMIN_BYMAIL);
        return  $tabdot;
    }

    /**
     * @param $dispatch DispatchSpaceWeb
     * @param $website Website
     * @return DispatchSpaceWeb
     */
    public function addwebsitedispatch(DispatchSpaceWeb $dispatch, Website $website): DispatchSpaceWeb
    {  //$mail, $website, $password, $name

        $spwsite=New Spwsite();
        $spwsite->setIsadmin(true);
        $spwsite->setWebsite($website);
        $spwsite->setRole('superadmin');
        $website->addSpwsite($spwsite);
        $dispatch->addSpwsite($spwsite);
        $this->em->persist($website);
        $this->em->persist($dispatch);
        $this->em->flush();
        $event = new CustomerEvent($dispatch->getCustomer(),$website);
        $this->eventDispatcher->dispatch($event, AffiEvents::DISPATCH_INVIT_WEBSITE);
        return $dispatch;
    }


    /**
     * @param $objcustomer Customers
     * @param $coord
     * @return DispatchSpaceWeb
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function NewDispatch(Customers $objcustomer, $coord): DispatchSpaceWeb
    {
        $dispatch=new DispatchSpaceWeb();
        $dispatch->setPermission([0,0,0]); // creation autonome
        $dispatch->setCustomer($objcustomer);
        $objcustomer->setDispatchspace($dispatch);
        $dispatch->setName($objcustomer->getProfil()->getFirstname()??$objcustomer->getProfil()->getEmailfirst());
        $gps=$this->localise->defineCity($coord['lat'], $coord['lon']);
        if(!$gps){ //todo revoir ça pas sur necessaire et le bon placement de la creation du gps ??
            $gps = $this->gpsRepository->find(3); //par defaut bouaye
        }
        $dispatch->setLocality($gps);
        $this->em->persist($dispatch);
        $this->em->flush();
        $this->sessioninit->preInitSpaceWeb($dispatch);
        $this->sessioninit->chenageLoc($dispatch);
        return $dispatch;
    }

    /**
     * @param $dispatch
     * @param $coord
     * @return DispatchSpaceWeb
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function confirmDispatch($dispatch,$coord,): DispatchSpaceWeb
    {
        $gps=$this->localise->defineCity($coord['lat'], $coord['lon']);
        if(!$gps){
            $gps = $this->gpsRepository->find(3); //par defaut bouaye
        }
        $dispatch->setLocality($gps);
        $this->em->persist($dispatch);
        $this->em->flush();
        $this->sessioninit->preInitSpaceWeb($dispatch);
        $this->sessioninit->chenageLoc($dispatch);
        return $dispatch;
    }

    /**
     * @param $dispatch
     * @param $website
     * @return DispatchSpaceWeb
     */
    public function confirmLocByWebsite($dispatch,$website): DispatchSpaceWeb
    {
        $dispatch->setLocality($website->getLocality());
        $this->em->persist($dispatch);
        $this->em->flush();
        $this->sessioninit->preInitSpaceWeb($dispatch);
        $this->sessioninit->chenageLoc($dispatch);
        return $dispatch;
    }

    /**
     * @param $dispatch DispatchSpaceWeb
     * @param $form
     * @return DispatchSpaceWeb
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function majDistach(DispatchSpaceWeb $dispatch, $form): DispatchSpaceWeb
    {
        $dispatch->setName($form['namespaceweb']->getData());
        $idgps=$form['idlocate']->getData();
        if($idgps){
            $testgps=explode(" ",$idgps);
            $gps = $this->localise->changeLocate(null, $testgps[0], $testgps[1])??$this->gpsRepository->find(3);
        }else {
            $gps = $this->gpsRepository->find(3); //par defaut bouaye
        }
        $dispatch->setLocality($gps);
        $this->em->persist($dispatch);
        $this->em->flush();
        $this->sessioninit->chenageLoc($dispatch);
        return $dispatch;
    }

    /**
     * @param $objcustomer Customers
     * @param $tabmenber
     * @return DispatchSpaceWeb
     * creation d'un dispatch depuis une requete via formulaire de conversation du website (non user)
     */
    public function newDispatchclient(Customers $objcustomer, $tabmenber): DispatchSpaceWeb
    {
        $dispatch=new DispatchSpaceWeb();
        $spwsite=New Spwsite();
        $spwsite->setIsadmin(false);
        $spwsite->setWebsite($tabmenber['website']);
        $spwsite->setRole("client");
        $spwsite->setTermes([0]);
        $tabmenber['website']->addSpwsite($spwsite);
        $dispatch->addSpwsite($spwsite);
        $dispatch->setPermission([0]); //pour rejoint conversation website  --
        $dispatch->setCustomer($objcustomer);
        $objcustomer->setDispatchspace($dispatch);
        $dispatch->setName($tabmenber['name']??"auto".$objcustomer->getNumclient()->getNumero());
        $this->em->persist($tabmenber['website']);
        $this->em->persist($dispatch);
        $this->em->persist($spwsite);
        $this->em->flush();
        return $dispatch;
    }

    /**
     * @param $objcustomer Customers
     * @param $tabmenber
     * @return DispatchSpaceWeb
     * creation d'un dispatch depuis une requete via formulaire de conversation du website (non user)
     */
    public function newDispatchmember(Customers $objcustomer, $tabmenber): DispatchSpaceWeb
    {
        $dispatch=new DispatchSpaceWeb();
        $spwsite=New Spwsite();
        $spwsite->setIsadmin(true);
        $spwsite->setWebsite($tabmenber['website']);
        $spwsite->setRole("member");
        $spwsite->setTermes([0]);
        $tabmenber['website']->addSpwsite($spwsite);
        $dispatch->addSpwsite($spwsite);
        $dispatch->setPermission([0,0,1]); //invitation et creation d'un membre  -- //todo atte,top, vient en contradiction avec la vérif charte par localisation
        $dispatch->setCustomer($objcustomer);
        $objcustomer->setDispatchspace($dispatch);
        $dispatch->setName($tabmenber['email']);
        $this->em->persist($tabmenber['website']);
        $this->em->persist($dispatch);
        $this->em->flush();
        return $dispatch;
    }

    /**
     * @param $objcustomer Customers
     * @param $tabmenber
     * @return DispatchSpaceWeb
     */
    public function newDispatchAdmin(Customers $objcustomer, $tabmenber): DispatchSpaceWeb
    {
        $dispatch=new DispatchSpaceWeb();
        $spwsite=New Spwsite();
        $spwsite->setIsadmin(true);
        $spwsite->setWebsite($tabmenber['website']);
        $spwsite->setRole("superadmin");
        $spwsite->setTermes([0]);
        $tabmenber['website']->addSpwsite($spwsite);
        $dispatch->addSpwsite($spwsite);
        $dispatch->setPermission([0,0,1]);
        $dispatch->setCustomer($objcustomer);
        $objcustomer->setDispatchspace($dispatch);
        $dispatch->setName($tabmenber['mail']);
        $this->em->persist($tabmenber['website']);
        $this->em->persist($dispatch);
        $this->em->flush();
        return $dispatch;
    }

    /**
     * @param $website Website
     * @param $dispatch DispatchSpaceWeb
     * @return Spwsite
     */
    public function addSpwsite(Website $website, DispatchSpaceWeb $dispatch): Spwsite
    {
        $spwsite=New Spwsite();
        $spwsite->setIsadmin(false);
        $spwsite->setWebsite($website);
        $spwsite->setRole('member');
        $website->addSpwsite($spwsite);
        $dispatch->addSpwsite($spwsite);
        $this->em->persist($website);
        $this->em->persist($dispatch);
        $this->em->flush();
        return $spwsite;
    }

    /**
     * @param $website Website
     * @param $dispatch DispatchSpaceWeb
     * @return Spwsite
     */
    public function addSpwsiteClient(Website $website, DispatchSpaceWeb $dispatch): Spwsite
    {
        $spwsite=New Spwsite();
        $spwsite->setIsadmin(false);
        $spwsite->setWebsite($website);
        $spwsite->setRole('client');
        $website->addSpwsite($spwsite);
        $dispatch->addSpwsite($spwsite);
        $this->em->persist($website);
        $this->em->persist($dispatch);
        $this->em->flush();
        return $spwsite;
    }

    /**
     * @param Customers $customer
     * @param $dispatchSpace DispatchSpaceWeb
     * @return Website
     */
    public function createFirstWebsite(Customers $customer,DispatchSpaceWeb $dispatchSpace): Website
    {
     // todo verifier si le website n'existe pas déjà
        $nums=$this->numerator->getActiveNumeratewebsite();
        $tabdot=$this->tabdotwbrepo->findOneBy(['email'=>$customer->getEmailcontact()]);
        if($tabdot){
            $website=$tabdot->getWebsite();
            $website->setAttached(true);
            $this->em->remove($tabdot);
            $this->em->flush();
        }else{
            $website=new Website();
            $website->setNamewebsite($dispatchSpace->getName().'-'.$dispatchSpace->getLocality()->getSlugcity());
            $website->setLocality($dispatchSpace->getLocality());
            $template=new Template();
            $template->setEmailspaceweb($customer->getEmailcontact());
            $website->setTemplate($template);
            $sector=new Sectors();
            $template->setSector($sector);
            $cli=new Wbcustomers();
            $cli->setWebsite($website);
            $website->setWbcustomer($cli);
            $cli->setNumero($nums->getNumClient());
            $cli->setOrdre(date('Y'));
        }
        $spw=new Spwsite();
        $spw->setDisptachwebsite($dispatchSpace);
        $spw->setWebsite($website);
        $spw->setRole('superadmin');
        $dispatchSpace->addSpwsite($spw);
        $spw->activeAdmin();
        $this->modulator->initModules($customer->getServices(), $website);  // creation des modules de base avec le contactation
        $this->em->persist($spw);
        $this->em->persist($dispatchSpace);
        $this->em->persist($website);
        $this->em->flush();
        $this->sessioninit->preInitSpaceWeb($dispatchSpace);
        return $website;
    }


    /**
     * @param DispatchSpaceWeb $dispatch
     * @param $spw Spwsite
     * @param $form
     * @return Website
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function addWebsiteLocality(DispatchSpaceWeb $dispatch, Spwsite $spw, $form): Website
    {
        $nums=$this->numerator->getActiveNumeratewebsite();
        $website=new Website();
        $website->setNamewebsite($form['namewebsite']->getData());
       // $gps=$this->localise->defineCity($form['lat']->getData(), $form['lon']->getData());
       // if(!$gps) throw new Exception('erruer sur la recherche du gps');
        $website->setLocality($this->gpsRepository->find($form['idcity']->getData())??null);
        $spw->setDisptachwebsite($dispatch);
        $spw->setWebsite($website);
        $spw->setRole('superadmin');
        $dispatch->addSpwsite($spw);
        $template=new Template();
        $template->setEmailspaceweb($dispatch->getCustomer()->getEmailcontact());
        $sector=new Sectors();
        $this->modulator->initModules($dispatch->getCustomer()->getServices(), $website);  // creation des modules de base avec le contactation
        $cli=new Wbcustomers();
        $cli->setWebsite($website);
        $website->setWbcustomer($cli);
        $cli->setNumero($nums->getNumClient());
        $cli->setOrdre(date('Y'));
        $website->setTemplate($template);
        $template->setSector($sector);
        $this->em->persist($spw);
        $this->em->persist($dispatch);
        $this->em->flush();
        return $website;
    }

    /**
     * @param DispatchSpaceWeb $dispatch
     * @param $form
     * @return Website
     */
    public function addWebsiteLocalityAdmin(DispatchSpaceWeb $dispatch, $form): Website
    {
        $website=new Website();
        $website->setAttached(false);
        $website->setNamewebsite($form['namewebsite']->getData());
        $website->setLocality($this->gpsRepository->find($form['idcity']->getData())??null);
        $template=new Template();
        $sector=new Sectors();
        $website->setTemplate($template);
        $template->setSector($sector);
        return $website;
    }

    /**
     * @param Website $website
     * @param $partner
     * @return array
     */
    public function addPartner(Website $website, $partner): array
    {
       $website->addWebsitepartner($partner);
       $this->em->persist($website);
       $this->em->flush();
       return MsgAjax::MSG_SUCCESS;
    }

    /**
     * @param $dispatchSpace DispatchSpaceWeb
     * @param $spw Spwsite
     * @param $website Website
     * @return Website
     */
    public function addWebsite(DispatchSpaceWeb $dispatchSpace, Spwsite $spw, Website $website): Website
    {
        $spw->setDisptachwebsite($dispatchSpace);
        $spw->setWebsite($website);
        $spw->setRole('admin');
        $spw->setIsadmin(false);
        $website->addSpwsite($spw);
        $dispatchSpace->addSpwsite($spw);
        //$spw->activeAdmin(); fonction pour donner les droit admin
        $this->em->persist($spw);
        $this->em->persist($dispatchSpace);
        $this->em->persist($website);
        $this->em->flush();
        return $website;
    }

    /**
     * @param $website Website
     * @param $form
     * @return Website
     */
    public function initTemplate(Website $website, $form): Website
    {
        $template=$website->getTemplate();
        $file=$form['template']['logotemplate']->getData();
        if($file!==null){ //todo remove ancien logo si changement
            $pict=new Pict();
            $this->uploadator->Upload($file, $pict);
            $template->setLogo($pict);
        }

        $filefond=$form['template']['background']->getData();
        if($filefond!==null){ //todo remove ancien logo si changement
            $background=new Background();
            $this->uploadator->Upload($filefond, $background);
            $template->setBackground($background);
        }
        $tagueirelist=$template->getTagueries();
        foreach ($tagueirelist as $sup){
            $template->removeTaguery($sup);
        }
        $tags=Tools::cleanTags($form['template']['tagueries']->getData());
        foreach ($tags as $tag){
            if(!$resulttag=$this->tagueryRepository->findOneBy([ 'name'=>$tag])){
                $resulttag=New Taguery();
                $resulttag->setName($tag);
                $resulttag->setPhylo($website->getSlug());
            }
            $template->addTaguery($resulttag);
        }
        return $website;
    }

}