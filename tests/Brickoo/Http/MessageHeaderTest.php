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

namespace Brickoo\Tests\Http;

use Brickoo\Http\MessageHeader,
    PHPUnit_Framework_TestCase;

/**
 * MessageHeaderTest
 *
 * Test suite for the MessageHeader class.
 * @see Brickoo\Http\MessageHeader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageHeaderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Http\MessageHeader::__construct
     * @covers Brickoo\Http\MessageHeader::setHeader
     * @covers Brickoo\Http\MessageHeader::getHeader
     */
    public function testSetAndGetHeader() {
        $header = $this->getHttpHeaderStub();
        $header->expects($this->any())
               ->method("getName")
               ->will($this->returnValue("Host"));
        $messageHeader = new MessageHeader();
        $this->assertSame($messageHeader, $messageHeader->setHeader($header));
        $this->assertSame($header, $messageHeader->getHeader("Host"));
    }

    /**
     * @covers Brickoo\Http\MessageHeader::getHeader
     * @expectedException \InvalidArgumentException
     */
    public function testGetHeaderWithInvalidNameThrowsException() {
        $messageHeader = new MessageHeader();
        $messageHeader->getHeader(["wrongType"]);
    }

    /**
     * @covers Brickoo\Http\MessageHeader::getHeader
     * @covers Brickoo\Http\Exception\HeaderNotFoundException
     * @expectedException Brickoo\Http\Exception\HeaderNotFoundException
     */
    public function testGetHeaderWithMissingNameThrowsException() {
        $messageHeader = new MessageHeader();
        $messageHeader->getHeader("Host");
    }

    /** @covers Brickoo\Http\MessageHeader::hasHeader */
    public function testHasHeader() {
        $messageHeader = new MessageHeader(["Host" => $this->getHttpHeaderStub()]);
        $this->assertTrue($messageHeader->hasHeader("Host"));
        $this->assertFalse($messageHeader->hasHeader("Accept"));
    }

    /** @covers Brickoo\Http\MessageHeader::removeHeader */
    public function testRemoveHeader() {
        $messageHeader = new MessageHeader(["Host" => $this->getHttpHeaderStub()]);
        $this->assertSame($messageHeader, $messageHeader->removeHeader("Host"));
        $this->assertFalse($messageHeader->hasHeader("Host"));
    }

    /**
     * @covers Brickoo\Http\MessageHeader::removeHeader
     * @expectedException \InvalidArgumentException
     */
    public function testRemoveHeaderWithInvalidNameThrowsException() {
        $messageHeader = new MessageHeader();
        $messageHeader->removeHeader(["wrongType"]);
    }

    /**
     * @covers Brickoo\Http\MessageHeader::removeHeader
     * @covers Brickoo\Http\Exception\HeaderNotFoundException
     * @expectedException Brickoo\Http\Exception\HeaderNotFoundException
     */
    public function testRemoveHeaderWithMissingNameThrowsException() {
        $messageHeader = new MessageHeader();
        $messageHeader->removeHeader("Host");
    }

    /**
     * @covers Brickoo\Http\MessageHeader::toString
     * @covers Brickoo\Http\MessageHeader::normalizeHeaders
     *
     */
    public function testToString() {
        $expectedString = "Host: example.com\r\n";
        $header = $this->getHttpHeaderStub();
        $header->expects($this->any())
               ->method("getName")
               ->will($this->returnValue("Host"));
        $header->expects($this->any())
               ->method("getValue")
               ->will($this->returnValue("example.com"));
        $messageHeader = new MessageHeader(["Host" => $header]);
        $this->assertEquals($expectedString, $messageHeader->toString());
    }

    /**
     * @covers Brickoo\Http\MessageHeader::toArray
     * @covers Brickoo\Http\MessageHeader::normalizeHeaders
     *
     */
    public function testToArray() {
        $expectedArray = ["Host" => "example.com"];
        $header = $this->getHttpHeaderStub();
        $header->expects($this->any())
               ->method("getName")
               ->will($this->returnValue("Host"));
        $header->expects($this->any())
               ->method("getValue")
               ->will($this->returnValue("example.com"));
        $messageHeader = new MessageHeader(["Host" => $header]);
        $this->assertEquals($expectedArray, $messageHeader->toArray());
    }

    /**
     * Returns a http header stub.
     * @return \Brickoo\Http\HttpHeader
     */
    private function getHttpHeaderStub() {
        return $this->getMockBuilder("\\Brickoo\\Http\\HttpHeader")
            ->disableOriginalConstructor()
            ->getMock();
    }

}