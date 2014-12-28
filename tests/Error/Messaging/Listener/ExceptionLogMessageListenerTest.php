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

namespace Brickoo\Tests\Component\Error\Messaging\Listener;

use Brickoo\Component\Error\Messaging\Messages;
use Brickoo\Component\Error\Messaging\Listener\ExceptionLogMessageListener;
use PHPUnit_Framework_TestCase;

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
