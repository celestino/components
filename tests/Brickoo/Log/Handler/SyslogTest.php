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

    namespace Tests\Brickoo\Log\Handler;

    use Brickoo\Log\Handler\Syslog;

    /**
     * SyslogTest
     *
     * Test suite for the Syslog class.
     * @see Brickoo\Log\Handler\Syslog
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class SyslogTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Log\Handler\Syslog::__construct
         */
        public function testConstruct() {
            $Client = $this->getMock('Brickoo\Network\Interfaces\Client');
            $hostname = "localhost";
            $serverAddress = "someServer.com";
            $serverPort = 514;
            $timeout = 30;
            $facility = Syslog::FACILITY_LOG;

            $Syslog = new Syslog($Client, $hostname, $serverAddress, $serverPort, $timeout, $facility);
            $this->assertInstanceOf('\Brickoo\Log\Handler\Interfaces\Handler', $Syslog);
            $this->assertAttributeSame($Client, "Client", $Syslog);
            $this->assertAttributeEquals($hostname, "hostname", $Syslog);
            $this->assertAttributeEquals($serverAddress, "serverAddress", $Syslog);
            $this->assertAttributeEquals($serverPort, "serverPort", $Syslog);
            $this->assertAttributeEquals($timeout, "timeout", $Syslog);
            $this->assertAttributeEquals($facility, "facility", $Syslog);
        }

        /**
         * @covers Brickoo\Log\Handler\Syslog::log
         * @covers Brickoo\Log\Handler\Syslog::sendMessages
         * @covers Brickoo\Log\Handler\Syslog::getMessageHeader
         */
        public function testLog() {
            $hostname = "localhost";
            $serverAddress = "someServer.com";
            $serverPort = 1024;
            $timeout = 60;

            $logMessage = "Message to log.";
            $expectedRegexMessage = "~^\<[0-9]+\>[0-9]{4}\-[0-9]{2}\-[0-9]{2}T[0-9]{2}\:[0-9]{2}\:[0-9]{2}\+[0-9]{2}\:[0-9]{2} ". $hostname ." ". $logMessage ."$~";

            $Client = $this->getMock('Brickoo\Network\Interfaces\Client');
            $Client->expects($this->once())
                         ->method('open')
                         ->with("udp://". $serverAddress, $serverPort, $timeout, STREAM_CLIENT_CONNECT, null)
                         ->will($this->returnSelf());
            $Client->expects($this->once())
                         ->method('write')
                         ->with($this->matchesRegularExpression($expectedRegexMessage));
            $Client->expects($this->once())
                         ->method('close');

            $Syslog = new Syslog($Client, $hostname, $serverAddress, $serverPort, $timeout);
            $this->assertNull($Syslog->log($logMessage, Syslog::SEVERITY_INFO));
        }

        /**
         * @covers Brickoo\Log\Handler\Syslog::log
         * @expectedException InvalidArgumentException
         */
        public function testLogThrowsSeverityArgumentException() {
            $Syslog = new Syslog($this->getMock('Brickoo\Network\Interfaces\Client'), "localhost", "someServer.com");
            $Syslog->log("message", "wrongType");
        }

    }