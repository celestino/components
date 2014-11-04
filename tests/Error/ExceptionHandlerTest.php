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
