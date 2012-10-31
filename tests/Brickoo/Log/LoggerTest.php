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

    namespace Tests\Brickoo\Log;

    use Brickoo\Log\Logger;

    /**
     * LoggerTest
     *
     * Test suite for the Logger class.
     * @see Brickoo\Log\Logger
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class LoggerTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Log\Logger::__construct
         */
        public function testConstructor() {
            $Handler = $this->getMock('\Brickoo\Log\Handler\Interfaces\Handler');
            $severityLevel = \Brickoo\Log\Logger::SEVERITY_NOTICE;

            $Logger = new Logger($Handler, $severityLevel);
            $this->assertInstanceOf('Brickoo\Log\Interfaces\Logger', $Logger);
            $this->assertAttributeSame($Handler, 'Handler', $Logger);
            $this->assertAttributeEquals($severityLevel,  'defaultSeverityLevel', $Logger);
        }

        /**
        * @covers Brickoo\Log\Logger::log
        */
        public function testLogOfStringWithourSeverityArgument() {
            $message = "test message to log";
            $defaultSeverityLevel = \Brickoo\Log\Logger::SEVERITY_NOTICE;

            $Handler = $this->getMock('\Brickoo\Log\Handler\Interfaces\Handler');
            $Handler->expects($this->once())
                    ->method('log')
                    ->with(array($message), $defaultSeverityLevel);

            $Logger = new Logger($Handler, $defaultSeverityLevel);
            $this->assertNull($Logger->log($message));
        }

        /**
        * @covers Brickoo\Log\Logger::log
        */
        public function testLogOfArrayWithSeverityArgument() {
            $messages = array("test message 1 to log", "test message 2 to log");
            $severityLevel = \Brickoo\Log\Logger::SEVERITY_DEBUG;

            $Handler = $this->getMock('\Brickoo\Log\Handler\Interfaces\Handler');
            $Handler->expects($this->once())
                    ->method('log')
                    ->with($messages, $severityLevel);

            $Logger = new Logger($Handler);
            $this->assertNull($Logger->log($messages, $severityLevel));
        }

        /**
        * @covers Brickoo\Log\Logger::log
        * @expectedException InvalidArgumentException
        */
        public function testMessagesThrowsArgumentException() {
            $Logger = new Logger($this->getMock('\Brickoo\Log\Handler\Interfaces\Handler'));
            $Logger->log(array(new \stdClass()));
        }

        /**
        * @covers Brickoo\Log\Logger::log
        * @expectedException InvalidArgumentException
        */
        public function testSeverityThrowsArgumentException() {
            $Logger = new Logger($this->getMock('\Brickoo\Log\Handler\Interfaces\Handler'));
            $Logger->Log("test valid message", "wrongType");
        }

    }