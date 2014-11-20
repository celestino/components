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
use Brickoo\Component\Http\HttpMessageHeader;
use PHPUnit_Framework_TestCase;

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
     * @covers Brickoo\Component\Http\HttpHeaderNormalizer::normalizeHeaders
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
