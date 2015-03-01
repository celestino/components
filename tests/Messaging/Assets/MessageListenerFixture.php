<?php

namespace Brickoo\Tests\Component\Messaging\Assets;

use Brickoo\Component\Messaging\Message;
use Brickoo\Component\Messaging\MessageDispatcher;
use Brickoo\Component\Messaging\MessageListener;

class MessageListenerFixture extends MessageListener {

    public function __construct($doStopMessageDispatch = false) {
        parent::__construct(
            "message.test",
            0,
            function(Message $message, MessageDispatcher $dispatcher) use ($doStopMessageDispatch) {
                $message->getResponseList()->add($message->getName()." processed");
                if ($doStopMessageDispatch) {
                    $message->stop();
                }
            }
        );
    }

}
