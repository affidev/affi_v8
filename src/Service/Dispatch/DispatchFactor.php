<?php


namespace App\Service\Dispatch;


use App\AffiEvents;
use App\Entity\Customer\Customers;
use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\DispatchSpace\Spwsite;
use App\Entity\Websites\Website;
use App\Event\CustomerEvent;
use App\Repository\Entity\GpsRepository;
use App\Service\Localisation\LocalisationServices;
use App\Service\Registration\Sessioninit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


class DispatchFactor
{
    private EntityManagerInterface $em;
    private Sessioninit $sessionInit;
    private GpsRepository $gpsRepository;
    private EventDispatcherInterface $eventDispatcher;
    private LocalisationServices $localise;



    public function __construct( EntityManagerInterface $em,
                                 Sessioninit $sessionInit,GpsRepository $gpsRepository,
                                EventDispatcherInterface $eventDispatcher, LocalisationServices $localise){

        $this->em = $em;
        $this->sessionInit = $sessionInit;
        $this->eventDispatcher = $eventDispatcher;
        $this->localise = $localise;
        $this->gpsRepository = $gpsRepository;

    }


    /**
     * @param $dispatch DispatchSpaceWeb
     * @param $website Website
     * @return DispatchSpaceWeb
     */
    public function addwebsitedispatch(DispatchSpaceWeb $dispatch, Website $website): DispatchSpaceWeb
    {

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
        $this->sessionInit->preInitSpaceWeb($dispatch);
        $this->sessionInit->chenageLoc($dispatch);
        return $dispatch;
    }

    /**
     * @param $objcustomer Customers
     * @param $website
     * @return DispatchSpaceWeb
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function NewDispatchByWebsite(Customers $objcustomer, $website): DispatchSpaceWeb
    {
        $dispatch=new DispatchSpaceWeb();
        $dispatch->setPermission([0,0,0]); // creation autonome
        $dispatch->setCustomer($objcustomer);
        $objcustomer->setDispatchspace($dispatch);
        $dispatch->setName($objcustomer->getProfil()->getFirstname()??$objcustomer->getProfil()->getEmailfirst());
        $dispatch->setLocality($website->getLocality());
        $this->em->persist($dispatch);
        $this->em->flush();
        $this->sessionInit->preInitSpaceWeb($dispatch);
        $this->sessionInit->chenageLoc($dispatch);
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
        $this->sessionInit->preInitSpaceWeb($dispatch);
        $this->sessionInit->chenageLoc($dispatch);
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
        $this->sessionInit->preInitSpaceWeb($dispatch);
        $this->sessionInit->chenageLoc($dispatch);
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
        $this->sessionInit->chenageLoc($dispatch);
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
}