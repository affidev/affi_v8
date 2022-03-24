<?php


namespace App\DoctrineListener;

use App\Entity\DispatchSpace\DispatchSpaceWeb;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class DispatchListener
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(DispatchSpaceWeb $dispatchSpaceWeb, LifecycleEventArgs $event)
    {
        $dispatchSpaceWeb->dispatchSpaceWebSlug($this->slugger);
    }

    public function preUpdate(DispatchSpaceWeb $dispatchSpaceWeb, LifecycleEventArgs $event)
    {
        $dispatchSpaceWeb->dispatchSpaceWebSlug($this->slugger);
    }
}