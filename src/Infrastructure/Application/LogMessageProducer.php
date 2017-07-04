<?php

namespace Ddd\Infrastructure\Application;

use Ddd\Application\MessageProducer;

class LogMessageProducer implements MessageProducer
{
    protected $txt;
    protected $file = __DIR__ . '/log.txt';

    public function open($exchangeName)
    {
        $this->txt = file_get_contents($this->file);
    }

    public function send(
        $exchangeName,
        $notificationMessage,
        $notificationType,
        $notificationId,
        \DateTime $notificationOccurredOn
    ) {

        $this->txt .= $exchangeName;
        $this->txt .= "\n";
        $this->txt .= $notificationType;
        $this->txt .= "\n";
        $this->txt .= $notificationMessage;
        $this->txt .= "\n";
    }

    public function close($exchangeName)
    {
        file_put_contents($this->file, $this->txt);
    }
}