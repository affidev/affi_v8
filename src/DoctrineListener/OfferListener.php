<?php


namespace App\DoctrineListener;

use App\Entity\Module\Offer;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class OfferListener
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Offer $offer, LifecycleEventArgs $event)
    {
        $offer->offerSlug($this->slugger);
    }

    public function preUpdate(Offer $offer, LifecycleEventArgs $event)
    {
        $offer->offerSlug($this->slugger);
    }
}