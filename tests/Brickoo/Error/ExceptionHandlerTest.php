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
            $EventManager = $this->getEventManagerStub();
            $ExceptionHandler = new ExceptionHandler($EventManager);
            $this->assertAttributeSame($EventManager, 'EventManager', $ExceptionHandler);
        }

        /**
         * @covers Brickoo\Error\ExceptionHandler::register
         * @covers Brickoo\Error\ExceptionHandler::isRegistered
         * @covers Brickoo\Error\ExceptionHandler::unregister
         */
        public function testRegisterAndUnregisterProcess() {
            $ExceptionHandler = new ExceptionHandler($this->getEventManagerStub());
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
            $ExceptionHandler = new ExceptionHandler($this->getEventManagerStub());
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
            $ExceptionHandler = new ExceptionHandler($this->getEventManagerStub());
            $ExceptionHandler->unregister();
        }

        /**
         * @covers Brickoo\Error\ExceptionHandler::handleException
         */
        public function testHandleExceptionExecutesEventNotification() {
            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $EventManager->expects($this->once())
                         ->method('notify')
                         ->with($this->isInstanceOf('Brickoo\Error\Event\Interfaces\ExceptionEvent'))
                         ->will($this->returnValue(null));

            $ExceptionHandler = new ExceptionHandler($EventManager);
            $ExceptionHandler->handleException(new \Exception("test case exception throwed", 123));
        }

        /**
         * @covers Brickoo\Error\ExceptionHandler::__destruct
         */
        public function testDestructorUnregister() {
            $ExceptionHandler = new ExceptionHandler($this->getEventManagerStub());
            $ExceptionHandler->register();
            $ExceptionHandler->__destruct();
            $this->assertAttributeEquals(false, 'isRegistered', $ExceptionHandler);
        }

        /**
         * Returns an event manager stub.
         * @return \Brickoo\Event\Interfaces\Manager
         */
        private function getEventManagerStub() {
            return $this->getMock('Brickoo\Event\Interfaces\Manager');
        }

    }