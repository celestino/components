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

use Brickoo\Component\IO\Stream\StreamReader;
use PHPUnit_Framework_TestCase;

/**
 * StreamReaderTest
 *
 * Test suite for the StreamReader class.
 * @see Brickoo\Component\IO\Stream\StreamReader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class StreamReaderTest extends PHPUnit_Framework_TestCase {

    /** @var resource */
    private $streamResource;

    /** {@inheritdoc} */
    public function setUp() {
        $this->streamResource = fopen("php://memory", "rb+");
        fwrite($this->streamResource, "row 1\r\nrow 2\r\nrow 3\r\n");
        rewind($this->streamResource);
    }

    /** {@inheritdoc} */
    public function tearDown() {
        if (is_resource($this->streamResource)) {
            fclose($this->streamResource);
            $this->streamResource = null;
        }
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamReader::__construct
     * @covers Brickoo\Component\IO\Stream\StreamReader::read
     */
    public function testStreamReaderReadsBytes() {
        $expectedContent = "row 1\r\nrow 2\r\n";
        $streamReader = new StreamReader($this->streamResource);
        $this->assertEquals($expectedContent, $streamReader->read(strlen($expectedContent)));
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamReader::read
     * @covers \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @expectedException \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     */
    public function testStreamReaderReadWithInvalidResourceThrowsException() {
        $streamReader = new StreamReader($this->streamResource);
        fclose($this->streamResource);
        $streamReader->read();
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamReader::__construct
     * @covers Brickoo\Component\IO\Stream\StreamReader::readLine
     */
    public function testStreamReaderReadsLines() {
        $streamReader = new StreamReader($this->streamResource);
        $this->assertEquals("row 1\r\n", $streamReader->readLine());
        $this->assertEquals("row 2\r\n", $streamReader->readLine());
        $this->assertEquals("row 3\r\n", $streamReader->readLine());
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamReader::readLine
     * @covers \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @expectedException \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     */
    public function testStreamReaderReadLineWithInvalidResourceThrowsException() {
        $streamReader = new StreamReader($this->streamResource);
        fclose($this->streamResource);
        $streamReader->readLine();
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamReader::readLine
     * @covers \Brickoo\Component\IO\Stream\Exception\UnableToReadBytesException
     * @expectedException \Brickoo\Component\IO\Stream\Exception\UnableToReadBytesException
     */
    public function testStreamReaderReadLineUnableToReadBytesThrowsException() {
        $streamReader = new StreamReader($this->streamResource);
        fseek($this->streamResource, 0, SEEK_END);
        $streamReader->readLine();
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamReader::__construct
     * @covers Brickoo\Component\IO\Stream\StreamReader::readFile
     */
    public function testStreamReaderReadsFile() {
        $expectedContent = "row 1\r\nrow 2\r\nrow 3\r\n";
        $streamReader = new StreamReader($this->streamResource);
        $this->assertEquals($expectedContent, $streamReader->readFile());
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamReader::readFile
     * @covers \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @expectedException \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     */
    public function testStreamReaderReadFileWithInvalidResourceThrowsException() {
        $streamReader = new StreamReader($this->streamResource);
        fclose($this->streamResource);
        $streamReader->readFile();
    }

}
