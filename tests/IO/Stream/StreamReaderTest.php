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
