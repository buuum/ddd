<?php

namespace Ddd\Application;

use Ddd\Domain\DomainEvent;
use Ddd\Domain\Event\StoredEvent;

interface EventStore
{
    /**
     * @param DomainEvent $aDomainEvent
     * @return mixed
     */
    public function append($aDomainEvent);

    /**
     * @param $anEventId
     * @return StoredEvent[]
     */
    public function allStoredEventsSince($anEventId);

    public function all($page, $pageSize);
}