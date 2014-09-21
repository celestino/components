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
