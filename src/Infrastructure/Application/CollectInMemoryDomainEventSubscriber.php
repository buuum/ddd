<?php

namespace Ddd\Infrastructure\Application;

use Ddd\Domain\DomainEventSubscriber;

class CollectInMemoryDomainEventSubscriber implements DomainEventSubscriber
{
    protected $events = [];

    public function handle($aDomainEvent)
    {
        $this->events[] = $aDomainEvent;
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function isSubscribedTo($aDomainEvent)
    {
        return true;
    }

}