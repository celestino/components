<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
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

namespace Brickoo\Tests\Log;

use Brickoo\Log\SyslogLogger,
    PHPUnit_Framework_TestCase;

/**
 * SyslogLoggerTest
 *
 * Test suite for the SyslogLogger class.
 * @see Brickoo\Log\SyslogLogger
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class SyslogLoggerTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Log\SyslogLogger::__construct
     * @covers Brickoo\Log\SyslogLogger::log
     * @covers Brickoo\Log\SyslogLogger::sendMessages
     * @covers Brickoo\Log\SyslogLogger::getMessageHeader
     */
    public function testLog() {
        $hostname = "localhost";

        $logMessage = "Message to log.";
        $expectedRegexMessage = "~^\<[0-9]+\>[0-9]{4}\-[0-9]{2}\-[0-9]{2}T[0-9]{2}\:[0-9]{2}\:[0-9]{2}\+[0-9]{2}\:[0-9]{2} ". $hostname ." ". $logMessage ."$~";

        $networkClient = $this->getNetworkClientStub();
        $networkClient->expects($this->once())
                     ->method("open")
                     ->will($this->returnSelf());
        $networkClient->expects($this->once())
                     ->method("write")
                     ->with($this->matchesRegularExpression($expectedRegexMessage));
        $networkClient->expects($this->once())
                     ->method("close");

        $syslogLogger = new SyslogLogger($networkClient, $hostname);
        $this->assertNull($syslogLogger->log($logMessage, SyslogLogger::SEVERITY_INFO));
    }

    /**
     * @covers Brickoo\Log\SyslogLogger::log
     * @expectedException InvalidArgumentException
     */
    public function testLogInvalidSeverityThrowsArgumentException() {
        $syslogLogger = new SyslogLogger($this->getNetworkClientStub(), "localhost", "someServer.com");
        $syslogLogger->log("message", "wrongType");
    }

    /**
     * Returns a network client stub.
     * @return \Brickoo\Network\Client
     */
    private function getNetworkClientStub() {
        return $this->getMockBuilder("\\Brickoo\\Network\\Client")
            ->disableOriginalConstructor()
            ->getMock();
    }

}