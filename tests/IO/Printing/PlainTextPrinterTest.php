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

use Brickoo\Component\IO\Printing\PlainTextPrinter;
use PHPUnit_Framework_TestCase;

/**
 * PlainTextPrinterTestTest
 *
 * Test suite for the PlainTextPrinter class.
 * @see Brickoo\Component\IO\Printing\PlainTextPrinter
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class PlainTextPrinterTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidIndentModeThrowsInvalidArgumentException() {
        new PlainTextPrinter($this->getOutputPrinterStub(), ["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidEOLSeparatorThrowsInvalidArgumentException() {
        new PlainTextPrinter($this->getOutputPrinterStub(), PlainTextPrinter::INDENT_SPACES, ["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::__construct
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::nextLine
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::addText
     */
    public function testNextLinePrintsBufferedTextAndAddsNewLineToTheOutputPrinter() {
        $text = "test case output";
        $lineSeparator = "EOL";

        $outputPrinter = $this->getOutputPrinterStub();
        $outputPrinter->expects($this->any())
                      ->method("render")
                      ->withConsecutive([$text], [$lineSeparator])
                      ->will($this->returnSelf());

        $printer = new PlainTextPrinter($outputPrinter, PlainTextPrinter::INDENT_SPACES, $lineSeparator);
        $printer->addText($text);
        $printer->nextLine();
    }

    /**
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::__construct
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::indent
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::addText
     */
    public function testAppendedTextIsIndented() {
        $text = "test case output";

        $outputPrinter = $this->getOutputPrinterStub();
        $outputPrinter->expects($this->any())
                      ->method("render")
                      ->with(sprintf("\t\t%s\t%s", $text, $text))
                      ->will($this->returnSelf());

        $printer = new PlainTextPrinter($outputPrinter);
        $printer->indent(1)
                ->indent(1)
                ->addText($text)
                ->indent(1)
                ->addText($text)
                ->doPrint();
    }

    /**
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::nextLine
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::indent
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::addText
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::outdent
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::doPrint
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::getOutputPrinter
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::hasBufferedText
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::clearTextBuffer
     * @covers Brickoo\Component\IO\Printing\PlainTextPrinter::getIndentation
     */
    public function testPrinterRoundTrip() {
        $text = "test case output";
        $lineSeparator = ";";
        $indentation = 2;
        $firstExpectedOutput = str_repeat(PlainTextPrinter::INDENT_SPACES, (4 * $indentation)).$text;
        $lastExpectedOutput = str_repeat(PlainTextPrinter::INDENT_SPACES, (4 * 1)).$text;

        $outputPrinter = $this->getOutputPrinterStub();
        $outputPrinter->expects($this->any())
                      ->method("render")
                      ->withConsecutive([$firstExpectedOutput], [$lineSeparator], [$lastExpectedOutput])
                      ->will($this->returnSelf());

        $printer = new PlainTextPrinter($outputPrinter, PlainTextPrinter::INDENT_SPACES, $lineSeparator);
        $printer->indent($indentation)
                ->addText($text)
                ->nextLine()
                ->outdent(1)
                ->addText($text)
                ->doPrint();
    }

    /**
     * Return OutputPrinter stub.
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getOutputPrinterStub() {
        return $this->getMock("\\Brickoo\\Component\\IO\\Printing\\OutputPrinter");
    }

}
