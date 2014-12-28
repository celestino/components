<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Tests\Component\Log;

use Brickoo\Component\IO\Stream\SocketStream;
use Brickoo\Component\IO\Stream\SocketStreamConfig;
use Brickoo\Component\Log\SyslogLogger;
use PHPUnit_Framework_TestCase;

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
