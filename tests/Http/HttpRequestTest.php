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

use Brickoo\Component\Http\HttpMethod,
    Brickoo\Component\Http\HttpRequest,
    Brickoo\Component\Http\HttpVersion,
    PHPUnit_Framework_TestCase;

/**
 * HttpRequestTest
 *
 * Test suite for the HttpRequest class.
 * @see Brickoo\Component\Http\HttpRequest
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpRequestTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\HttpRequest::__construct
     * @covers Brickoo\Component\Http\HttpRequest::getUri
     */
    public function testGetUri() {
        $uri = $this->getHttpUriStub();
        $httpRequest = new HttpRequest(
            $this->getHttpMethodStub(),
            $this->getHttpVersionStub(),
            $uri,
            $this->getHttpMessageStub()
        );
        $this->assertSame($uri, $httpRequest->getUri());
    }

    /** @covers Brickoo\Component\Http\HttpRequest::getQuery */
    public function testGetUriQuery() {
        $query = $this->getMock("\\Brickoo\\Component\\Http\\UriQuery");
        $uri = $this->getHttpUriStub();
        $uri->expects($this->once())
            ->method("getQuery")
            ->will($this->returnValue($query));
        $httpRequest = new HttpRequest(
            $this->getHttpMethodStub(),
            $this->getHttpVersionStub(),
            $uri,
            $this->getHttpMessageStub()
        );
        $this->assertSame($query, $httpRequest->getQuery());
    }

    /** @covers Brickoo\Component\Http\HttpRequest::getMethod */
    public function testGetMethod() {
        $method = $this->getHttpMethodStub();
        $httpRequest = new HttpRequest(
            $method,
            $this->getHttpVersionStub(),
            $this->getHttpUriStub(),
            $this->getHttpMessageStub()
        );
        $this->assertSame($method, $httpRequest->getMethod());
    }

    /** @covers Brickoo\Component\Http\HttpRequest::getVersion */
    public function testGetVersion() {
        $version = $this->getHttpVersionStub();
        $httpRequest = new HttpRequest(
            $this->getHttpMethodStub(),
            $version,
            $this->getHttpUriStub(),
            $this->getHttpMessageStub()
        );
        $this->assertSame($version, $httpRequest->getVersion());
    }

    /** @covers Brickoo\Component\Http\HttpRequest::getMessage */
    public function testGetMessage() {
        $message = $this->getHttpMessageStub();
        $httpRequest = new HttpRequest(
            $this->getHttpMethodStub(),
            $this->getHttpVersionStub(),
            $this->getHttpUriStub(),
            $message
        );
        $this->assertSame($message, $httpRequest->getMessage());
    }

    /** @covers Brickoo\Component\Http\HttpRequest::getHeader */
    public function testGetHeader() {
        $header = $this->getHttpMessageHeaderStub();
        $message = $this->getHttpMessageStub();
        $message->expects($this->once())
                ->method("getHeader")
                ->will($this->returnValue($header));
        $httpRequest = new HttpRequest(
            $this->getHttpMethodStub(),
            $this->getHttpVersionStub(),
            $this->getHttpUriStub(),
            $message
        );
        $this->assertSame($header, $httpRequest->getHeader());
    }

    /** @covers Brickoo\Component\Http\HttpRequest::getBody */
    public function testGetBody() {
        $body = $this->getHttpHttpMessageBodyStub();
        $message = $this->getHttpMessageStub();
        $message->expects($this->once())
                ->method("getBody")
                ->will($this->returnValue($body));
        $httpRequest = new HttpRequest(
            $this->getHttpMethodStub(),
            $this->getHttpVersionStub(),
            $this->getHttpUriStub(),
            $message
        );
        $this->assertSame($body, $httpRequest->getBody());
    }

    /** @covers Brickoo\Component\Http\HttpRequest::toString */
    public function testRequestToString() {
        $methodString = HttpMethod::GET;
        $versionString = HttpVersion::HTTP_1_1;
        $bodyString = "test content";
        $headerString = "UNIT: TEST";
        $queryString = "key=value1";
        $urlPath = "/path/to/script";

        $method = $this->getHttpMethodStub();
        $method->expects($this->any())
               ->method("toString")
               ->will($this->returnValue($methodString));

        $version = $this->getHttpVersionStub();
        $version->expects($this->any())
                ->method("toString")
                ->will($this->returnValue($versionString));

        $header = $this->getHttpMessageHeaderStub();
        $header->expects($this->any())
               ->method("toString")
               ->will($this->returnValue($headerString));

        $body = $this->getMock("\\Brickoo\\Component\\Http\\HttpMessageBody");
        $body->expects($this->any())
             ->method("getContent")
             ->will($this->returnValue($bodyString));

        $message = $this->getHttpMessageStub();
        $message->expects($this->any())
                ->method("getHeader")
                ->will($this->returnValue($header));
        $message->expects($this->any())
                ->method("getBody")
                ->will($this->returnValue($body));

        $query = $this->getMock("\\Brickoo\\Component\\Http\\UriQuery");
        $query->expects($this->any())
              ->method("toString")
              ->will($this->returnValue($queryString));

        $uri = $this->getHttpUriStub();
        $uri->expects($this->any())
            ->method("getQuery")
            ->will($this->returnValue($query));
        $uri->expects($this->any())
            ->method("getPath")
            ->will($this->returnValue($urlPath));


        $expectedValue = sprintf("%s %s %s\r\n",
            $methodString, $urlPath."?".$queryString, $versionString
        );
        $expectedValue .= $headerString."\r\n\r\n".$bodyString;

        $Request = new HttpRequest($method, $version, $uri, $message);
        $this->assertEquals($expectedValue, $Request->toString());
    }

    /**
     * Returns a http method stub.
     * @return \Brickoo\Component\Http\HttpMethod
     */
    private function getHttpMethodStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Http\\HttpMethod")
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
     * Returns a http uri stub.
     * @return \Brickoo\Component\Http\Uri
     */
    private function getHttpUriStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Http\\Uri")
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

    /**
     * Returns a http message header stub.
     * @return \Brickoo\Component\Http\HttpMessageHeader
     */
    private function getHttpMessageHeaderStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Http\\HttpMessageHeader")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a http message body stub.
     * @return \Brickoo\Component\Http\HttpMessageBody
     */
    private function getHttpHttpMessageBodyStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Http\\HttpMessageBody")
            ->disableOriginalConstructor()
            ->getMock();
    }

}
