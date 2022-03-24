<?php

namespace App\Service\Registration;

use App\AffiEvents;
use App\Entity\Admin\NumClients;
use App\Entity\Admin\Numeratum;
use App\Entity\ApiToken;
use App\Entity\Customer\Customers;
use App\Entity\Customer\Services;
use App\Entity\UserMap\Heuristiques;
use App\Entity\Users\Contacts;
use App\Entity\User;
use App\Entity\Users\ProfilUser;
use App\Heuristique\Synapse;
use App\Repository\Entity\ProductsRepository;
use App\Service\Gestion\AutoCommande;
use App\Service\Gestion\Numerator;
use App\Event\DisptachEvent;
use App\Util\Canonicalizer;
use App\Util\DefaultModules;
use App\Util\PasswordUpdater;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreatorUser
{
    private Numerator $numerator;
    private PasswordUpdater $passwordUpdater;
    private Canonicalizer $canonicalizer;
    private EntityManagerInterface $em;
    private EventDispatcherInterface $eventDispatcher;
    private AutoCommande $autoCommande;
    private ProductsRepository $producRepro;


    public function __construct(ProductsRepository $productsRepository,EventDispatcherInterface $eventDispatcher, AutoCommande $autoCommande, Numerator $numerator, PasswordUpdater $passwordUpdater,Canonicalizer $canonicalizer, EntityManagerInterface $em)
    {
        $this->numerator=$numerator;
        $this->passwordUpdater=$passwordUpdater;
        $this->canonicalizer=$canonicalizer;
        $this->em=$em;
        $this->eventDispatcher=$eventDispatcher;
        $this->autoCommande=$autoCommande;
        $this->producRepro=$productsRepository;
    }

    /**
     * @param $tabmember
     * @return Customers
     * @throws Exception
     */
    public function createUserByMailToInvitWebsite($tabmember): Customers
    {
        $user = New User();
        $nums=$this->numerator->getActiveNumerate();
        $customer=$this->addUser($user, $nums, $tabmember);
        $event = new DisptachEvent($user,$customer->getProfil(),$tabmember['website']);
        $this->eventDispatcher->dispatch($event, AffiEvents::DISPATCH_REGISTRATION_SUCCESS);
        $this->em->persist($user);
        $this->em->flush();
        $this->autoCommande->newInscriptionCmd($customer, $nums);
        return $customer;
    }

    /**
     * @param $tabmember
     * @return Customers
     * @throws Exception
     */
    public function createUserByConversToJoinWebsite($tabmember): Customers
    {
        $user = New User();
        $nums=$this->numerator->getActiveNumerate();
        $customer=$this->addUser($user, $nums, $tabmember);
        $event = new DisptachEvent($user,$customer->getProfil(),$tabmember['website']);
        $this->eventDispatcher->dispatch($event, AffiEvents::ADD_CONTACT_SUCCESS);
        $this->em->persist($user);
        $this->em->flush();
        $this->autoCommande->newInscriptionCmd($customer, $nums);
        return $customer;
    }

    /**
     * @param $tabmember
     * @return Customers
     * @throws Exception
     */
    public function createUserByShop($tabmember): Customers
    {
        $user = New User();
        $nums=$this->numerator->getActiveNumerate();
        $customer=$this->addUser($user, $nums, $tabmember);
        $event = new DisptachEvent($user,$customer->getProfil(),$tabmember['website']);
        $this->eventDispatcher->dispatch($event, AffiEvents::ADD_CLIENT_SUCCESS);
        $this->em->persist($user);
        $this->em->flush();
        $this->autoCommande->newInscriptionCmd($customer, $nums);
        return $customer;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function adduser(User $user, Numeratum $nums, $tabmember): Customers
    {
        $contact=$tabmember['contact'];
        $stringpass=$tabmember['pass'];
        $mail=$tabmember['mail'];
        return $this->newCompte($user, $nums,$contact,$stringpass,$mail);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function inituser(User $user, $form, $contact, Numeratum $nums): Customers
    {
        $stringpass=$form['plainPassword']->getData() ?? "";
        $mail=$form['email']->getData() ?? "";
        return $this->newCompte($user, $nums,$contact,$stringpass,$mail);
    }

    /**
     * @param $user User
     * @param Numeratum $nums
     * @param $contact
     * @param string $stringpass
     * @param $mail
     * @return Customers
     * @throws NonUniqueResultException
     */
    public function newCompte(User $user, Numeratum $nums, $contact, string $stringpass, $mail): Customers
    {
        $apitoken = New ApiToken($user);
        $customer=new Customers();
        $user->setCustomer($customer);
        $numeroclient=New NumClients();

        foreach (DefaultModules::MODULE_LIST as $list){
            $rpo=$this->producRepro->findOneProduct($list);
            if($rpo){
                $service= new Services();
                $service->setNamemodule($list);
                $service->setProducts($rpo);
                $service->setDatestartAt(new DateTime());
                $customer->addService($service);
                $this->em->persist($service);
            }
        }

        if($contact){
            /** @var Contacts $contact */
            $contact->setActive(false);
            $customer->setOldcontact($contact);
            $this->em->persist($contact);
        }
        $identity = new ProfilUser();
        if(!$stringpass){
            $identity->setMdpfirst(bin2hex(random_bytes(5)));
        }else{
            $identity->setMdpfirst($stringpass);
        }
        $identity->setEmailfirst($mail);
        $customer->setProfil($identity);
        $user->addRole("ROLE_CUSTOMER");
        $user->setDatemajAt(new \DateTime());
        $user->setEmail($mail);


        $this->passwordUpdater->hashPasswordstring($user, $identity->getMdpfirst());
        $this->canonicalizer->updateCanonicalFields($user);
        $numeroclient->setNumero($nums->getNumClient());
        $numeroclient->setOrdre(date("Y"));
        $numeroclient->setIdcustomer($customer);

        $customer->setClient(true);
        $customer->setEmailcontact($user->getEmailCanonical());
        $customer->setNumclient($numeroclient);

        $heuristique = new Heuristiques($customer);
        $sys=Synapse::INSCRIPTION;
        $heuristique->setSem($sys[0]);
        $heuristique->setColor($sys[1]);
        $heuristique->setBinarycolor($sys[2]);

        $this->em->persist($heuristique);
        $this->em->persist($apitoken);
        $this->em->persist($numeroclient);
        $this->em->persist($customer);
        return $customer;
    }

}