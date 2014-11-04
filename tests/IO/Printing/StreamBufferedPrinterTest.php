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

use Brickoo\Component\IO\Printing\StreamBufferedPrinter;
use Brickoo\Component\IO\Stream\FileStream;
use Brickoo\Component\IO\Stream\FileStreamConfig;
use PHPUnit_Framework_TestCase;

/**
 * StreamBufferedPrinterTest
 *
 * Test suite for the StreamBufferedPrinter class.
 * @see Brickoo\Component\IO\Printing\StreamBufferedPrinter
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class StreamBufferedPrinterTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\IO\Printing\StreamBufferedPrinter::__construct
     * @covers Brickoo\Component\IO\Printing\StreamBufferedPrinter::output
     * @covers Brickoo\Component\IO\Printing\StreamBufferedPrinter::getStreamWriter
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::initializeBuffer
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::doPrint
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::flushBuffer
     */
    public function testPrintBufferedContentToStream() {
        $expectedContent = "test case content";

        $stream = new FileStream(new FileStreamConfig("php://memory", FileStream::MODE_READ + FileStream::MODE_WRITE));
        $streamPrinter = new StreamBufferedPrinter($stream);
        $this->assertSame($streamPrinter, $streamPrinter->doPrint($expectedContent));
        $this->assertSame($streamPrinter, $streamPrinter->flushBuffer());
        fseek($stream->open(), 0);
        $this->assertSame($expectedContent, fgets($stream->open()));
        $stream->close();
    }

    /**
     * @covers Brickoo\Component\IO\Printing\StreamBufferedPrinter::__construct
     * @covers Brickoo\Component\IO\Printing\StreamBufferedPrinter::output
     * @covers Brickoo\Component\IO\Printing\StreamBufferedPrinter::getStreamWriter
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::doPrint
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::flushBuffer
     */
    public function testPrintDirectContentToStream() {
        $expectedContent = "test case content";

        $stream = new FileStream(new FileStreamConfig("php://memory", FileStream::MODE_READ + FileStream::MODE_WRITE));
        $streamPrinter = new StreamBufferedPrinter($stream, 0);
        $this->assertSame($streamPrinter, $streamPrinter->doPrint($expectedContent));
        fseek($stream->open(), 0);
        $this->assertSame($expectedContent, fgets($stream->open()));
        $stream->close();
    }

    /**
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::doPrint
     * @covers Brickoo\Component\IO\Printing\StreamBufferedPrinter::getStreamWriter
     */
    public function testStreamWriteResourceIsRefreshed() {
        $stream = new FileStream(new FileStreamConfig("php://memory", FileStream::MODE_READ + FileStream::MODE_WRITE));
        $streamPrinter = new StreamBufferedPrinter($stream, strlen("test"));
        $streamPrinter->doPrint("test");
        $streamPrinter->doPrint("Case");
        $streamPrinter->flushBuffer();
        fseek($stream->open(), 0);
        $this->assertSame("testCase", fgets($stream->open()));
        $stream->close();
    }

}
