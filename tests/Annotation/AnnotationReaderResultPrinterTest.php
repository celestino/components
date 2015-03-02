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

namespace Brickoo\Tests\Component\Annotation;

use Brickoo\Component\Annotation\AnnotationReaderResultPrinter;
use PHPUnit_Framework_TestCase;

/**
 * Test suite for the AnnotationReaderResultPrinter class.
 * @see Brickoo\Component\Annotation\AnnotationReaderResultPrinter
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationReaderResultPrinterTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultPrinter::__construct
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultPrinter::setPrinter
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultPrinter::getPrinter
     */
    public function testSetAndGetSamePrinter() {
        $innerPrinter = $this->getInnerPrinterStub();
        $annotationPrinter = new AnnotationReaderResultPrinter($this->getAnnotationReaderResultStub());
        $this->assertInstanceOf("\\Brickoo\\Component\\IO\\Printing\\Printable", $annotationPrinter);
        $this->assertSame($annotationPrinter, $annotationPrinter->setPrinter($innerPrinter));
        $this->assertSame($innerPrinter, $annotationPrinter->getPrinter());
    }

    /** @covers Brickoo\Component\Annotation\AnnotationReaderResultPrinter::getPrinter */
    public function testLazyPrinterInitialization() {
        $annotationPrinter = new AnnotationReaderResultPrinter($this->getAnnotationReaderResultStub());
        $this->assertInstanceOf("\\Brickoo\\Component\\IO\\Printing\\Printer", $annotationPrinter->getPrinter());
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultPrinter::runPrinter
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultPrinter::printAnnotationsByTarget
     */
    public function testRunPrinter() {
        $readerResultFixture = include __DIR__."/Assets/ReaderResultFixture.php";
        $annotationPrinter = new AnnotationReaderResultPrinter($readerResultFixture);
        $annotationPrinter->setPrinter($this->getInnerPrinterStub());
        $this->assertSame($annotationPrinter, $annotationPrinter->runPrinter());
    }

    /**
     * Returns an AnnotationReaderResult stub.
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getAnnotationReaderResultStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Annotation\\AnnotationReaderResult")
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * Return an inner printer stub.
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getInnerPrinterStub() {
        $printer = $this->getMock("\\Brickoo\\Component\\IO\\Printing\\Printer");
        $printer->expects($this->any())
                ->method("nextLine")
                ->will($this->returnSelf());
        $printer->expects($this->any())
                ->method("indent")
                ->will($this->returnSelf());
        $printer->expects($this->any())
                ->method("outdent")
                ->will($this->returnSelf());
        $printer->expects($this->any())
                ->method("addText")
                ->will($this->returnSelf());
        $printer->expects($this->any())
                ->method("doPrint")
                ->will($this->returnSelf());
        return $printer;

    }

}
