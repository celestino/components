<?php

namespace Brickoo\Tests\Component\Routing\Route\Collector\Assets;

use Brickoo\Component\Messaging\Message;
use Brickoo\Component\Messaging\MessageDispatcher;
use Brickoo\Component\Messaging\MessageListener;
use Brickoo\Component\Routing\Messaging\Messages;
use Brickoo\Component\Routing\Route\RouteCollection;

class RouteCollectorListenerFixture extends MessageListener {

    public function __construct(RouteCollection $routeCollection) {
        parent::__construct(
            Messages::COLLECT_ROUTES,
            0,
            function(Message $message, MessageDispatcher $dispatcher) use ($routeCollection) {
                $message->getResponseList()->add($routeCollection);
            }
        );
    }

}
