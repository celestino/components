<?php

/*
 * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

namespace Brickoo\Tests\Error;

use Brickoo\Error\ErrorHandler,
    PHPUnit_Framework_TestCase;

/**
 * ErrorHandlerTest
 *
 * Test suite for the ErrorHandler class.
 * @see Brickoo\Error\ErrorHandler
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ErrorHandlerTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Error\ErrorHandler::__construct */
    public function testConstructorInitializesProperties() {
        $eventDispatcher = $this->getEventDispatcherStub();
        $errorHandler = new ErrorHandler($eventDispatcher, true);
        $this->assertAttributeSame($eventDispatcher, "eventDispatcher", $errorHandler);
        $this->assertAttributeEquals(true, "convertToException", $errorHandler);
        $this->assertAttributeEquals(false, "isRegistered", $errorHandler);
    }

    /**
     * @covers Brickoo\Error\ErrorHandler::register
     * @covers Brickoo\Error\ErrorHandler::isRegistered
     * @covers Brickoo\Error\ErrorHandler::unregister
     */
    public function testRegisterAndUnregisterProcess() {
        $errorHandler = new ErrorHandler($this->getEventDispatcherStub());
        $this->assertSame($errorHandler, $errorHandler->register());
        $this->assertAttributeEquals(true, "isRegistered", $errorHandler);
        $this->assertSame($errorHandler, $errorHandler->unregister());
        $this->assertAttributeEquals(false, "isRegistered", $errorHandler);
    }

    /**
     * @covers Brickoo\Error\ErrorHandler::register
     * @covers Brickoo\Error\Exception\DuplicateHandlerRegistrationException
     * @expectedException Brickoo\Error\Exception\DuplicateHandlerRegistrationException
     */
    public function testRegisterDuplicateRegistrationThrowsException() {
        $errorHandler = new ErrorHandler($this->getEventDispatcherStub());
        $errorHandler->register();
        $errorHandler->register();
        $errorHandler->unregister();
    }

    /**
     * @covers Brickoo\Error\ErrorHandler::unregister
     * @covers Brickoo\Error\Exception\HandlerNotRegisteredException
     * @expectedException Brickoo\Error\Exception\HandlerNotRegisteredException
     */
    public function testUnregisterNotRegisteredHandlerThrowsException() {
        $errorHandler = new ErrorHandler($this->getEventDispatcherStub());
        $errorHandler->unregister();
    }

    /** @covers Brickoo\Error\ErrorHandler::handleError */
    public function testHandleErrorEventNotification() {
        $eventDispatcher = $this->getEventDispatcherStub();
        $eventDispatcher->expects($this->once())
                        ->method("notify")
                        ->with($this->isInstanceOf("\\Brickoo\\Error\\Event\\ErrorEvent"))
                        ->will($this->returnValue(null));

        $errorHandler = new ErrorHandler($eventDispatcher, false);
        $errorHandler->handleError(\E_ALL, "message", "file", 0);
    }

    /**
     * @covers Brickoo\Error\ErrorHandler::handleError
     * @covers Brickoo\Error\Exception\ErrorOccurredException
     * @expectedException Brickoo\Error\Exception\ErrorOccurredException
     */
    public function testHandleErrorConvertingToException() {
        $errorHandler = new ErrorHandler($this->getEventDispatcherStub(), true);
        $errorHandler->handleError(\E_ALL, "message", "file", 0);
    }

    /** @covers Brickoo\Error\ErrorHandler::__destruct */
    public function testDestructorUnregister() {
        $errorHandler = new ErrorHandler($this->getEventDispatcherStub());
        $errorHandler->register();
        $errorHandler->__destruct();
        $this->assertAttributeEquals(false, "isRegistered", $errorHandler);
    }

    /**
     * Returns an event manager stub.
     * @return \Brickoo\Event\EventDispatcher
     */
    private function getEventDispatcherStub() {
        return $this->getMockBuilder("\\Brickoo\\Event\\EventDispatcher")
            ->disableOriginalConstructor()
            ->getMock();
    }

}