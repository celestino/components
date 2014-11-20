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

namespace Brickoo\Tests\Component\Http;

use Brickoo\Component\Http\Header\GenericHeader;
use PHPUnit_Framework_TestCase;

/**
 * GenericHeaderTest
 *
 * Test suite for the GenericHeader class.
 * @see Brickoo\Component\Http\Header\GenericHeader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class GenericHeaderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\Header\GenericHeader::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidHeaderNameThrowsException() {
        new GenericHeader(["wrongType"], "some value");
    }

    /**
     * @covers Brickoo\Component\Http\Header\GenericHeader::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidHeaderValueThrowsException() {
        new GenericHeader("Accept", ["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Http\Header\GenericHeader::__construct
     * @covers Brickoo\Component\Http\Header\GenericHeader::setName
     * @covers Brickoo\Component\Http\Header\GenericHeader::getName
     */
    public function testGetAndSetHeaderName() {
        $headerName = "Accept";
        $genericHeader = new GenericHeader($headerName, "*/*");
        $this->assertEquals($headerName, $genericHeader->getName());
        $genericHeader->setName("Accept-Language");
        $this->assertEquals("Accept-Language", $genericHeader->getName());
    }

    /**
     * @covers Brickoo\Component\Http\Header\GenericHeader::setValue
     * @covers Brickoo\Component\Http\Header\GenericHeader::getValue
     */
    public function testGetAndSetHeaderValue() {
        $headerValue = "*/*";
        $genericHeader = new GenericHeader("Accept", $headerValue);
        $this->assertEquals($headerValue, $genericHeader->getValue());
        $genericHeader->setValue("text/html");
        $this->assertEquals("text/html", $genericHeader->getValue());
    }

    /** @covers Brickoo\Component\Http\Header\GenericHeader::toString */
    public function testToString() {
        $headerName = "Accept";
        $headerValue = "*/*";
        $genericHeader = new GenericHeader($headerName, $headerValue);
        $this->assertEquals($headerName.": ".$headerValue, $genericHeader->toString());
    }

}
