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
     * AbstractHandlerTest
     *
     * Test suite for the AbstractHandler class.
     * @see Brickoo\Library\Error\AbstractHandler
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class AbstractHandlerTest extends \PHPUnit_Framework_TestCase
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
         * Holds an instance of the AbstractHandler class.
         * @var AbstractHandler
         */
        protected $AbstractHandler;

        /**
         * Sets up the AbstractHandler stub for the test cases.
         * @return void
         */
        protected function setUp()
        {
            $this->AbstractHandler = $this->getMockForAbstractClass('Brickoo\Library\Error\AbstractHandler');
        }

        /**
         * Test if the Logger dependency can be injected and the AbstractHandler reference is returned.
         * @covers Brickoo\Library\Error\AbstractHandler::Logger
         */
        public function testLoggerInjection()
        {
            $LoggerStub = $this->getLoggerStub();
            $this->assertSame($this->AbstractHandler, $this->AbstractHandler->Logger($LoggerStub));
            $this->assertAttributeSame($LoggerStub, '_Logger', $this->AbstractHandler);
        }

        /**
         * Test if the Logger dependency can be retrieved.
         * @covers Brickoo\Library\Error\AbstractHandler::Logger
         */
        public function testGetLogger()
        {
            $this->AbstractHandler->Logger(($Logger = $this->getLoggerStub()));
            $this->assertSame($Logger, $this->AbstractHandler->Logger());;
        }

        /**
         * Test if trying to retrive the not avilable Logger dependency throws an exception.
         * @covers Brickoo\Library\Error\AbstractHandler::Logger
         * @covers Brickoo\Library\Core\Exceptions\DependencyNotAvailableException::__construct
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyNotAvailableException
         */
        public function testGetLoggerDependencyException()
        {
            $this->AbstractHandler->Logger();
        }

        /**
         * Test if the Logger dependency can be removed and the object reference is returned.
         * @covers Brickoo\Library\Error\AbstractHandler::removeLogger
         */
        public function testRemoveLogger()
        {
            $this->AbstractHandler->Logger($this->getLoggerStub());
            $this->assertSame($this->AbstractHandler, $this->AbstractHandler->removeLogger());
            $this->assertAttributeEmpty('_Logger', $this->AbstractHandler);
        }

        /**
         * Test if trying to remove a not available Logger dependency throws an exception.
         * @covers Brickoo\Library\Error\AbstractHandler::removeLogger
         * @covers Brickoo\Library\Core\Exceptions\DependencyNotAvailableException::__construct
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyNotAvailableException
         */
        public function testRemoveLoggerDependencyException()
        {
            $this->AbstractHandler->removeLogger();
        }

        /**
         * Test if a Logger is available it is recognized.
         * @covers Brickoo\Library\Error\AbstractHandler::hasLogger
         */
        public function testHasLogger()
        {
            $this->assertFalse($this->AbstractHandler->hasLogger());

            $this->AbstractHandler->Logger($this->getLoggerStub());

            $this->assertTrue($this->AbstractHandler->hasLogger());
        }

    }
