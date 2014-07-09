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

use Brickoo\Component\Http\Header\GenericHeader,
    Brickoo\Component\Http\HttpMessageHeader,
    PHPUnit_Framework_TestCase;

/**
 * HttpMessageHeaderTest
 *
 * Test suite for the HttpMessageHeader class.
 * @see Brickoo\Component\Http\HttpMessageHeader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpMessageHeaderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\HttpMessageHeader::__construct
     * @covers Brickoo\Component\Http\HttpMessageHeader::addHeader
     * @covers Brickoo\Component\Http\HttpMessageHeader::getHeader
     * @covers Brickoo\Component\Http\HttpMessageHeader::getHeaderList
     * @covers Brickoo\Component\Http\HttpMessageHeader::contains
     */
    public function testAddAndGetHeader() {
        $header = new GenericHeader("Host", "brickoo.com");
        $messageHeader = new HttpMessageHeader();
        $this->assertSame($messageHeader, $messageHeader->addHeader($header));
        $this->assertSame($header, $messageHeader->getHeader("Host"));
    }

    /**
     * @covers Brickoo\Component\Http\HttpMessageHeader::getHeader
     * @covers Brickoo\Component\Http\HttpMessageHeader::getHeaderList
     * @expectedException \InvalidArgumentException
     */
    public function testGetHeaderWithInvalidNameThrowsException() {
        $messageHeader = new HttpMessageHeader();
        $messageHeader->getHeader(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Http\HttpMessageHeader::getHeader
     * @covers Brickoo\Component\Http\HttpMessageHeader::getHeaderList
     * @covers Brickoo\Component\Http\Exception\HeaderNotFoundException
     * @expectedException \Brickoo\Component\Http\Exception\HeaderNotFoundException
     */
    public function testGetHeaderWithMissingNameThrowsException() {
        $messageHeader = new HttpMessageHeader();
        $messageHeader->getHeader("Host");
    }

    /**
     * @covers Brickoo\Component\Http\HttpMessageHeader::getHeaderList
     * @covers Brickoo\Component\Http\HttpMessageHeader::contains
     */
    public function testGetHeaderList() {
        $header = new GenericHeader("Host", "brickoo.com");
        $messageHeader = new HttpMessageHeader();
        $messageHeader->addHeader($header);
        $this->assertInstanceOf("\\Brickoo\\Component\\Http\\HttpHeaderList",  $messageHeader->getHeaderList("Host"));
    }

    /**
     * @covers Brickoo\Component\Http\HttpMessageHeader::toString
     * @covers Brickoo\Component\Http\HttpMessageHeader::normalizeHeaders
     *
     */
    public function testToString() {
        $expectedString = "Host: brickoo.com\r\n";
        $header = new GenericHeader("Host", "brickoo.com");
        $messageHeader = new HttpMessageHeader();
        $messageHeader->addHeader($header);
        $this->assertEquals($expectedString, $messageHeader->toString());
    }

}
