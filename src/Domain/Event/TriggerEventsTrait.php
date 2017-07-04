<?php

namespace Ddd\Domain\Event;

use Ddd\Domain\DomainEventPublisher;

trait TriggerEventsTrait
{
    private $events = [];

    protected function triggerEvent($event)
    {
        DomainEventPublisher::instance()->publish($event);
    }

    protected function trigger($event)
    {
        $this->events[] = $event;
    }

    public function getEvents()
    {
        return $this->events;
    }
}