<?php


namespace App\Service\Registration;


use App\AffiEvents;
use App\Entity\User;
use App\Event\GetResponseUserEvent;
use App\Service\Gestion\AutoCommande;
use App\Service\Gestion\Numerator;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;


class Identificator
{


    private EventDispatcherInterface $eventdispatcher;
    private CreatorUser $creator;
    private Numerator $numerator;
    private AutoCommande $autoCommande;


    public function __construct(EventDispatcherInterface $eventDispatcher,Numerator $numerator, CreatorUser $creator,AutoCommande $autoCommande)
    {
        $this->eventdispatcher=$eventDispatcher;
        $this->creator=$creator;
        $this->numerator=$numerator;
        $this->autoCommande=$autoCommande;

    }
    public function newUser(): User|Response|null
    {
        $user = New User();
        $event = new GetResponseUserEvent($user);
        $this->eventdispatcher->dispatch($event, AffiEvents::REGISTRATION_INITIALIZE);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        return $user;
    }

    /**
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function creator($user, $form){
        $contact="";
        $nums=$this->numerator->getActiveNumeratecustomer();
        $customer=$this->creator->inituser($user, $form, $contact, $nums);
        $this->autoCommande->newInscriptionCmd($customer->getNumclient());
    }
}