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

use Brickoo\Component\IO\Stream\StreamWriter;
use PHPUnit_Framework_TestCase;

/**
 * StreamWriterTest
 *
 * Test suite for the StreamWriter class.
 * @see Brickoo\Component\IO\Stream\StreamWriter
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class StreamWriterTest extends PHPUnit_Framework_TestCase {

    /** @var resource */
    private $streamResource;

    /** {@inheritdoc} */
    public function setUp() {
        $this->streamResource = fopen("php://memory", "rb+");
    }

    /** {@inheritdoc} */
    public function tearDown() {
        if (is_resource($this->streamResource)) {
            fclose($this->streamResource);
            $this->streamResource = null;
        }
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamWriter::__construct
     * @covers Brickoo\Component\IO\Stream\StreamWriter::write
     * @covers Brickoo\Component\IO\Stream\StreamWriter::writeWithRetryLoop
     */
    public function testStreamWriterWrites() {
        $expectedContent = "row 1\r\nrow 2\r\n";
        $streamWriter = new StreamWriter($this->streamResource);
        $this->assertSame($streamWriter, $streamWriter->write($expectedContent));
        rewind($this->streamResource);
        $this->assertEquals($expectedContent, fread($this->streamResource, strlen($expectedContent)));
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamWriter::__construct
     * @covers Brickoo\Component\IO\Stream\StreamWriter::write
     * @covers \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @expectedException \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     */
    public function testStreamWriterInvalidHandleThrowsException() {
        $streamWriter = new StreamWriter($this->streamResource);
        fclose($this->streamResource);
        $streamWriter->write("some content");
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamWriter::__construct
     * @covers Brickoo\Component\IO\Stream\StreamWriter::write
     * @covers Brickoo\Component\IO\Stream\StreamWriter::writeWithRetryLoop
     * @covers \Brickoo\Component\IO\Stream\Exception\UnableToWriteBytesException
     * @expectedException \Brickoo\Component\IO\Stream\Exception\UnableToWriteBytesException
     */
    public function testStreamWriterUnableToWriteBytesThrowsException() {
        $filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "streamWriterTest";
        file_put_contents($filename, "");
        $resource = fopen($filename, "r");
        $streamWriter = new StreamWriter($resource);
        $streamWriter->write("some content");
        fclose($resource);
        unlink($filename);
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamWriter::refreshResource
     * @covers Brickoo\Component\IO\Stream\StreamWriter::write
     */
    public function testResourceCanBeRefreshed() {
        $filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "streamWriterTest";
        $streamWriter = new StreamWriter(fopen($filename, "rb+"));
        $resource = fopen("php://memory", "rb+");
        $streamWriter->refreshResource($resource);
        $streamWriter->write("test case");
        fseek($resource, 0);
        $this->assertEquals("test case", fgets($resource));
        fclose($resource);
    }

}
