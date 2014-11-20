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
