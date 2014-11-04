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

namespace Brickoo\Tests\Component\IO\Stream;

use Brickoo\Component\IO\Stream\FileStream;
use Brickoo\Component\IO\Stream\FileStreamConfig;
use PHPUnit_Framework_TestCase;

/**
 * FileStreamTest
 *
 * Test suite for the FileStream class.
 * @see Brickoo\Component\IO\Stream\FileStream
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class FileStreamTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\IO\Stream\FileStream::__construct
     * @covers Brickoo\Component\IO\Stream\FileStream::open
     * @covers Brickoo\Component\IO\Stream\FileStream::getConfiguration
     * @covers Brickoo\Component\IO\Stream\FileStream::resolveMode
     */
    public function testOpenFileStream() {
        $fileStream = new FileStream($this->getFileStreamConfigurationFixture());
        $this->assertInternalType("resource", $fileStream->open());
    }

    /**
     * @covers Brickoo\Component\IO\Stream\FileStream::open
     * @covers Brickoo\Component\IO\Stream\FileStream::hasResource
     */
    public function testOpenFileStreamTwiceReturnsSameResource() {
        $fileStream = new FileStream($this->getFileStreamConfigurationFixture());
        $this->assertInternalType("resource", ($resource_1 = $fileStream->open()));
        $this->assertInternalType("resource", ($resource_2 = $fileStream->open()));
        $this->assertSame($resource_1, $resource_2);
    }

    /**
     * @covers Brickoo\Component\IO\Stream\FileStream::open
     * @covers \Brickoo\Component\IO\Stream\Exception\UnableToCreateResourceHandleException
     * @expectedException \Brickoo\Component\IO\Stream\Exception\UnableToCreateResourceHandleException
     */
    public function testOpenFileStreamFailureThrowsException() {
        $fileStream = new FileStream(new FileStreamConfig("/tmp/fileDoesNotExist".time(), FileStream::MODE_READ));
        $fileStream->open();
    }

    /**
     * @covers Brickoo\Component\IO\Stream\FileStream::open
     * @covers Brickoo\Component\IO\Stream\FileStream::resolveMode
     * @covers \Brickoo\Component\IO\Stream\Exception\AccessModeUnknownException
     * @expectedException \Brickoo\Component\IO\Stream\Exception\AccessModeUnknownException
     */
    public function testOpenFileStreamUnknownModeThrowsException() {
        $fileStream = new FileStream(new FileStreamConfig("/tmp/someFile", 666));
        $fileStream->open();
    }

    /**
     * @covers Brickoo\Component\IO\Stream\FileStream::close
     * @covers Brickoo\Component\IO\Stream\FileStream::open
     * @covers Brickoo\Component\IO\Stream\FileStream::hasResource
     */
    public function testOpenAndCloseFileStream() {
        $fileStream = new FileStream($this->getFileStreamConfigurationFixture());
        $this->assertInternalType("resource", $fileStream->open());
        $fileStream->close();
        $this->assertAttributeEquals(null, "resource", $fileStream);
    }

    /**
     * @covers Brickoo\Component\IO\Stream\FileStream::close
     * @covers Brickoo\Component\IO\Stream\FileStream::open
     * @covers Brickoo\Component\IO\Stream\FileStream::__destruct
     */
    public function testDestructionClosesFileStream() {
        $fileStream = new FileStream($this->getFileStreamConfigurationFixture());
        $this->assertInternalType("resource", $fileStream->open());
        unset($fileStream);
        $fileStream = null;
    }

    /** @covers Brickoo\Component\IO\Stream\FileStream::reconfigure */
    public function testFileStreamReconfiguration() {
        $config_1 = $this->getFileStreamConfigurationFixture();
        $config_2 = $this->getFileStreamConfigurationFixture();
        $fileStream = new FileStream($config_1);
        $this->assertSame($config_1, $fileStream->getConfiguration());
        $this->assertSame($fileStream, $fileStream->reconfigure($config_2));
        $this->assertSame($config_2, $fileStream->getConfiguration());
    }

    /**
     * Returns a file stream configuration fixture.
     * @param array $context
     * @return FileStreamConfig
     */
    private function getFileStreamConfigurationFixture() {
        return  new FileStreamConfig("php://memory", FileStream::MODE_READ);
    }

}
