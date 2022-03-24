<?php

namespace App\Event;

use App\Entity\Websites\Website;

class ContentDeletedEvent
{
    private Website $content;

    public function __construct(Website $content)
    {
        $this->content = $content;
    }

    public function getContent(): Website
    {
        return $this->content;
    }
}
