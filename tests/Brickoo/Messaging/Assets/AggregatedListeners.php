<?php

namespace Brickoo\Tests\Messaging\Assets;

use Brickoo\Messaging\Message,
    Brickoo\Messaging\MessageDispatcher,
    Brickoo\Messaging\MessageListener,
    Brickoo\Messaging\ListenerAggregate;

class AggregatedListeners implements ListenerAggregate {

    public function attachListeners(MessageDispatcher $eventManager) {
        $eventManager->attach(new MessageListener(
            "test.event", 100, [$this, "listenerCallback"]
        ));
    }

    public function listenerCallback(Message $event, MessageDispatcher $eventDispatcher) {}

}