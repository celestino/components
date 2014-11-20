<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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
