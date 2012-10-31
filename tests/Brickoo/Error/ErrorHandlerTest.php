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

    use Brickoo\Error\ErrorHandler;

    /**
     * ErrorHandlerTest
     *
     * Test suite for the ErrorHandler class.
     * @see Brickoo\Error\ErrorHandler
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ErrorHandlerTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Error\ErrorHandler::__construct
         */
        public function testErrorHandlerConstructor() {
            $ErrorHandler = new ErrorHandler(E_ALL, true);
            $this->assertAttributeEquals(E_ALL, 'errorLevel', $ErrorHandler);
            $this->assertAttributeEquals(true, 'convertToException', $ErrorHandler);
        }

        /**
         * @covers Brickoo\Error\ErrorHandler::setEventManager
         */
        public function testSetEventManager() {
            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $ErrorHandler = new ErrorHandler();
            $ErrorHandler->setEventManager($EventManager);
            $this->assertAttributeSame($EventManager, 'EventManager', $ErrorHandler);
        }

        /**
         * @covers Brickoo\Error\ErrorHandler::register
         * @covers Brickoo\Error\ErrorHandler::isRegistered
         * @covers Brickoo\Error\ErrorHandler::unregister
         */
        public function testRegisterAndUnregisterProcess() {
            $ErrorHandler = new ErrorHandler();
            $this->assertSame($ErrorHandler, $ErrorHandler->register());
            $this->assertAttributeEquals(true, 'isRegistered', $ErrorHandler);
            $this->assertSame($ErrorHandler, $ErrorHandler->unregister());
            $this->assertAttributeEquals(false, 'isRegistered', $ErrorHandler);
        }

        /**
         * @covers Brickoo\Error\ErrorHandler::register
         * @covers Brickoo\Error\Exceptions\DuplicateHandlerRegistration
         * @expectedException Brickoo\Error\Exceptions\DuplicateHandlerRegistration
         */
        public function testRegisterDuplicateRegistrationThrowsException() {
            $ErrorHandler = new ErrorHandler();
            $ErrorHandler->register();
            $ErrorHandler->register();
            $ErrorHandler->unregister();
        }

        /**
         * @covers Brickoo\Error\ErrorHandler::unregister
         * @covers Brickoo\Error\Exceptions\HandlerNotRegistered
         * @expectedException Brickoo\Error\Exceptions\HandlerNotRegistered
         */
        public function testUnregisterNotRegisteredhandlerThrowsException() {
            $ErrorHandler = new ErrorHandler();
            $ErrorHandler->unregister();
        }

        /**
         * @covers Brickoo\Error\ErrorHandler::handleError
         */
        public function testHandleErrorDoesNotingWithoutEventManagerAndConversionToException() {
            $ErrorHandler = new ErrorHandler(0, false);
            $this->assertTrue($ErrorHandler->handleError(777, 'does nothing', 'noFileNeeded', 0));
        }

        /**
         * @covers Brickoo\Error\ErrorHandler::handleError
         */
        public function testHandleErrorWithEventExecution() {
            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $EventManager->expects($this->once())
                         ->method('notify')
                         ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Event'))
                         ->will($this->returnValue(null));

            $ErrorHandler = new ErrorHandler(0, false);
            $ErrorHandler->setEventManager($EventManager);
            $ErrorHandler->handleError(E_ALL, 'message', 'file', 0);
        }

        /**
         * @covers Brickoo\Error\ErrorHandler::handleError
         * @covers Brickoo\Error\Exceptions\ErrorOccurred
         * @expectedException Brickoo\Error\Exceptions\ErrorOccurred
         */
        public function testHandleErrorConvertingToException() {
            $ErrorHandler = new ErrorHandler(E_ALL, true);
            $ErrorHandler->handleError(E_ALL, 'message', 'file', 0);
        }

        /**
         * @covers Brickoo\Error\ErrorHandler::__destruct
         */
        public function testDestructorUnregister() {
            $ErrorHandler = new ErrorHandler();
            $ErrorHandler->register();
            $ErrorHandler->__destruct();
            $this->assertAttributeEquals(false, 'isRegistered', $ErrorHandler);
        }

    }