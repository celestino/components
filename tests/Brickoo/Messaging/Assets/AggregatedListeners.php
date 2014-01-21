<?php

namespace Brickoo\Tests\Messaging\Assets;

use Brickoo\Messaging\Message,
    Brickoo\Messaging\MessageDispatcher,
    Brickoo\Messaging\MessageListener,
    Brickoo\Messaging\ListenerAggregate;

class AggregatedListeners implements ListenerAggregate {

    public function attachListeners(MessageDispatcher $messageManager) {
        $messageManager->attach(new MessageListener(
            "test.message", 100, [$this, "listenerCallback"]
        ));
    }

    public function listenerCallback(Message $message, MessageDispatcher $messageDispatcher) {}

}