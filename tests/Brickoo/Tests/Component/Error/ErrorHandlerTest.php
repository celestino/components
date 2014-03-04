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

namespace Brickoo\Tests\Component\Error;

use Brickoo\Component\Error\ErrorHandler,
    PHPUnit_Framework_TestCase;

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
        $errorHandler = new ErrorHandler($messageDispatcher, "wrongType");
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
     * @expectedException Brickoo\Component\Error\Exception\DuplicateHandlerRegistrationException
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
     * @expectedException Brickoo\Component\Error\Exception\HandlerNotRegisteredException
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
                        ->with($this->isInstanceOf("\\Brickoo\\Component\\Error\\Message\\ErrorMessage"))
                        ->will($this->returnValue(null));

        $errorHandler = new ErrorHandler($messageDispatcher, false);
        $errorHandler->handleError(\E_ALL, "message", "file", 0);
    }

    /**
     * @covers Brickoo\Component\Error\ErrorHandler::handleError
     * @covers Brickoo\Component\Error\Exception\ErrorOccurredException
     * @expectedException Brickoo\Component\Error\Exception\ErrorOccurredException
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