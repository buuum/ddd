<?php

namespace Ddd\Application\Service;

use Ddd\Application\EventStore;
use Ddd\Application\MessageProducer;
use Ddd\Application\PublishedMessageTracker;
use Ddd\Domain\Event\StoredEvent;
use JMS\Serializer\SerializerBuilder;

class NotificationService
{
    protected $eventStore;
    protected $publishedMessageTracker;
    protected $messageProducer;

    public function __construct(
        EventStore $eventStore,
        PublishedMessageTracker $publishedMessageTracker,
        MessageProducer $messageProducer
    ) {
        $this->eventStore = $eventStore;
        $this->publishedMessageTracker = $publishedMessageTracker;
        $this->messageProducer = $messageProducer;
    }

    public function publishNotifications($exchangeName)
    {
        $publishedMessageTracker = $this->publishedMessageTracker();
        $notifications = $this->listUnpublishedNotifications(
            $publishedMessageTracker->mostRecentPublishedMessageId($exchangeName)
        );
        if (!$notifications) {
            return 0;
        }

        $messageProducer = $this->messageProducer();
        $messageProducer->open($exchangeName);

        try {
            $publishedMessages = 0;
            $lastPublishedNotification = null;
            foreach ($notifications as $notification) {
                $lastPublishedNotification = $this->publish($exchangeName, $notification, $messageProducer);
                $publishedMessages++;
            }
        } catch (\Exception $e) {
        }

        $this->trackMostRecentPublishedMessage($publishedMessageTracker, $exchangeName, $lastPublishedNotification);
        $messageProducer->close($exchangeName);

        return $publishedMessages;
    }

    private function publish($exchangeName, StoredEvent $notification, MessageProducer $messageProducer): StoredEvent
    {

        $messageProducer->send(
            $exchangeName,
            SerializerBuilder::create()->build()->serialize($notification, 'json'),
            $notification->typeName(),
            $notification->eventId(),
            $notification->occurredOn()
        );

        return $notification;
    }

    protected function publishedMessageTracker()
    {
        return $this->publishedMessageTracker;
    }

    /**
     * @param $mostRecentPublishedMessageId
     * @return StoredEvent[]
     */
    private function listUnpublishedNotifications($mostRecentPublishedMessageId)
    {
        $storeEvents = $this->eventStore()->allStoredEventsSince($mostRecentPublishedMessageId);
        return $storeEvents;
    }

    protected function eventStore()
    {
        return $this->eventStore;
    }

    private function trackMostRecentPublishedMessage(
        PublishedMessageTracker $publishedMessageTracker,
        $exchangeName,
        StoredEvent $notification
    ) {
        $publishedMessageTracker->trackMostRecentPublishedMessage($exchangeName, $notification);
    }

    private function messageProducer()
    {
        return $this->messageProducer;
    }
}