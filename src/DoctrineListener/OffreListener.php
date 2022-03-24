<?php


namespace App\DoctrineListener;

use App\Entity\Marketplace\Offres;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class OffreListener
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Offres $offre, LifecycleEventArgs $event)
    {
        $offre->offreSlug($this->slugger);
    }

    public function preUpdate(Offres $offre, LifecycleEventArgs $event)
    {
        $offre->offreSlug($this->slugger);
    }
}