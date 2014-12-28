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

use Brickoo\Component\Http\HttpFormInput;
use Brickoo\Component\Http\HttpFormFile;
use PHPUnit_Framework_TestCase;

/**
 * HttpFormInputTest
 *
 * Test suite for the HttpFormInput class.
 * @see Brickoo\Component\Http\HttpFormInpute
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpFormInputTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\HttpFormInput::__construct
     * @covers Brickoo\Component\Http\HttpFormInput::hasField
     * @covers Brickoo\Component\Http\HttpFormInput::getField
     */
    public function testFormFieldsCanBeRetrieved() {
        $httpFormInput = new HttpFormInput(["page" => 10]);
        $this->assertFalse($httpFormInput->hasField("unset"));
        $this->assertTrue($httpFormInput->hasField("page"));
        $this->assertEquals(10, $httpFormInput->getField("page"));
    }

    /**
     * @covers Brickoo\Component\Http\HttpFormInput::hasField
     * @covers Brickoo\Component\Http\HttpFormInput::getField
     */
    public function testUnsetFormFieldReturnDefaultValue() {
        $httpFormInput = new HttpFormInput();
        $this->assertEquals("defaultValue", $httpFormInput->getField("unset", "defaultValue"));
    }

    /** @covers Brickoo\Component\Http\HttpFormInput::isEmpty */
    public function testFormEmptyStatus() {
        $httpFormInput = new HttpFormInput();
        $this->assertTrue($httpFormInput->isEmpty());
        $httpFormInput = new HttpFormInput(["page" => 10]);
        $this->assertFalse($httpFormInput->isEmpty());
    }

    /**
     * @covers Brickoo\Component\Http\HttpFormInput::hasFile
     * @covers Brickoo\Component\Http\HttpFormInput::getFile
     */
    public function testFileFieldsCanBeRetrieved() {
        $httpFormFile = new  HttpFormFile("document.pdf", "/temp/random", 500, UPLOAD_ERR_OK);
        $httpFormInput = new HttpFormInput(["page" => 10, "document" => $httpFormFile]);
        $this->assertFalse($httpFormInput->hasFile("page"));
        $this->assertSame($httpFormFile, $httpFormInput->getFile("document"));
    }

    /**
     * @covers \Brickoo\Component\Http\HttpFormInput::getFile
     * @covers \Brickoo\Component\Http\Exception\HttpFormFieldNotFoundException
     * @expectedException \Brickoo\Component\Http\Exception\HttpFormFieldNotFoundException
     */
    public function testUnknownFileFieldThrowsException() {
        $httpFormInput = new HttpFormInput();
        $httpFormInput->getFile("unknown");
    }

    /**
     * @covers \Brickoo\Component\Http\HttpFormInput::extract
     * @covers \Brickoo\Component\Http\HttpFormInput::hasField
     */
    public function testExtractFieldFromContainer() {
        $httpFormInput = new HttpFormInput(["page" => 10]);
        $this->assertTrue($httpFormInput->hasField("page"));
        $this->assertEquals(10, $httpFormInput->extract("page"));
        $this->assertFalse($httpFormInput->hasField("page"));
    }

    /**
     * @covers \Brickoo\Component\Http\HttpFormInput::extract
     * @covers \Brickoo\Component\Http\Exception\HttpFormFieldNotFoundException
     * @expectedException \Brickoo\Component\Http\Exception\HttpFormFieldNotFoundException
     */
    public function testExtractUnknownFieldThrowsException() {
        $httpFormInput = new HttpFormInput();
        $httpFormInput->extract("unknown");
    }

    /** @covers \Brickoo\Component\Http\HttpFormInput::getIterator */
    public function testRetrieveIterator() {
        $httpFormInput = new HttpFormInput([["page" => 10]]);
        $this->assertInstanceOf("\\Iterator", $httpFormInput->getIterator());
    }

}
