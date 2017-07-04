<?php

namespace Ddd\Application;

use Ddd\Domain\DomainEvent;

interface PublishedMessageTracker
{
    /**
     * @param $aTypeName
     * @return int
     */
    public function mostRecentPublishedMessageId($aTypeName);

    /**
     * @param $aTypeName
     * @param DomainEvent $notification
     */
    public function trackMostRecentPublishedMessage($aTypeName, $notification);

}