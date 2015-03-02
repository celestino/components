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

use Brickoo\Component\Log\FilesystemLogger;
use PHPUnit_Framework_TestCase;

/**
 * FilesystemLoggerTest
 *
 * Test suite for the Filesystem class.
 * @see Brickoo\Component\Log\FilesystemLogger
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class FilesystemLoggerTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Log\FilesystemLogger::__construct
     * @covers \Brickoo\Component\Log\FilesystemLogger::log
     * @covers \Brickoo\Component\Log\FilesystemLogger::convertToLogMessage
     */
    public function testLog() {
        date_default_timezone_set("UTC");

        $logMessage = "Message to log.";
        $expectedFilename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . date("Y-m-d").".log";
        $expectedRegexMessage = "~^\\[[0-9]{4}\\-[0-9]{2}\\-[0-9]{2} [0-9]{2}\\:[0-9]{2}\\:[0-9]{2}\\]\\[[a-zA-Z]+\\] ".$logMessage . PHP_EOL."$~";

        $filesystemLogger = new FilesystemLogger(sys_get_temp_dir());
        $this->assertSame($filesystemLogger, $filesystemLogger->log($logMessage, FilesystemLogger::SEVERITY_DEBUG));
        $this->assertTrue(file_exists($expectedFilename));
        $this->assertRegExp($expectedRegexMessage, file_get_contents($expectedFilename));
        $this->assertTrue(unlink($expectedFilename));
    }

    /**
     * @covers \Brickoo\Component\Log\FilesystemLogger::log
     * @covers \Brickoo\Component\Log\FilesystemLogger::convertToLogMessage
     * @covers \Brickoo\Component\Log\Exception\UnknownSeverityException
     * @expectedException \Brickoo\Component\Log\Exception\UnknownSeverityException
     */
    public function testUnknownSeverityThrowsException() {
        $filesystemLogger = new FilesystemLogger(sys_get_temp_dir());
        $filesystemLogger->log("message", 123);
    }

}
