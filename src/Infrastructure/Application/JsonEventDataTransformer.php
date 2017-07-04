<?php

namespace Ddd\Infrastructure\Application;

use Buuum\Dispatcher;
use Ddd\Application\EventDataTransformer;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonEventDataTransformer implements EventDataTransformer
{
    protected $serializer;
    protected $router;

    public function __construct(Serializer $serializer, Dispatcher $router)
    {
        $this->serializer = $serializer;
        $this->router = $router;
    }

    public function transform($events, $page, $pageSize): JsonResponse
    {
        $numberOfEvents = count($events);
        $areThereAnyEvents = !($numberOfEvents === 0);

        $jsonResponse = new JsonResponse(
            $this->serializer->serialize(
                [
                    '_meta'  => [
                        'count' => $numberOfEvents,
                        'page'  => $page
                    ],
                    '_links' => $this->calculateLinks($page, $pageSize, $numberOfEvents),
                    'data'   => $events
                ],
                'json'
            ),
            $areThereAnyEvents ? 200 : 404,
            [],
            true
        );

        return $jsonResponse;
    }

    protected function calculateLinks($page, $pageSize, $numberOfEvents)
    {
        $links = [];

        $links['first'] = $this->generateUrl('api_events', ['page' => 1]);

        if ($page > 1) {
            $links['prev'] = $this->generateUrl('api_events', ['page' => $page - 1]);
        }

        if ($numberOfEvents === $pageSize) {
            $links['next'] = $this->generateUrl('api_events', ['page' => $page + 1]);
        }

        $links['self'] = $this->generateUrl('api_events', ['page' => $page]);

        return $links;
    }

    protected function generateUrl($name, $params = [])
    {
        return $this->router->getUrlRequest($name, $params);
    }


}