<?php


namespace App\Service\Modules;


use App\Entity\Module\Reservation;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class Resator
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Resator constructor.
     * @param EntityManagerInterface $entityManager

     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function newResa($parametre){  //['form'=>$form,'spaceweb'=>$spaceweb,'module'=>$module]

        $resa=new Reservation();
            $resa->setNbCouverts($parametre['form']->get('nbCouverts')->getData());
            $resa->setSalle($parametre['form']->get('salle')->getData());
            $resa->setCommentaire($parametre['form']->get('content')->getData());
            $resa->setSpaceweb($parametre['spaceweb']);
            $resa->setStrtimeresa($parametre['form']->get('strtimereserve')->getData());
            $resa->setMsgsend(true);
        try {
            $resa->setDateresaAt(new DateTime(str_replace(",", "-", $parametre['form']->get('datereserve')->getData())));
        } catch (\Exception $e) {
            $resa->setDateresaAt(new DateTime());
            dump('error sur date reservation');// todo faire mieux
        }
        $this->entityManager->persist($resa);
        $this->entityManager->flush();
        return $resa;
    }



}