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
