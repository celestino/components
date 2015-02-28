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
        $this->assertSame($printer, $printer->nextLine());
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
        $this->assertSame(
            $printer,
            $printer->indent(1)
                ->indent(1)
                ->addText($text)
                ->indent(1)
                ->addText($text)
                ->doPrint()
        );
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
        $this->assertSame(
            $printer,
            $printer->indent($indentation)
                ->addText($text)
                ->nextLine()
                ->outdent(1)
                ->addText($text)
                ->doPrint()
        );
    }

    /**
     * Return OutputPrinter stub.
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getOutputPrinterStub() {
        return $this->getMock("\\Brickoo\\Component\\IO\\Printing\\OutputPrinter");
    }

}
