<?php

    /*
     * Copyright (c) 2008-2011, Celestino Diaz Teran <celestino@users.sourceforge.net>.
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
     * Test case for the ExceptionHandler class.
     * @see Brickoo\Library\Error\ExceptionHandler
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id$
     */

    class ExceptionHandlerTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Returns an LogHandler Stub for testing the logging of messages.
         * @return object implementing the Brickoo\Library\Log\Interfaces\LoggerInterface
         */
        protected function getLoggerStub()
        {
            $LogHandlerStub = $this->getMock
            (
                'Brickoo\Library\Log\Interfaces\LoggerInterface',
                array('log')
            );

            return $LogHandlerStub;
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
         * Test if the clearing the instance unregisters the error handler.
         * @covers Brickoo\Library\Error\ExceptionHandler::clear
         */
        public function testClear()
        {
            $ExceptionHandlerStub = $this->getMock('Brickoo\Library\Error\ExceptionHandler', array('unregister'));
            $ExceptionHandlerStub->expects($this->once())
                             ->method('unregister')
                             ->will($this->returnSelf());
            $this->assertSame($ExceptionHandlerStub, $ExceptionHandlerStub->register());
            $this->assertTrue($ExceptionHandlerStub->isRegistered());
            $this->assertSame($ExceptionHandlerStub, $ExceptionHandlerStub->clear());
        }

        /**
         * Test if the class can be created.
         * @covers Brickoo\Library\Error\ExceptionHandler::__construct
         * @covers Brickoo\Library\Error\ExceptionHandler::clear
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
         * @covers Brickoo\Library\Error\ExceptionHandler::isRegistered
         */
        public function testIsRegistered()
        {
            $this->assertFalse($this->ExceptionHandler->isRegistered());
            $this->assertSame($this->ExceptionHandler, $this->ExceptionHandler->register());
            $this->assertTrue($this->ExceptionHandler->isRegistered());
            $this->assertSame($this->ExceptionHandler, $this->ExceptionHandler->unregister());
            $this->assertFalse($this->ExceptionHandler->isRegistered());
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
            $this->assertSame($this->ExceptionHandler, $this->ExceptionHandler->register());
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
         * Test if the log handler can be checked of availability.
         * @covers Brickoo\Library\Error\ExceptionHandler::hasLogger
         */
        public function testHasLogHandler()
        {
            $LogHandlerStub = $this->getLoggerStub();

            $this->assertFalse($this->ExceptionHandler->hasLogger());
            $this->assertSame($this->ExceptionHandler, $this->ExceptionHandler->addLogger($LogHandlerStub));
            $this->assertTrue($this->ExceptionHandler->hasLogger());
        }

        /**
         * Test if the log handler can be assigned as dependency.
         * @covers Brickoo\Library\Error\ExceptionHandler::addLogger
         */
        public function testAddLogHandler()
        {
            $LogHandlerStub = $this->getLoggerStub();
            $this->assertSame($this->ExceptionHandler, $this->ExceptionHandler->addLogger($LogHandlerStub));
        }

        /**
         * Test if the trying to override the LogHandler dependecy throws an exception.
         * @covers Brickoo\Library\Error\ExceptionHandler::addLogger
         * @covers Brickoo\Library\Core\Exceptions\DependencyOverrideException
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverrideException
         */
        public function testAddLogHandlerDependencyException()
        {
            $LogHandlerStub = $this->getLoggerStub();
            $this->assertSame($this->ExceptionHandler, $this->ExceptionHandler->addLogger($LogHandlerStub));
            $this->ExceptionHandler->addLogger($LogHandlerStub);
        }

        /**
         * Test if the log handler can be removed.
         * @covers Brickoo\Library\Error\ExceptionHandler::removeLogger
         */
        public function testRemoveLogHandler()
        {
            $LogHandlerStub = $this->getLoggerStub();
            $this->assertSame($this->ExceptionHandler, $this->ExceptionHandler->addLogger($LogHandlerStub));
            $this->assertSame($this->ExceptionHandler, $this->ExceptionHandler->removeLogger());
        }

        /**
         * Test if the trying to remove an not assigend LogHandler dependecy throws an exception.
         * @covers Brickoo\Library\Error\ExceptionHandler::removeLogger
         * @covers Brickoo\Library\Core\Exceptions\DependencyNotAvailableException
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyNotAvailableException
         */
        public function testRemoveLogHandlerDependencyException()
        {
            $this->ExceptionHandler->removeLogger();
        }

        /**
         * Test if the exception returns nothing further.
         * @covers Brickoo\Library\Error\ExceptionHandler::handleException
         */
        public function testHandleException()
        {
            $this->assertNull($this->ExceptionHandler->handleException(new Exception()));
        }

        /**
         * Test if the exception message is passed to the LogHandler.
         * @covers Brickoo\Library\Error\ExceptionHandler::getExceptionMessage
         * @covers Brickoo\Library\Error\ExceptionHandler::handleException
         */
        public function testHandleExceptionWithLogHandler()
        {
            $LogHandlerStub = $this->getLoggerStub();
            $LogHandlerStub->expects($this->any())
                           ->method('log')
                           ->will($this->returnArgument(0));

            $this->assertSame($this->ExceptionHandler, $this->ExceptionHandler->addLogger($LogHandlerStub));
            $this->assertEquals
            (
                '[777]: message throwed in ' . __FILE__ . ' on line 241',
                $this->ExceptionHandler->handleException(new Exception('message', 777))
            );
        }

        /**
         * Test if the exception message is displayed.
         * @covers Brickoo\Library\Error\ExceptionHandler::handleException
         * @covers Brickoo\Library\Error\ExceptionHandler::getExceptionMessage
         * @covers Brickoo\Library\Error\Exceptions\OutputException
         * @expectedException Brickoo\Library\Error\Exceptions\OutputException
         */
        public function testDisplayHandleException()
        {
            $this->ExceptionHandler->displayExceptions = true;
            $this->assertNull($this->ExceptionHandler->handleException(new Exception('message', 777)));
        }

    }

?>