<?php

namespace Ddd\Infrastructure\Domain\Event;

use Ddd\Application\EventStore;
use Ddd\Domain\Event\StoredEvent;
use Doctrine\ORM\EntityRepository;
use JMS\Serializer\SerializerBuilder;

class DoctrineEventRepository extends EntityRepository implements EventStore
{

    public function append($aDomainEvent)
    {
        $this->getEntityManager()->persist(new StoredEvent(
            get_class($aDomainEvent),
            $aDomainEvent->occurredOn(),
            SerializerBuilder::create()->build()->serialize($aDomainEvent, 'json')
        ));

    }

    public function allStoredEventsSince($anEventId)
    {
        $query = $this->createQueryBuilder('e');
        if ($anEventId) {
            $query->where('e.eventId > :eventId');
            $query->setParameters(array('eventId' => $anEventId));
        }
        $query->orderBy('e.eventId');
        return $query->getQuery()->getResult();
    }

    public function all($page, $pageSize)
    {
        $query = $this->getEntityManager()->createQuery('SELECT u FROM ' . StoredEvent::class . ' u');
        $query->setFirstResult(($page - 1) * $pageSize);
        $query->setMaxResults($pageSize);
        return $query->getResult();
    }
}