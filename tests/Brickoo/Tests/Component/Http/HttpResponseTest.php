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

use Brickoo\Component\Http\HttpResponse;
use Brickoo\Component\Http\HttpVersion;
use Brickoo\Component\Http\HttpStatus;
use Brickoo\Component\Http\HttpMessage;
use Brickoo\Component\Http\HttpMessageBody;
use Brickoo\Component\Http\HttpMessageHeader;
use Brickoo\Component\Http\HttpHeaderList;
use Brickoo\Component\Http\Header\GenericHeader;
use PHPUnit_Framework_TestCase;

/**
 * HttpResponseTest
 *
 * Test suite for the HttpResponse class.
 * @see Brickoo\Component\Http\HttpResponse
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpResponseTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\HttpResponse::__construct
     * @covers Brickoo\Component\Http\HttpResponse::getStatus
     */
    public function testGetStatus() {
        $expectedStatus = $this->getHttpStatusStub();
        $httpResponse = new HttpResponse($this->getHttpVersionStub(), $expectedStatus, $this->getHttpMessageStub());
        $this->assertEquals($expectedStatus, $httpResponse->getStatus());
    }

    /** @covers Brickoo\Component\Http\HttpResponse::getVersion */
    public function testGetVersion() {
        $version = $this->getHttpVersionStub();
        $httpResponse = new HttpResponse($version, $this->getHttpStatusStub(), $this->getHttpMessageStub());
        $this->assertSame($version, $httpResponse->getVersion());
    }

    /** @covers Brickoo\Component\Http\HttpResponse::getMessage */
    public function testGetMessage() {
        $message = $this->getHttpMessageStub();
        $httpResponse = new HttpResponse($this->getHttpVersionStub(), $this->getHttpStatusStub(), $message);
        $this->assertSame($message, $httpResponse->getMessage());
    }

    /** @covers Brickoo\Component\Http\HttpResponse::getHeader */
    public function testGetMessageHeader() {
        $header = $this->getHttpMessageStub();
        $message = $this->getHttpMessageStub();
        $message->expects($this->any())
                ->method("getHeader")
                ->will($this->returnValue($header));
        $httpResponse = new HttpResponse($this->getHttpVersionStub(), $this->getHttpStatusStub(), $message);
        $this->assertSame($header, $httpResponse->getHeader());
    }

    /** @covers Brickoo\Component\Http\HttpResponse::getBody */
    public function testGetHttpMessageBody() {
        $body = $this->getHttpMessageStub();
        $message = $this->getHttpMessageStub();
        $message->expects($this->any())
                ->method("getBody")
                ->will($this->returnValue($body));
        $httpResponse = new HttpResponse($this->getHttpVersionStub(), $this->getHttpStatusStub(), $message);
        $this->assertSame($body, $httpResponse->getBody());
    }

    /** @covers Brickoo\Component\Http\HttpResponse::inject */
    public function testInjectDependencies() {
        $httpResponse = new HttpResponse(
            $this->getHttpVersionStub(),
            $this->getHttpStatusStub(),
            $this->getHttpMessageStub()
        );

        $version = new HttpVersion(HttpVersion::HTTP_1_1);
        $status = new HttpStatus(HttpStatus::CODE_OK);
        $message = new HttpMessage(
            new HttpMessageHeader([
                new HttpHeaderList([
                    new GenericHeader("Unit", "TEST")
                ])
            ]),
            new HttpMessageBody("test case content")
        );
        $injectedResponse = new HttpResponse($version, $status, $message);
        $this->assertSame($httpResponse, $httpResponse->inject($injectedResponse));
        $this->assertSame($version, $httpResponse->getVersion());
        $this->assertSame($status, $httpResponse->getStatus());
        $this->assertSame($message, $httpResponse->getMessage());
    }

    /** @covers Brickoo\Component\Http\HttpResponse::toString */
    public function testToString() {
        $expectedOutput  = "HTTP/1.1 200 OK\r\n";
        $expectedOutput .= "Unit: TEST\r\n";
        $expectedOutput .= "\r\ntest case content";

        $version = new HttpVersion(HttpVersion::HTTP_1_1);
        $status = new HttpStatus(HttpStatus::CODE_OK);
        $message = new HttpMessage(
            new HttpMessageHeader([
                new HttpHeaderList([
                    new GenericHeader("Unit", "TEST")
                ])
            ]),
            new HttpMessageBody("test case content")
        );

        $httpResponse = new HttpResponse($version, $status, $message);
        $this->assertEquals($expectedOutput, $httpResponse->toString());
    }

    /**
     * Returns a http status stub.
     * @return \Brickoo\Component\Http\HttpStatus
     */
    private function getHttpStatusStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Http\\HttpStatus")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a http version stub.
     * @return \Brickoo\Component\Http\HttpVersion
     */
    private function getHttpVersionStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Http\\HttpVersion")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a http message stub.
     * @return \Brickoo\Component\Http\HttpMessage
     */
    private function getHttpMessageStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Http\\HttpMessage")
            ->disableOriginalConstructor()
            ->getMock();
    }

}
