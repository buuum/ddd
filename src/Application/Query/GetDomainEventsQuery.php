<?php

namespace Ddd\Application\Query;

use Ddd\Application\EventDataTransformer;
use Ddd\Application\EventStore;

class GetDomainEventsQuery
{

    const PAGE_SIZE = 10;

    protected $eventStore;
    protected $dataTransformer;

    public function __construct(EventStore $eventStore, EventDataTransformer $dataTransformer)
    {
        $this->eventStore = $eventStore;
        $this->dataTransformer = $dataTransformer;
    }

    public function query($page = 1)
    {
        return $this->dataTransformer->transform(
            $this->eventStore->all($page, self::PAGE_SIZE),
            $page,
            self::PAGE_SIZE
        );
    }
}
