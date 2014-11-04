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

use Brickoo\Component\Error\Messaging\Messages;
use Brickoo\Component\Error\Messaging\Listener\ErrorLogMessageListener;
use PHPUnit_Framework_TestCase;

/**
 * ErrorLogMessageListenerTest
 *
 * Test suite for the ErrorLogMessageListener class.
 * @see Brickoo\Component\Error\Messaging\Listener\ErrorLogMessageListener
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ErrorLogMessageListenerTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Error\Messaging\Listener\ErrorLogMessageListener::getMessageName */
    public function testGetMessageName() {
        $errorLogListener = new ErrorLogMessageListener();
        $this->assertEquals(Messages::ERROR, $errorLogListener->getMessageName());
    }

    /** @covers Brickoo\Component\Error\Messaging\Listener\ErrorLogMessageListener::getPriority */
    public function testGetPriority() {
        $errorLogListener = new ErrorLogMessageListener();
        $this->assertInternalType("integer", $errorLogListener->getPriority());
    }

    /** @covers Brickoo\Component\Error\Messaging\Listener\ErrorLogMessageListener::handleMessage */
    public function testHandleMessageCallsMessageManager() {
        $errorMessage = "An error occurred.";

        $message = $this->getMockBuilder("\\Brickoo\\Component\\Error\\Messaging\\Message\\ErrorMessage")
            ->disableOriginalConstructor()
            ->getMock();
        $message->expects($this->once())
              ->method("getErrorMessage")
              ->will($this->returnValue($errorMessage));

        $messageDispatcher = $this->getMessageDispatcherStub();
        $messageDispatcher->expects($this->once())
                        ->method("dispatch")
                        ->with($this->isInstanceOf("\\Brickoo\\Component\\Log\\Messaging\\Message\\LogMessage"))
                        ->will($this->returnValue($messageDispatcher));

        $errorLogListener = new ErrorLogMessageListener();
        $this->assertNull($errorLogListener->handleMessage($message, $messageDispatcher));
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
