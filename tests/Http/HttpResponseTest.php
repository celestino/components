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
