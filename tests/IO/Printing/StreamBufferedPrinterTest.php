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
