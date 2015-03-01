<?php

namespace Brickoo\Tests\Component\Messaging\Assets;

use Brickoo\Component\Messaging\Message;
use Brickoo\Component\Messaging\MessageDispatcher;
use Brickoo\Component\Messaging\MessageListener;

class MessageListenerInfiniteRecursionFixture extends MessageListener {

    public function __construct() {
        parent::__construct(
            "message.test",
            0,
            function(Message $message, MessageDispatcher $dispatcher) {
                $dispatcher->dispatch($message);
            }
        );
    }

}
