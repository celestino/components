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

namespace Brickoo\Tests\Component\Log;

use Brickoo\Component\IO\Stream\SocketStream,
    Brickoo\Component\IO\Stream\SocketStreamConfig,
    Brickoo\Component\Log\SyslogLogger,
    PHPUnit_Framework_TestCase;

/**
 * SyslogLoggerTest
 *
 * Test suite for the SyslogLogger class.
 * @see Brickoo\Component\Log\SyslogLogger
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class SyslogLoggerTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Log\SyslogLogger::__construct
     * @covers Brickoo\Component\Log\SyslogLogger::log
     * @covers Brickoo\Component\Log\SyslogLogger::sendMessages
     * @covers Brickoo\Component\Log\SyslogLogger::getMessageHeader
     */
    public function testLog() {
        $resource = fopen("php://memory", "rb+");

        $socketStream = $this->getMockBuilder("\\Brickoo\\Component\\IO\\Stream\\SocketStream")
            ->disableOriginalConstructor()->getMock();
        $socketStream->expects($this->any())
                     ->method("open")
                     ->will($this->returnValue($resource));
        $socketStream->expects($this->any())
                     ->method("close")
                     ->will($this->returnSelf());

        $hostname = "myServer";
        $logMessage = "Message to log.";
        $expectedRegexMessage = "~^\\<[0-9]+\\>[0-9]{4}\\-[0-9]{2}\\-[0-9]{2}T[0-9]{2}\\:[0-9]{2}\\:[0-9]{2}\\+[0-9]{2}\\:[0-9]{2} ".$hostname." ".$logMessage."$~";

        $syslogLogger = new SyslogLogger($socketStream, $hostname);
        $this->assertSame($syslogLogger, $syslogLogger->log($logMessage, SyslogLogger::SEVERITY_INFO));

        rewind($resource);
        $this->assertRegExp($expectedRegexMessage, fgets($resource));
        fclose($resource);
    }

    /**
     * @covers Brickoo\Component\Log\SyslogLogger::log
     * @expectedException \InvalidArgumentException
     */
    public function testLogInvalidSeverityThrowsArgumentException() {
        $syslogLogger = new SyslogLogger(new SocketStream(new SocketStreamConfig("udp://localhost", 514)), "myServer");
        $syslogLogger->log("message", "wrongType");
    }

}
