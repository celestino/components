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

    use Brickoo\Library\Error\ExceptionHandler;


    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * ExceptionHandlerTest
     *
     * Test suite for the ExceptionHandler class.
     * @see Brickoo\Library\Error\ExceptionHandler
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ExceptionHandlerTest extends PHPUnit_Framework_TestCase
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
                    'getLogHandler',
                    'injectLogHandler',
                    'getDefaultSeverity',
                    'setDefaultSeverity',
                    'log'
                )
            );
        }

        /**
         * Holds the ExceptionHandler instance for the tests.
         * @var Brickoo\Library\Error\ExceptionHandler
         */
        protected $ExceptionHandler;

        /**
         * Setup the ExceptionHandler instance used for the tests.
         * @see PHPUnit_Framework_TestCase::setUp()
         * @return void
         */
        public function setUp()
        {
            $this->ExceptionHandler = new ExceptionHandler();
        }

        /**
         * Test if the class can be created.
         * @covers Brickoo\Library\Error\ExceptionHandler::__construct
         */
        public function testExceptionHandlerConstructor()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Error\ExceptionHandler',
                $this->ExceptionHandler
            );
        }

        /**
         * Test if the error handler can be registered and unregistered.
         * @covers Brickoo\Library\Error\ExceptionHandler::register
         * @covers Brickoo\Library\Error\ExceptionHandler::unregister
         */
        public function testUnRegisterProcess()
        {
            $this->assertSame($this->ExceptionHandler, $this->ExceptionHandler->register());
            $this->assertSame($this->ExceptionHandler, $this->ExceptionHandler->unregister());
        }

        /**
         * Test if the unregistration without being registered before throws an exception.
         * @covers Brickoo\Library\Error\ExceptionHandler::register
         * @covers Brickoo\Library\Error\Exceptions\DuplicateHandlerRegistrationException
         * @expectedException Brickoo\Library\Error\Exceptions\DuplicateHandlerRegistrationException
         */
        public function testRegisterDuplicateRegistrationException()
        {
            $this->ExceptionHandler->register();
            $this->ExceptionHandler->register();
        }

        /**
         * Test if the unregistration without being registered before throws an exception.
         * @covers Brickoo\Library\Error\ExceptionHandler::unregister
         * @covers Brickoo\Library\Error\Exceptions\HandlerNotRegisteredException
         * @expectedException Brickoo\Library\Error\Exceptions\HandlerNotRegisteredException
         */
        public function testUnregisterNotregisteredException()
        {
            $this->ExceptionHandler->unregister();
        }

        /**
         * Test if the error handler can be registered and unregistered.
         * @covers Brickoo\Library\Error\ExceptionHandler::isRegistered
         */
        public function testIsRegistered()
        {
            $this->assertFalse($this->ExceptionHandler->isRegistered());
            $this->ExceptionHandler->register();
            $this->assertTrue($this->ExceptionHandler->isRegistered());
            $this->ExceptionHandler->unregister();
            $this->assertFalse($this->ExceptionHandler->isRegistered());
        }

        /**
         * Test if the exception returns nothing further.
         * @covers Brickoo\Library\Error\ExceptionHandler::handleException
         */
        public function testHandleException()
        {
            $this->assertEquals
            (
                '[123]: message throwed in ' . __FILE__ . ' on line 155',
                $this->ExceptionHandler->handleException(new Exception('message', 123))
            );
        }

        /**
         * Test if the exception message is passed to the Logger.
         * @covers Brickoo\Library\Error\ExceptionHandler::getExceptionMessage
         * @covers Brickoo\Library\Error\ExceptionHandler::handleException
         */
        public function testHandleExceptionWithLogger()
        {
            $LoggerStub = $this->getLoggerStub();
            $LoggerStub->expects($this->any())
                           ->method('log')
                           ->will($this->returnArgument(0));

            $this->ExceptionHandler->injectLogger($LoggerStub);
            $this->assertEquals
            (
                '[777]: message throwed in ' . __FILE__ . ' on line 175',
                $this->ExceptionHandler->handleException(new Exception('message', 777))
            );
        }

        /**
         * Test if the exception message is displayed.
         * @covers Brickoo\Library\Error\ExceptionHandler::handleException
         * @covers Brickoo\Library\Error\ExceptionHandler::getExceptionMessage
         * @expectedException Brickoo\Library\Error\Exceptions\ErrorHandlerException
         * @expectedExceptionMessage some exception message
         */
        public function testDisplayException()
        {
            $this->ExceptionHandler->register()
                                   ->displayExceptions = true;
            $this->assertNull
            (
                $this->ExceptionHandler->handleException
                (
                    new Brickoo\Library\Error\Exceptions\ErrorHandlerException('some exception message')
                )
            );
        }

    }

?>