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
