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

use Brickoo\Http\HttpResponse,
    PHPUnit_Framework_TestCase;

/**
 * HttpResponseTest
 *
 * Test suite for the HttpResponse class.
 * @see Brickoo\Http\HttpResponse
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpResponseTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Http\HttpResponse::__construct
     * @covers Brickoo\Http\HttpResponse::getStatus
     */
    public function testGetStatus() {
        $expectedStatus = $this->getHttpStatusStub();
        $httpResponse = new HttpResponse($expectedStatus, $this->getHttpVersionStub(), $this->getHttpMessageStub());
        $this->assertEquals($expectedStatus, $httpResponse->getStatus());
    }

    /** @covers Brickoo\Http\HttpResponse::getVersion */
    public function testGetVersion() {
        $version = $this->getHttpVersionStub();
        $httpResponse = new HttpResponse($this->getHttpStatusStub(), $version, $this->getHttpMessageStub());
        $this->assertSame($version, $httpResponse->getVersion());
    }

    /** @covers Brickoo\Http\HttpResponse::getMessage */
    public function testGetMessage() {
        $message = $this->getHttpMessageStub();
        $httpResponse = new HttpResponse($this->getHttpStatusStub(), $this->getHttpVersionStub(), $message);
        $this->assertSame($message, $httpResponse->getMessage());
    }

    /** @covers Brickoo\Http\HttpResponse::getHeader */
    public function testGetMessageHeader() {
        $header = $this->getHttpMessageStub();
        $message = $this->getHttpMessageStub();
        $message->expects($this->any())
                ->method("getHeader")
                ->will($this->returnValue($header));
        $httpResponse = new HttpResponse($this->getHttpStatusStub(), $this->getHttpVersionStub(), $message);
        $this->assertSame($header, $httpResponse->getHeader());
    }

    /** @covers Brickoo\Http\HttpResponse::getBody */
    public function testGetMessageBody() {
        $body = $this->getHttpMessageStub();
        $message = $this->getHttpMessageStub();
        $message->expects($this->any())
                ->method("getBody")
                ->will($this->returnValue($body));
        $httpResponse = new HttpResponse($this->getHttpStatusStub(), $this->getHttpVersionStub(), $message);
        $this->assertSame($body, $httpResponse->getBody());
    }

    /** @covers Brickoo\Http\HttpResponse::toString */
    public function testToString() {
        $expectedOutput  = "HTTP/1.1 200 OK\r\n";
        $expectedOutput .= "Unit: TEST\r\n";
        $expectedOutput .= "\r\ntest case content";

        $status = $this->getHttpStatusStub();
        $status->expects($this->any())
               ->method("toString")
               ->will($this->returnValue("200 OK"));

        $version = $this->getHttpVersionStub();
        $version->expects($this->any())
                ->method("toString")
                ->will($this->returnValue("HTTP/1.1"));

        $header = $this->getHttpMessageHeaderStub();
        $header->expects($this->any())
               ->method("toString")
               ->will($this->returnValue("Unit: TEST\r\n"));

        $body = $this->getHttpMessageBodyStub();
        $body->expects($this->any())
             ->method("getContent")
             ->will($this->returnValue("test case content"));

        $message = $this->getHttpMessageStub();
        $message->expects($this->any())
                ->method("getHeader")
                ->will($this->returnValue($header));
        $message->expects($this->any())
                ->method("getBody")
                ->will($this->returnValue($body));

        $httpResponse = new HttpResponse($status, $version, $message);
        $this->assertEquals($expectedOutput, $httpResponse->toString());
    }

    /**
     * Returns a http status stub.
     * @return \Brickoo\Http\HttpStatus
     */
    private function getHttpStatusStub() {
        return $this->getMockBuilder("\\Brickoo\\Http\\HttpStatus")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a http version stub.
     * @return \Brickoo\Http\HttpVersion
     */
    private function getHttpVersionStub() {
        return $this->getMockBuilder("\\Brickoo\\Http\\HttpVersion")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a http message stub.
     * @return \Brickoo\Http\HttpMessage
     */
    private function getHttpMessageStub() {
        return $this->getMockBuilder("\\Brickoo\\Http\\HttpMessage")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a http message header stub.
     * @return \Brickoo\Http\MessageHeader
     */
    private function getHttpMessageHeaderStub() {
        return $this->getMockBuilder("\\Brickoo\\Http\\MessageHeader")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a http message body stub.
     * @return \Brickoo\Http\MessageBody
     */
    private function getHttpMessageBodyStub() {
        return $this->getMockBuilder("\\Brickoo\\Http\\MessageBody")
            ->disableOriginalConstructor()
            ->getMock();
    }

}