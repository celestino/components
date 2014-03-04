<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Brickoo\Tests\Component\Messaging;

use Brickoo\Component\Messaging\MessageListener,
    PHPUnit_Framework_TestCase;

/**
 * MessageListenerTest
 *
 * Test suite for the Listener class.
 * @see Brickoo\Component\Messaging\MessageListener
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageListenerTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Messaging\MessageListener::__construct
     * @expectedException InvalidArgumentException
     */
    public function testConstructorInvalidPriorityArgumentThrowsException() {
        $listener = new MessageListener("test.message", "wrongType", function(){});
    }

    /**
     * @covers Brickoo\Component\Messaging\MessageListener::__construct
     * @covers Brickoo\Component\Messaging\MessageListener::getMessageName
     */
    public function testGetMessageName() {
        $messageName = "test.message";
        $listener = new MessageListener($messageName, 0, function(){});
        $this->assertEquals($messageName, $listener->getMessageName());
    }

    /** @covers Brickoo\Component\Messaging\MessageListener::getPriority */
    public function testGetPriority() {
        $priority = 100;
        $listener = new MessageListener("test.message", $priority, function(){});
        $this->assertEquals($priority, $listener->getPriority());
    }

    /** @covers Brickoo\Component\Messaging\MessageListener::handleMessage */
    public function testHandleMessage() {
        $message = $this->getMock("\\Brickoo\\Component\\Messaging\\Message");
        $messageDispatcher = $this->getMockBuilder("\\Brickoo\\Component\\Messaging\\MessageDispatcher")
            ->disableOriginalConstructor()->getMock();
        $callback = function(){};
        $listener = new MessageListener("test.message", 0, $callback);
        $this->assertNull($listener->handleMessage($message, $messageDispatcher));
    }

}