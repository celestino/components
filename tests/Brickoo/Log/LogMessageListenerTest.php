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

namespace Brickoo\Tests\Log;

use Brickoo\Log\Logger,
    Brickoo\Log\LogMessageListener,
    Brickoo\Log\Messages;

/**
 * LogMessageListenerTest
 *
 * Test suite for the LogMessageListener class.
 * @see Brickoo\Log\LogMessageListener
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class LogMessageListenerTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Log\LogMessageListener::__construct
     * @expectedException InvalidArgumentException
     */
    public function testConstructorThrowsArgumentException() {
        $listener = new LogMessageListener($this->getLoggerStub(), "wrongType");
    }

    /**
     * @covers Brickoo\Log\LogMessageListener::__construct
     * @covers Brickoo\Log\LogMessageListener::getMessageName
     */
    public function testGetMessageName() {
        $listener = new LogMessageListener($this->getLoggerStub(), 100);
        $this->assertEquals(Messages::LOG, $listener->getMessageName());
    }

    /** @covers Brickoo\Log\LogMessageListener::getPriority */
    public function testGetPriority() {
        $priority = 99;
        $listener = new LogMessageListener($this->getLoggerStub(), $priority);
        $this->assertEquals($priority, $listener->getPriority());
    }

    /** @covers Brickoo\Log\LogMessageListener::handleMessage */
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
     * @return \Brickoo\Log\LogMessageListener
     */
    private function getLoggerStub() {
        return $this->getMockBuilder("\\Brickoo\\Log\\Logger")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a message dispatcher stub.
     * @return \Brickoo\Messaging\MessageDispatcher
     */
    private function getMessageDispatcherStub() {
        return $this->getMockBuilder("\\Brickoo\\Messaging\\MessageDispatcher")
            ->disableOriginalConstructor()
            ->getMock();

    }

    /**
     * Returns a message stub.
     * @return \Brickoo\Log\LogMessage
     */
    private function getLogMessageStub() {
        return $this->getMockBuilder("\\Brickoo\\Log\\LogMessage")
            ->disableOriginalConstructor()
            ->getMock();
    }

}