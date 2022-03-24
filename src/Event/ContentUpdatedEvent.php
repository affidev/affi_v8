<?php

namespace App\Event;


use App\Entity\Websites\Website;

class ContentUpdatedEvent
{
    private Website $content;
    private Website $previous;

    public function __construct(Website $content, Website $previous)
    {
        $this->content = $content;
        $this->previous = $previous;
    }

    public function getContent(): Website
    {
        return $this->content;
    }
}
