<?php

namespace Ddd\Application;

interface EventDataTransformer
{
    public function transform($events, $page, $pageSize);
}