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

use Brickoo\Component\IO\Stream\StreamWriter,
    PHPUnit_Framework_TestCase;

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
