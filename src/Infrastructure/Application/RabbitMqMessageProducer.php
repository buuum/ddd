<?php

namespace Ddd\Infrastructure\Application;

use Ddd\Application\MessageProducer;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqMessageProducer implements MessageProducer
{

    protected $connection;
    protected $channel;

    public function __construct(AMQPStreamConnection $aConnection)
    {
        $this->connection = $aConnection;
        $this->channel = null;
    }

    private function connect($exchangeName)
    {
        if (null !== $this->channel) {
            return;
        }
        $channel = $this->connection->channel();
        $channel->exchange_declare($exchangeName, 'fanout', false, true, false);
        $channel->queue_declare($exchangeName, false, true, false, false);
        $channel->queue_bind($exchangeName, $exchangeName);
        $this->channel = $channel;
    }

    protected function channel($exchangeName)
    {
        $this->connect($exchangeName);
        return $this->channel;
    }

    public function open($exchangeName)
    {

    }

    public function send(
        $exchangeName,
        $notificationMessage,
        $notificationType,
        $notificationId,
        \DateTime $notificationOccurredOn
    ) {
        $this->channel($exchangeName)->basic_publish(
            new AMQPMessage(
                $notificationMessage,
                ['type' => $notificationType, 'timestamp' => $notificationOccurredOn->getTimestamp(), 'message_id' => $notificationId]
            ),
            $exchangeName
        );
    }

    public function close($exchangeName)
    {
        $this->channel->close();
        $this->connection->close();
    }
}