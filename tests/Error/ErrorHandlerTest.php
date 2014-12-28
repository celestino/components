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

namespace Brickoo\Tests\Component\Error;

use Brickoo\Component\Error\ErrorHandler;
use PHPUnit_Framework_TestCase;

/**
 * ErrorHandlerTest
 *
 * Test suite for the ErrorHandler class.
 * @see Brickoo\Component\Error\ErrorHandler
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ErrorHandlerTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Error\ErrorHandler::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructor() {
        $messageDispatcher = $this->getMessageDispatcherStub();
        new ErrorHandler($messageDispatcher, "wrongType");
    }

    /**
     * @covers Brickoo\Component\Error\ErrorHandler::__construct
     * @covers Brickoo\Component\Error\ErrorHandler::register
     * @covers Brickoo\Component\Error\ErrorHandler::isRegistered
     * @covers Brickoo\Component\Error\ErrorHandler::unregister
     */
    public function testRegisterAndUnregisterProcess() {
        $errorHandler = new ErrorHandler($this->getMessageDispatcherStub());
        $this->assertSame($errorHandler, $errorHandler->register());
        $this->assertAttributeEquals(true, "isRegistered", $errorHandler);
        $this->assertSame($errorHandler, $errorHandler->unregister());
        $this->assertAttributeEquals(false, "isRegistered", $errorHandler);
    }

    /**
     * @covers Brickoo\Component\Error\ErrorHandler::register
     * @covers Brickoo\Component\Error\Exception\DuplicateHandlerRegistrationException
     * @expectedException \Brickoo\Component\Error\Exception\DuplicateHandlerRegistrationException
     */
    public function testRegisterDuplicateRegistrationThrowsException() {
        $errorHandler = new ErrorHandler($this->getMessageDispatcherStub());
        $errorHandler->register();
        $errorHandler->register();
        $errorHandler->unregister();
    }

    /**
     * @covers Brickoo\Component\Error\ErrorHandler::unregister
     * @covers Brickoo\Component\Error\Exception\HandlerNotRegisteredException
     * @expectedException \Brickoo\Component\Error\Exception\HandlerNotRegisteredException
     */
    public function testUnregisterNotRegisteredHandlerThrowsException() {
        $errorHandler = new ErrorHandler($this->getMessageDispatcherStub());
        $errorHandler->unregister();
    }

    /** @covers Brickoo\Component\Error\ErrorHandler::handleError */
    public function testHandleErrorMessageNotification() {
        $messageDispatcher = $this->getMessageDispatcherStub();
        $messageDispatcher->expects($this->once())
                        ->method("dispatch")
                        ->with($this->isInstanceOf("\\Brickoo\\Component\\Error\\Messaging\\Message\\ErrorMessage"))
                        ->will($this->returnValue(null));

        $errorHandler = new ErrorHandler($messageDispatcher, false);
        $errorHandler->handleError(\E_ALL, "message", "file", 0);
    }

    /**
     * @covers Brickoo\Component\Error\ErrorHandler::handleError
     * @covers Brickoo\Component\Error\Exception\ErrorOccurredException
     * @expectedException \Brickoo\Component\Error\Exception\ErrorOccurredException
     */
    public function testHandleErrorConvertingToException() {
        $errorHandler = new ErrorHandler($this->getMessageDispatcherStub(), true);
        $errorHandler->handleError(\E_ALL, "message", "file", 0);
    }

    /** @covers Brickoo\Component\Error\ErrorHandler::__destruct */
    public function testDestructorUnregister() {
        $errorHandler = new ErrorHandler($this->getMessageDispatcherStub());
        $errorHandler->register();
        $errorHandler->__destruct();
        $this->assertAttributeEquals(false, "isRegistered", $errorHandler);
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
