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

namespace Tests\Brickoo\Error;

use Brickoo\Error\ExceptionHandler,
    PHPUnit_Framework_TestCase;

/**
 * ExceptionHandlerTest
 *
 * Test suite for the ExceptionHandler class.
 * @see Brickoo\Error\ExceptionHandler
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ExceptionHandlerTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Error\ExceptionHandler::__construct
     * @covers Brickoo\Error\ExceptionHandler::register
     * @covers Brickoo\Error\ExceptionHandler::isRegistered
     * @covers Brickoo\Error\ExceptionHandler::unregister
     */
    public function testRegisterAndUnregisterProcess() {
        $exceptionHandler = new ExceptionHandler($this->getMessageDispatcherStub());
        $this->assertSame($exceptionHandler, $exceptionHandler->register());
        $this->assertAttributeEquals(true, "isRegistered", $exceptionHandler);
        $this->assertSame($exceptionHandler, $exceptionHandler->unregister());
        $this->assertAttributeEquals(false, "isRegistered", $exceptionHandler);
    }

    /**
     * @covers Brickoo\Error\ExceptionHandler::register
     * @covers Brickoo\Error\Exception\DuplicateHandlerRegistrationException
     * @expectedException Brickoo\Error\Exception\DuplicateHandlerRegistrationException
     */
    public function testRegisterDuplicateRegistrationException() {
        $exceptionHandler = new ExceptionHandler($this->getMessageDispatcherStub());
        $exceptionHandler->register();
        $exceptionHandler->register();
        $exceptionHandler->unregister();
    }

    /**
     * @covers Brickoo\Error\ExceptionHandler::unregister
     * @covers Brickoo\Error\Exception\HandlerNotRegisteredException
     * @expectedException Brickoo\Error\Exception\HandlerNotRegisteredException
     */
    public function testUnregisterNotregisteredHandlerThrowsException() {
        $exceptionHandler = new ExceptionHandler($this->getMessageDispatcherStub());
        $exceptionHandler->unregister();
    }

    /** @covers Brickoo\Error\ExceptionHandler::handleException */
    public function testHandleExceptionExecutesMessageNotification() {
        $messageDispatcher = $this->getMessageDispatcherStub();
        $messageDispatcher->expects($this->once())
                     ->method("dispatch")
                     ->with($this->isInstanceOf("\\Brickoo\\Error\\Message\\ExceptionMessage"))
                     ->will($this->returnValue(null));

        $exceptionHandler = new ExceptionHandler($messageDispatcher);
        $exceptionHandler->handleException(new \Exception("test case exception throwed", 123));
    }

    /** @covers Brickoo\Error\ExceptionHandler::__destruct */
    public function testDestructorUnregister() {
        $exceptionHandler = new ExceptionHandler($this->getMessageDispatcherStub());
        $exceptionHandler->register();
        $exceptionHandler->__destruct();
        $this->assertAttributeEquals(false, "isRegistered", $exceptionHandler);
    }

    /**
     * Returns an message manager stub.
     * @return \Brickoo\Messaging\MessageDispatcher
     */
    private function getMessageDispatcherStub() {
        return $this->getMockBuilder("\\Brickoo\\Messaging\\MessageDispatcher")
            ->disableOriginalConstructor()
            ->getMock();
    }

}