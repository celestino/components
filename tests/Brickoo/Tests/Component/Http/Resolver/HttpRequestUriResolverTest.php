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

namespace Brickoo\Tests\Component\Http\Resolver;

use Brickoo\Component\Http\Resolver\HttpRequestUriResolver,
    PHPUnit_Framework_TestCase;

/**
 * HttpRequestUriResolverTest
 *
 * Test suite for the UriResolver class.
 * @see Brickoo\Component\Http\Resolver\HttpRequestUriResolver
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpRequestUriResolverTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::__construct
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getScheme
     */
    public function testGetSchemeFromForwardedProtocol() {
        $messageHeader = $this->getMessageHeaderMock("X-Forwarded-Proto", $this->getHeaderStub("HTTPS"));
        $uriResolver = new HttpRequestUriResolver($messageHeader);
        $this->assertEquals("https", $uriResolver->getScheme());
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getScheme
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getServerVar
     */
    public function testGetSchemeFromServerValues() {
        $uriResolver = new HttpRequestUriResolver($this->getMessageHeaderStub(), ["HTTPS" => "on"]);
        $this->assertEquals("https", $uriResolver->getScheme());
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::__construct
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getHostname
     */
    public function testGetHostnameFromHeader() {
        $messageHeader = $this->getMessageHeaderMock("Host", $this->getHeaderStub("example.org"));
        $uriResolver = new HttpRequestUriResolver($messageHeader);
        $this->assertEquals("example.org", $uriResolver->getHostname());
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getHostname
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getServerVar
     */
    public function testGetHostnameFromServerValues() {
        $uriResolver = new HttpRequestUriResolver($this->getMessageHeaderStub(), ["SERVER_NAME" => "example.org"]);
        $this->assertEquals("example.org", $uriResolver->getHostname());
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::__construct
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getPort
     */
    public function testGetPortFromForwardedPort() {
        $messageHeader = $this->getMessageHeaderMock("X-Forwarded-Port", $this->getHeaderStub(8080));
        $uriResolver = new HttpRequestUriResolver($messageHeader);
        $this->assertEquals(8080, $uriResolver->getPort());
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getPort
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getServerVar
     */
    public function testGetPortFromServerValues() {
        $uriResolver = new HttpRequestUriResolver($this->getMessageHeaderStub(), ["SERVER_PORT" => 8080]);
        $this->assertEquals(8080, $uriResolver->getPort());
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getPath
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getIISRequestUri
     */
    public function testGetPathWithoutProviders() {
        $uriResolver = new HttpRequestUriResolver($this->getMessageHeaderStub(), array());
        $this->assertEquals("/", $uriResolver->getPath());
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getPath
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getServerVar
     */
    public function testGetPathFromRequestUri() {
        $uriResolver = new HttpRequestUriResolver($this->getMessageHeaderStub(), ["REQUEST_URI" => "/path/to/app"]);
        $this->assertEquals("/path/to/app", $uriResolver->getPath());
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getPath
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getServerVar
     */
    public function testGetPathFromOriginalPathInfo() {
        $uriResolver = new HttpRequestUriResolver($this->getMessageHeaderStub(), ["ORIG_PATH_INFO" => "/path/to/app"]);
        $this->assertEquals("/path/to/app", $uriResolver->getPath());
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getPath
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getIISRequestUri
     */
    public function testGetPathFromISSOriginalUrlHeader() {
        $uriResolver = new HttpRequestUriResolver($this->getMessageHeaderMock("X-Original-Url", $this->getHeaderStub("/path/to/app")));
        $this->assertEquals("/path/to/app", $uriResolver->getPath());
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getPath
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getIISRequestUri
     */
    public function testGetPathFromISSRewriteUrlHeader() {
        $headerChecks = [["X-Original-Url", false], ["X-Rewrite-Url", true]];
        $messageHeader = $this->getMessageHeaderStub();
        $messageHeader->expects($this->any())
                      ->method("hasHeader")
                      ->will($this->returnValueMap($headerChecks));
        $messageHeader->expects($this->any())
                      ->method("getHeader")
                      ->with("X-Rewrite-Url")
                      ->will($this->returnValue($this->getHeaderStub("/path/to/app/")));
        $uriResolver = new HttpRequestUriResolver($messageHeader);
        $this->assertEquals("/path/to/app", $uriResolver->getPath());
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getQueryString
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getServerVar
     */
    public function testGetQueryStringFromServerValue() {
        $uriResolver = new HttpRequestUriResolver($this->getMessageHeaderStub(), ["QUERY_STRING" => "a=b&c=d"]);
        $this->assertEquals("a=b&c=d", $uriResolver->getQueryString());
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getQueryString
     * @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getServerVar
     */
    public function testGetQueryStringFromGlobalGetVariable() {
        $backupGlobalGet = $_GET;
        $_GET = ["a" => "b", "c" => "d"];
        $uriResolver = new HttpRequestUriResolver($this->getMessageHeaderStub(), array());
        $this->assertEquals("a=b&c=d", $uriResolver->getQueryString());
        $_GET = $backupGlobalGet;
    }

    /** @covers Brickoo\Component\Http\Resolver\HttpRequestUriResolver::getFragment */
    public function testGetFragment() {
        $uriResolver = new HttpRequestUriResolver($this->getMessageHeaderStub(), array());
        $this->assertEquals("", $uriResolver->getFragment());
    }

    /**
     * Returns a message header stub.
     * @return \Brickoo\Component\Http\MessageHeader
     */
    private function getMessageHeaderStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Http\\MessageHeader")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a message header mock.
     * @param string $headerName
     * @param \Brickoo\Component\Http\HttpHeader $headerStub
     * @return \Brickoo\Component\Http\MessageHeader
     */
    private function getMessageHeaderMock($headerName, $headerStub) {
        $messageHeader = $this->getMockBuilder("\\Brickoo\\Component\\Http\\MessageHeader")
            ->disableOriginalConstructor()
            ->getMock();
        $messageHeader->expects($this->any())
                      ->method("hasHeader")
                      ->with($headerName)
                      ->will($this->returnValue(true));
        $messageHeader->expects($this->any())
                      ->method("getHeader")
                      ->with($headerName)
                      ->will($this->returnValue($headerStub));
        return $messageHeader;
    }

    /**
     * Returns a http header stub.
     * @param string $headerValue
     * @return \Brickoo\Component\Http\HttpHeader
     */
    private function getHeaderStub($headerValue) {
        $header = $this->getMockBuilder("\\Brickoo\\Component\\Http\\HttpHeader")
            ->disableOriginalConstructor()
            ->getMock();
        $header->expects($this->any())
               ->method("getValue")
               ->will($this->returnValue($headerValue));
        return $header;
    }

}
