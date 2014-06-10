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

use Brickoo\Component\Log\FilesystemLogger,
    PHPUnit_Framework_TestCase;

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
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidDirectoryTypeThrowsException() {
        new FilesystemLogger(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Log\FilesystemLogger::__construct
     * @covers \Brickoo\Component\Log\FilesystemLogger::log
     * @covers \Brickoo\Component\Log\FilesystemLogger::convertToLogMessage
     */
    public function testLog() {
        date_default_timezone_set("UTC");

        $logMessage = "Message to log.";
        $expectedFilename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . date("Y-m-d") .".log";
        $expectedRegexMessage = "~^\\[[0-9]{4}\\-[0-9]{2}\\-[0-9]{2} [0-9]{2}\\:[0-9]{2}\\:[0-9]{2}\\]\\[[a-zA-Z]+\\] ". $logMessage . PHP_EOL ."$~";

        $filesystemLogger = new FilesystemLogger(sys_get_temp_dir());
        $this->assertSame($filesystemLogger, $filesystemLogger->log($logMessage, FilesystemLogger::SEVERITY_DEBUG));
        $this->assertTrue(file_exists($expectedFilename));
        $this->assertRegExp($expectedRegexMessage, file_get_contents($expectedFilename));
        $this->assertTrue(unlink($expectedFilename));
    }

    /**
    * @covers \Brickoo\Component\Log\FilesystemLogger::log
    * @expectedException \InvalidArgumentException
    */
    public function testLogInvalidSeverityThrowsException() {
        $filesystemLogger = new FilesystemLogger(sys_get_temp_dir());
        $filesystemLogger->log("message", "wrongType");
    }

}
