<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Tests\Component\Messaging;

use Brickoo\Component\Messaging\MessageListener;
use PHPUnit_Framework_TestCase;

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
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidPriorityArgumentThrowsException() {
        new MessageListener("test.message", "wrongType", function(){});
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
