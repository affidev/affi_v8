<?php


namespace App\DoctrineListener;

use App\Entity\Websites\Website;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class WebsiteListener
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Website $website, LifecycleEventArgs $event)
    {
        $website->websiteSlug($this->slugger);
    }

    public function preUpdate(Website $website, LifecycleEventArgs $event)
    {
        $website->websiteSlug($this->slugger);
    }
}