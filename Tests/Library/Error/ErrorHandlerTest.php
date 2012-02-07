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

    use Brickoo\Library\Error\ErrorHandler;


    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * ErrorHandlerTest
     *
     * Test suite for the ErrorHandler class.
     * @see Brickoo\Library\Error\ErrorHandler
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ErrorHandlerTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Returns an Logger Stub for testing the logging of messages.
         * @return object Logger implementing the Brickoo\Library\Log\Interfaces\LoggerInterface
         */
        protected function getLoggerStub()
        {
            return $this->getMock
            (
                'Brickoo\Library\Log\Interfaces\LoggerInterface',
                array
                (
                    'LogHandler',
                    'getDefaultSeverity',
                    'setDefaultSeverity',
                    'log'
                )
            );
        }

        /**
         * Holds the ErrorHandler instance for the tests.
         * @var Brickoo\Library\Error\ErrorHandler
         */
        protected $ErrorHandler;

        /**
         * Setup the ErrorHandler instance used for the tests.
         * @see PHPUnit_Framework_TestCase::setUp()
         * @return void
         */
        public function setUp()
        {
            $this->ErrorHandler = new ErrorHandler();
        }

        /**
         * Test if the class can be created.
         * @covers Brickoo\Library\Error\ErrorHandler::__construct
         */
        public function testErrorHandlerConstructor()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Error\ErrorHandler',
                $this->ErrorHandler
            );
        }

        /**
         * Test if the error level can be retrieved.
         * @covers Brickoo\Library\Error\ErrorHandler::getErrorLevel
         */
        public function testGetErrorLevel()
        {
            $this->assertEquals(0, $this->ErrorHandler->getErrorLevel());
        }

        /**
         * Test if the error level can be set and retrieved.
         * @covers Brickoo\Library\Error\ErrorHandler::setErrorLevel
         * @covers Brickoo\Library\Error\ErrorHandler::getErrorLevel
         */
        public function testSetErrorLevel()
        {
            $this->assertSame($this->ErrorHandler, $this->ErrorHandler->setErrorLevel(1));
            $this->assertEquals(1, $this->ErrorHandler->getErrorLevel());
        }

        /**
         * Test if the error level with an wrong type throws an exception.
         * @covers Brickoo\Library\Error\ErrorHandler::setErrorLevel
         * @expectedException InvalidArgumentException
         */
        public function testSetErrorLevelArgumentException()
        {
            $this->ErrorHandler->setErrorLevel('wrongType');
        }

        /**
         * Test if the error handler can be registered and unregistered.
         * @covers Brickoo\Library\Error\ErrorHandler::register
         * @covers Brickoo\Library\Error\ErrorHandler::unregister
         */
        public function testUnRegisterProcess()
        {
            $this->assertSame($this->ErrorHandler, $this->ErrorHandler->register());
            $this->assertSame($this->ErrorHandler, $this->ErrorHandler->unregister());
        }

        /**
         * Test if the unregistration without being registered before throws an exception.
         * @covers Brickoo\Library\Error\ErrorHandler::register
         * @covers Brickoo\Library\Error\Exceptions\DuplicateHandlerRegistrationException
         * @expectedException Brickoo\Library\Error\Exceptions\DuplicateHandlerRegistrationException
         */
        public function testRegisterDuplicateRegistrationException()
        {
            $this->ErrorHandler->register();
            $this->ErrorHandler->register();
        }

        /**
         * Test if the unregistration without being registered before throws an exception.
         * @covers Brickoo\Library\Error\ErrorHandler::unregister
         * @covers Brickoo\Library\Error\Exceptions\HandlerNotRegisteredException
         * @expectedException Brickoo\Library\Error\Exceptions\HandlerNotRegisteredException
         */
        public function testUnregisterNotregisteredException()
        {
            $this->ErrorHandler->unregister();
        }

        /**
         * Test if the error handler return the registered status.
         * @covers Brickoo\Library\Error\ErrorHandler::isRegistered
         */
        public function testIsRegistered()
        {
            $this->assertFalse($this->ErrorHandler->isRegistered());
            $this->ErrorHandler->register();
            $this->assertTrue($this->ErrorHandler->isRegistered());
            $this->ErrorHandler->unregister();
            $this->assertFalse($this->ErrorHandler->isRegistered());
        }

        /**
         * Test if the sending an not catched error level message does nothing.
         * @covers Brickoo\Library\Error\ErrorHandler::handleError
         */
        public function testHandleError()
        {
            $this->assertTrue($this->ErrorHandler->handleError(777, 'does nothing', 'noFileNeeded', 0));
        }

        /**
         * Test if the sending an message with matched error level throws an exception.
         * @covers Brickoo\Library\Error\ErrorHandler::handleError
         * @covers Brickoo\Library\Error\Exceptions\ErrorHandlerException
         * @expectedException Brickoo\Library\Error\Exceptions\ErrorHandlerException
         */
        public function testHandleErrorException()
        {
            $this->ErrorHandler->setErrorLevel(777);
            $this->ErrorHandler->handleError(777, 'message', 'file', 0);
        }

        /**
         * Test if the sending an message with matched error level is passed to the Logger.
         * @covers Brickoo\Library\Error\ErrorHandler::handleError
         */
        public function testHandleErrorWithLogger()
        {
            $LoggerStub = $this->getLoggerStub();
            $LoggerStub->expects($this->any())
                       ->method('log')
                       ->will($this->returnSelf());

            $this->ErrorHandler->Logger($LoggerStub);
            $this->assertTrue($this->ErrorHandler->handleError(777, 'message', 'myFile.php', 123));
        }

    }