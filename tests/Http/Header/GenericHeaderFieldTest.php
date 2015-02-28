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

namespace Brickoo\Tests\Component\Http;

use Brickoo\Component\Http\Header\GenericHeaderField;
use PHPUnit_Framework_TestCase;

/**
 * GenericHeaderFieldTest
 *
 * Test suite for the GenericHeaderField class.
 * @see Brickoo\Component\Http\Header\GenericHeaderField
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class GenericHeaderFieldTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\Header\GenericHeaderField::__construct
     * @covers Brickoo\Component\Http\Header\GenericHeaderField::setName
     * @covers Brickoo\Component\Http\Header\GenericHeaderField::getName
     */
    public function testGetAndSetHeaderName() {
        $headerFieldName = "Accept";
        $genericHeader = new GenericHeaderField($headerFieldName, "*/*");
        $this->assertEquals($headerFieldName, $genericHeader->getName());
        $genericHeader->setName("Accept-Language");
        $this->assertEquals("Accept-Language", $genericHeader->getName());
    }

    /**
     * @covers Brickoo\Component\Http\Header\GenericHeaderField::setValue
     * @covers Brickoo\Component\Http\Header\GenericHeaderField::getValue
     */
    public function testGetAndSetHeaderValue() {
        $headerFieldValue = "*/*";
        $genericHeader = new GenericHeaderField("Accept", $headerFieldValue);
        $this->assertEquals($headerFieldValue, $genericHeader->getValue());
        $genericHeader->setValue("text/html");
        $this->assertEquals("text/html", $genericHeader->getValue());
    }

    /** @covers Brickoo\Component\Http\Header\GenericHeaderField::toString */
    public function testToString() {
        $headerFieldName = "Accept";
        $headerFieldValue = "*/*";
        $genericHeader = new GenericHeaderField($headerFieldName, $headerFieldValue);
        $this->assertEquals($headerFieldName.": ".$headerFieldValue, $genericHeader->toString());
    }

}
