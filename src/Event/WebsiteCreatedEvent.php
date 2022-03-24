<?php

namespace App\Event;

use App\Entity\Websites\Website;
use Symfony\Contracts\EventDispatcher\Event;

class WebsiteCreatedEvent extends Event
{
    public const CREATE = 'website.create';
    public const MAJ = 'website.maj';
    public const SHOW_WEBSITE = 'website.show';

    protected Website $website;

    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    public function getWebsite(): Website
    {
        return $this->website;
    }
}
