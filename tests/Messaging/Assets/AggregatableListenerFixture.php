<?php

namespace Brickoo\Tests\Component\Messaging\Assets;

use Brickoo\Component\Messaging\Message;
use Brickoo\Component\Messaging\MessageDispatcher;
use Brickoo\Component\Messaging\MessageListener;
use Brickoo\Component\Messaging\ListenerAggregate;

class AggregatableListenerFixture implements ListenerAggregate {

    public function attachListeners(MessageDispatcher $messageManager) {
        $messageManager->attach(new MessageListener(
            "test.message", 100, [$this, "listenerCallback"]
        ));
    }

    public function listenerCallback(Message $message, MessageDispatcher $messageDispatcher) {}

}
