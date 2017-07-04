<?php

namespace Ddd\Infrastructure\Application;

use Ddd\Application\EventStore;
use Ddd\Domain\DomainEventPublisher;
use League\Tactician\Middleware;

class DomainEventsMiddleware implements Middleware
{
    protected $eventStore;

    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function execute($command, callable $next)
    {

        $domainEventPublisher = DomainEventPublisher::instance();
        $domainEventsCollector = new CollectInMemoryDomainEventSubscriber();
        $domainEventPublisher->subscribe($domainEventsCollector);

        $returnValue = $next($command);

        $events = $domainEventsCollector->getEvents();
        foreach ($events as $event) {
            $this->eventStore->append($event);
        }

        return $returnValue;
    }
}