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

namespace Brickoo\Tests\Component\IO\Printing;

use Brickoo\Component\IO\Printing\OutputBufferedPrinter;
use PHPUnit_Framework_TestCase;

/**
 * OutputBufferedPrinterTest
 *
 * Test suite for the OutputBufferedPrinter class.
 * @see Brickoo\Component\IO\Printing\OutputBufferedPrinter
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class OutputBufferedPrinterTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\IO\Printing\OutputBufferedPrinter::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidBufferLengthThrowsInvalidArgumentException() {
        new OutputBufferedPrinter("wrongType");
    }

    /**
     * @covers Brickoo\Component\IO\Printing\OutputBufferedPrinter::__construct
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::initializeBuffer
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::doPrint
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::isBufferTurnedOff
     */
    public function testPrintWithoutOutputBuffer() {
        $expectedOutput = "Test case output";
        $printer = new OutputBufferedPrinter(0);
        $printer->doPrint($expectedOutput);
        $this->expectOutputString($expectedOutput);
    }

    /**
     * @covers Brickoo\Component\IO\Printing\OutputBufferedPrinter::__construct
     * @covers Brickoo\Component\IO\Printing\OutputBufferedPrinter::output
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::initializeBuffer
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::doPrint
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::addToBuffer
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::getBuffer
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::isBufferTurnedOff
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::isBufferLessThan
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::sumBufferWith
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::clearBuffer
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::flushBuffer
     */
    public function testPrintWithOutputBuffer() {
        $firstText = "Test case output";
        $lastText = str_repeat(".", 10);
        $expectedOutput = $firstText.$lastText;

        $printer = new OutputBufferedPrinter(strlen($firstText));
        $printer->doPrint($firstText);
        $printer->doPrint($lastText);
        $printer->flushBuffer();
        $this->expectOutputString($expectedOutput);
    }

    /**
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::doPrint
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::flushBuffer
     */
    public function testFlushBufferedContent() {
        $text = "Test case output";
        $printer = new OutputBufferedPrinter(strlen($text));
        $printer->doPrint($text);
        $printer->flushBuffer();
        $this->expectOutputString($text);
    }

    /**
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::doPrint
     * @covers Brickoo\Component\IO\Printing\BufferedPrinter::flushBuffer
     * @covers Brickoo\Component\IO\Printing\OutputBufferedPrinter::__destruct
     */
    public function testBufferedContentIsPrintedOnDestruction() {
        $text = "Test case output";
        $printer = new OutputBufferedPrinter(strlen($text));
        $printer->doPrint($text);
        $this->expectOutputString($text);
    }

}
