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

namespace Brickoo\Tests\Component\IO\Printing;

use Brickoo\Component\IO\Printing\OutputBufferedPrinter,
    PHPUnit_Framework_TestCase;

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
     * @covers Brickoo\Component\IO\Printing\BufferRoutines::initializeBuffer
     * @covers Brickoo\Component\IO\Printing\OutputBufferedPrinter::doPrint
     * @covers Brickoo\Component\IO\Printing\BufferRoutines::isBufferTurnedOff
     */
    public function testPrintWithoutOutputBuffer() {
        $expectedOutput = "Test case output";
        $printer = new OutputBufferedPrinter(0);
        $printer->doPrint($expectedOutput);
        $this->expectOutputString($expectedOutput);
    }

    /**
     * @covers Brickoo\Component\IO\Printing\OutputBufferedPrinter::__construct
     * @covers Brickoo\Component\IO\Printing\BufferRoutines::initializeBuffer
     * @covers Brickoo\Component\IO\Printing\OutputBufferedPrinter::doPrint
     * @covers Brickoo\Component\IO\Printing\BufferRoutines::addToBuffer
     * @covers Brickoo\Component\IO\Printing\BufferRoutines::getBuffer
     * @covers Brickoo\Component\IO\Printing\BufferRoutines::isBufferTurnedOff
     * @covers Brickoo\Component\IO\Printing\BufferRoutines::isBufferLessThan
     * @covers Brickoo\Component\IO\Printing\BufferRoutines::sumBufferWith
     * @covers Brickoo\Component\IO\Printing\BufferRoutines::clearBuffer
     * @covers Brickoo\Component\IO\Printing\OutputBufferedPrinter::output
     * @covers Brickoo\Component\IO\Printing\OutputBufferedPrinter::flushBuffer
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
     * @covers Brickoo\Component\IO\Printing\OutputBufferedPrinter::doPrint
     * @covers Brickoo\Component\IO\Printing\OutputBufferedPrinter::flushBuffer
     */
    public function testFlushBufferedContent() {
        $text = "Test case output";
        $printer = new OutputBufferedPrinter(strlen($text));
        $printer->doPrint($text);
        $printer->flushBuffer();
        $this->expectOutputString($text);
    }

    /** @covers Brickoo\Component\IO\Printing\OutputBufferedPrinter::doPrint */
    public function testBufferedContentIsNotPrinted() {
        $text = "Test case output";
        $printer = new OutputBufferedPrinter(strlen($text));
        $printer->doPrint($text);
        $this->expectOutputString("");
    }

}
