<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
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

namespace Brickoo\Tests\Component\Log\Messaging\Listener;

use Brickoo\Component\Log\Logger;
use Brickoo\Component\Log\Messaging\Listener\LogMessageListener;
use Brickoo\Component\Log\Messaging\Messages;

/**
 * LogMessageListenerTest
 *
 * Test suite for the LogMessageListener class.
 * @see Brickoo\Component\Log\Messaging\Listener\LogMessageListener
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class LogMessageListenerTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Log\Messaging\Listener\LogMessageListener::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorThrowsArgumentException() {
        new LogMessageListener($this->getLoggerStub(), "wrongType");
    }

    /**
     * @covers Brickoo\Component\Log\Messaging\Listener\LogMessageListener::__construct
     * @covers Brickoo\Component\Log\Messaging\Listener\LogMessageListener::getMessageName
     */
    public function testGetMessageName() {
        $listener = new LogMessageListener($this->getLoggerStub(), 100);
        $this->assertEquals(Messages::LOG, $listener->getMessageName());
    }

    /** @covers Brickoo\Component\Log\Messaging\Listener\LogMessageListener::getPriority */
    public function testGetPriority() {
        $priority = 99;
        $listener = new LogMessageListener($this->getLoggerStub(), $priority);
        $this->assertEquals($priority, $listener->getPriority());
    }

    /** @covers Brickoo\Component\Log\Messaging\Listener\LogMessageListener::handleMessage */
    public function testHandleMessageCallsLogger() {
        $messages = array("log this test message");
        $severity = Logger::SEVERITY_INFO;

        $logger = $this->getLoggerStub();
        $logger->expects($this->once())
               ->method("log")
               ->with($messages, $severity);

        $messageDispatcher = $this->getMessageDispatcherStub();

        $message = $this->getLogMessageStub();
        $message->expects($this->once())
                ->method("getMessages")
                ->will($this->returnValue($messages));
        $message->expects($this->once())
                ->method("getSeverity")
                ->will($this->returnValue($severity));


        $listener = new LogMessageListener($logger, 100);
        $this->assertNull($listener->handleMessage($message, $messageDispatcher));
    }

    /**
     * Returns a logger stub.
     * @return \Brickoo\Component\Log\Logger
     */
    private function getLoggerStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Log\\Logger")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a message dispatcher stub.
     * @return \Brickoo\Component\Messaging\MessageDispatcher
     */
    private function getMessageDispatcherStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Messaging\\MessageDispatcher")
            ->disableOriginalConstructor()
            ->getMock();

    }

    /**
     * Returns a message stub.
     * @return \Brickoo\Component\Log\Messaging\Message\LogMessage
     */
    private function getLogMessageStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Log\\Messaging\\Message\\LogMessage")
            ->disableOriginalConstructor()
            ->getMock();
    }

}
