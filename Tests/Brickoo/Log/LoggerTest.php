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

    use Brickoo\Log\Logger;


    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * LoggerTest
     *
     * Test suite for the Logger class.
     * @see Brickoo\Log\Logger
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class LoggerTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Returns an LogHandler Stub for testing the logging of messages.
         * @return \Brickoo\Log\Handler\Interfaces\LogHandlerInterface
         */
        protected function getLogHandlerStub()
        {
            return $this->getMock
            (
                'Brickoo\Log\Handler\Interfaces\LogHandlerInterface',
                array('log')
            );
        }

        /**
         * Holds an instance of the Logger class.
         * @var Brickoo\Log\Logger
         */
        protected $Logger;

        /**
         * Setup the Logger instance used for the tests.
         * @see PHPUnit_Framework_TestCase::setUp()
         * @return void
         */
        public function setUp()
        {
            $this->Logger = new Logger($this->getLogHandlerStub());
        }

        /**
         * Test the constructor of the Logger class.
         * @covers Brickoo\Log\Logger::__construct
         */
        public function testLoggerConstructor()
        {
            $this->assertInstanceOf('Brickoo\Log\Interfaces\LoggerInterface', $this->Logger);
        }

        /**
         * Test if the default severity is returned.
         * @covers Brickoo\Log\Logger::getDefaultSeverity
         */
        public function testGetDefaultSeverity()
        {
            $this->assertEquals(Logger::SEVERITY_INFO, $this->Logger->getDefaultSeverity());
        }

        /**
         * Test if the default severity can be overriden.
         * @covers Brickoo\Log\Logger::setDefaultSeverity
         * @covers Brickoo\Log\Logger::getDefaultSeverity
         */
        public function testSetDefaultSeverity()
        {
            $this->assertSame($this->Logger, $this->Logger->setDefaultSeverity(Logger::SEVERITY_ERROR));
            $this->assertEquals(Logger::SEVERITY_ERROR, $this->Logger->getDefaultSeverity());
        }

        /**
         * Test if trying to set a wrong type of severity throws an exception.
         * @covers Brickoo\Log\Logger::setDefaultSeverity
         * @expectedException InvalidArgumentException
         */
        public function testSetDefaultSeverityArgumentException()
        {
            $this->Logger->setDefaultSeverity('wrongType');
        }

        /**
         * Test if the LogHandler dependency can be retrieved.
         * @covers Brickoo\Log\Logger::LogHandler
         */
        public function testGetLogHandler()
        {
            $LogHandler = $this->getLogHandlerStub();
            $Logger = new Logger($LogHandler);

            $this->assertSame($LogHandler, $Logger->LogHandler());
            $this->assertAttributeSame($LogHandler, '_LogHandler', $Logger);
        }

        /**
        * Test if a string can be logged.
        * @covers Brickoo\Log\Logger::log
        */
        public function testLogOfString()
        {
            $LogHandler = $this->Logger->LogHandler();
            $LogHandler->expects($this->once())
                       ->method('log');

            $this->assertSame($this->Logger, $this->Logger->log('message'));
        }

        /**
        * Test if an array can be logged with severity.
        * @covers Brickoo\Log\Logger::log
        */
        public function testLogOfArrayWithSeverity()
        {
            $LogHandler = $this->Logger->LogHandler();
            $LogHandler->expects($this->once())
                       ->method('log');

            $this->assertSame($this->Logger, $this->Logger->log(array('message1', 'message2'), Logger::SEVERITY_ERROR));
        }

        /**
        * Test if passed a wrong severity argument type throws an exception.
        * @covers Brickoo\Log\Logger::log
        * @expectedException InvalidArgumentException
        */
        public function testSeverityArgumentException()
        {
            $this->Logger->Log('mesage', 'wrongType');
        }

    }