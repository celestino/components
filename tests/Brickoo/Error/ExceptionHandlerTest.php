<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    use Brickoo\Error\ExceptionHandler;

    /**
     * ExceptionHandlerTest
     *
     * Test suite for the ExceptionHandler class.
     * @see Brickoo\Error\ExceptionHandler
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ExceptionHandlerTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Error\ExceptionHandler::__construct
         */
        public function testExceptionHandlerConstructor() {
            $ExceptionHandler = new ExceptionHandler(true);
            $this->assertAttributeEquals(true, 'displayExceptions', $ExceptionHandler);
        }

        /**
         * @covers Brickoo\Error\ExceptionHandler::setEventManager
         */
        public function testSetEventManager() {
            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $ExceptionHandler = new ExceptionHandler();
            $ExceptionHandler->setEventManager($EventManager);
            $this->assertAttributeEquals($EventManager, 'EventManager', $ExceptionHandler);
        }

        /**
         * @covers Brickoo\Error\ExceptionHandler::register
         * @covers Brickoo\Error\ExceptionHandler::isRegistered
         * @covers Brickoo\Error\ExceptionHandler::unregister
         */
        public function testRegisterAndUnregisterProcess() {
            $ExceptionHandler = new ExceptionHandler(true);
            $this->assertSame($ExceptionHandler, $ExceptionHandler->register());
            $this->assertAttributeEquals(true, 'isRegistered', $ExceptionHandler);
            $this->assertSame($ExceptionHandler, $ExceptionHandler->unregister());
            $this->assertAttributeEquals(false, 'isRegistered', $ExceptionHandler);
        }

        /**
         * @covers Brickoo\Error\ExceptionHandler::register
         * @covers Brickoo\Error\Exceptions\DuplicateHandlerRegistration
         * @expectedException Brickoo\Error\Exceptions\DuplicateHandlerRegistration
         */
        public function testRegisterDuplicateRegistrationException() {
            $ExceptionHandler = new ExceptionHandler(true);
            $ExceptionHandler->register();
            $ExceptionHandler->register();
            $ExceptionHandler->unregister();
        }

        /**
         * @covers Brickoo\Error\ExceptionHandler::unregister
         * @covers Brickoo\Error\Exceptions\HandlerNotRegistered
         * @expectedException Brickoo\Error\Exceptions\HandlerNotRegistered
         */
        public function testUnregisterNotregisteredHandlerThrowsException() {
            $ExceptionHandler = new ExceptionHandler(true);
            $ExceptionHandler->unregister();
        }

        /**
         * @covers Brickoo\Error\ExceptionHandler::handleException
         * @covers Brickoo\Error\ExceptionHandler::getExceptionMessage
         */
        public function testHandleExceptionDoesNotForwardToOutput() {
            $ExceptionHandler = new ExceptionHandler(false);
            $this->assertNull($ExceptionHandler->handleException(new \Exception('message', 123)));
        }

        /**
         * @covers Brickoo\Error\ExceptionHandler::handleException
         * @covers Brickoo\Error\ExceptionHandler::getExceptionMessage
         */
        public function testHandleErrorWithEventExecution() {
            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $EventManager->expects($this->once())
                         ->method('notify')
                         ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Event'))
                         ->will($this->returnValue(null));

            $ExceptionHandler = new ExceptionHandler(false);
            $ExceptionHandler->setEventManager($EventManager);
            $ExceptionHandler->handleException(new \Exception("test case exception throwed", 123));
        }

        /**
         * @covers Brickoo\Error\ExceptionHandler::handleException
         * @covers Brickoo\Error\ExceptionHandler::getExceptionMessage
         * @expectedException Brickoo\Error\Exceptions\ErrorOccurred
         * @expectedExceptionMessage some exception message
         */
        public function testDisplayException() {
            $ExceptionHandler = new ExceptionHandler(true);
            $ExceptionHandler->register();
            $this->assertNull($ExceptionHandler->handleException(
                new \Brickoo\Error\Exceptions\ErrorOccurred('some exception message', 123)
            ));
        }

        /**
         * @covers Brickoo\Error\ExceptionHandler::__destruct
         */
        public function testDestructorUnregister() {
            $ExceptionHandler = new ExceptionHandler();
            $ExceptionHandler->register();
            $ExceptionHandler->__destruct();
            $this->assertAttributeEquals(false, 'isRegistered', $ExceptionHandler);
        }

    }