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

namespace Tests\Brickoo\Component\Error;

use Brickoo\Component\Error\ExceptionHandler;
use PHPUnit_Framework_TestCase;

/**
 * ExceptionHandlerTest
 *
 * Test suite for the ExceptionHandler class.
 * @see Brickoo\Component\Error\ExceptionHandler
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ExceptionHandlerTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Error\ExceptionHandler::__construct
     * @covers Brickoo\Component\Error\ExceptionHandler::register
     * @covers Brickoo\Component\Error\ExceptionHandler::isRegistered
     * @covers Brickoo\Component\Error\ExceptionHandler::unregister
     */
    public function testRegisterAndUnregisterProcess() {
        $exceptionHandler = new ExceptionHandler($this->getMessageDispatcherStub());
        $this->assertSame($exceptionHandler, $exceptionHandler->register());
        $this->assertAttributeEquals(true, "isRegistered", $exceptionHandler);
        $this->assertSame($exceptionHandler, $exceptionHandler->unregister());
        $this->assertAttributeEquals(false, "isRegistered", $exceptionHandler);
    }

    /**
     * @covers Brickoo\Component\Error\ExceptionHandler::register
     * @covers Brickoo\Component\Error\Exception\DuplicateHandlerRegistrationException
     * @expectedException \Brickoo\Component\Error\Exception\DuplicateHandlerRegistrationException
     */
    public function testRegisterDuplicateRegistrationException() {
        $exceptionHandler = new ExceptionHandler($this->getMessageDispatcherStub());
        $exceptionHandler->register();
        $exceptionHandler->register();
        $exceptionHandler->unregister();
    }

    /**
     * @covers Brickoo\Component\Error\ExceptionHandler::unregister
     * @covers Brickoo\Component\Error\Exception\HandlerNotRegisteredException
     * @expectedException \Brickoo\Component\Error\Exception\HandlerNotRegisteredException
     */
    public function testUnregisterNotRegisteredHandlerThrowsException() {
        $exceptionHandler = new ExceptionHandler($this->getMessageDispatcherStub());
        $exceptionHandler->unregister();
    }

    /** @covers Brickoo\Component\Error\ExceptionHandler::handleException */
    public function testHandleExceptionExecutesMessageNotification() {
        $messageDispatcher = $this->getMessageDispatcherStub();
        $messageDispatcher->expects($this->once())
                     ->method("dispatch")
                     ->with($this->isInstanceOf("\\Brickoo\\Component\\Error\\Messaging\\Message\\ExceptionMessage"))
                     ->will($this->returnValue(null));

        $exceptionHandler = new ExceptionHandler($messageDispatcher);
        $exceptionHandler->handleException(new \Exception("test case exception thrown", 123));
    }

    /**
     * Returns an message manager stub.
     * @return \Brickoo\Component\Messaging\MessageDispatcher
     */
    private function getMessageDispatcherStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Messaging\\MessageDispatcher")
            ->disableOriginalConstructor()
            ->getMock();
    }

}
