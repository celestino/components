<?php

namespace Brickoo\Tests\Event\Assets;

use Brickoo\Event\Event,
    Brickoo\Event\EventDispatcher,
    Brickoo\Event\GenericListener,
    Brickoo\Event\ListenerAggregate;

class AggregatedListeners implements ListenerAggregate {

    public function attachListeners(EventDispatcher $eventManager) {
        $eventManager->attach(new GenericListener(
            "test.event", 100, [$this, "listenerCallback"]
        ));
    }

    public function listenerCallback(Event $event, EventDispatcher $eventDispatcher) {}

}