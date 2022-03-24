<?php


namespace App\DoctrineListener;

use App\Entity\Module\Reservation;
use App\Service\LogAdmin;
use Doctrine\ORM\Event\LifecycleEventArgs;


class ResaCreationListener
{
    private $reservationMailer;
    private $logadmin;

    public function __construct(LogAdmin $logAdmin)
    {
        $this->logadmin=$logAdmin;
    }

    public function postPersist(LifecycleEventArgs $args){
        $entity= $args->getObject();

        if(!$entity instanceof Reservation){
            return;
        }

        $log['spaceweb']=$entity->getSpaceweb()->getId();
        $log["event"]="notification mail";

        try{
            $log["confirm"]=true;

        } catch (\Exception $e){
            $log["confirm"]=false;

            $log["codeerror"]=$e->getCode();
            $log["messageerror"]=$e->getMessage();
        }

        $this->logadmin->NewLogResa($log);

    }
}