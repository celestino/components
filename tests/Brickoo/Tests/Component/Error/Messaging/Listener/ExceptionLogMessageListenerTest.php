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

namespace Brickoo\Tests\Component\Error\Messaging\Listener;

use Brickoo\Component\Error\Messaging\Messages,
    Brickoo\Component\Error\Messaging\Listener\ExceptionLogMessageListener,
    PHPUnit_Framework_TestCase;

/**
 * ExceptionLogMessageListenerTest
 *
 * Test suite for the ExceptionLogMessageListener class.
 * @see Brickoo\Component\Error\Messaging\Listener\ExceptionLogMessageListener
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ExceptionLogMessageListenerTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Error\Messaging\Listener\ExceptionLogMessageListener::getMessageName */
    public function testGetMessageName() {
        $exceptionLogListener = new ExceptionLogMessageListener();
        $this->assertEquals(Messages::EXCEPTION, $exceptionLogListener->getMessageName());
    }

    /** @covers Brickoo\Component\Error\Messaging\Listener\ExceptionLogMessageListener::getPriority */
    public function testGetPriority() {
        $exceptionLogListener = new ExceptionLogMessageListener();
        $this->assertInternalType("integer", $exceptionLogListener->getPriority());
    }

    /**
     * @covers Brickoo\Component\Error\Messaging\Listener\ExceptionLogMessageListener::handleMessage
     * @covers Brickoo\Component\Error\Messaging\Listener\ExceptionLogMessageListener::getExceptionMessage
     * @covers Brickoo\Component\Error\Messaging\Listener\ExceptionLogMessageListener::generateLogMessage
     */
    public function testHandleMessageCallsMessageManager() {
        $previousException = new \Exception();
        $exception = new \Exception("Some exception message", 0, $previousException);

        $message = $this->getMockBuilder("\\Brickoo\\Component\\Error\\Messaging\\Message\\ExceptionMessage")
            ->disableOriginalConstructor()
            ->getMock();
        $message->expects($this->once())
              ->method("getException")
              ->will($this->returnValue($exception));

        $messageDispatcher = $this->getMessageDispatcherStub();
        $messageDispatcher->expects($this->once())
                     ->method("dispatch")
                     ->with($this->isInstanceOf("\\Brickoo\\Component\\Log\\Messaging\\Message\\LogMessage"))
                     ->will($this->returnValue($messageDispatcher));

        $exceptionLogListener = new ExceptionLogMessageListener();
        $this->assertNull($exceptionLogListener->handleMessage($message, $messageDispatcher));
    }

    /**
     * Returns an message dispatcher stub.
     * @return \Brickoo\Component\Messaging\MessageDispatcher
     */
    private function getMessageDispatcherStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Messaging\\MessageDispatcher")
            ->disableOriginalConstructor()
            ->getMock();
    }

}