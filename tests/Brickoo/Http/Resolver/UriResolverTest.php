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

namespace Brickoo\Tests\Http\Resolver;

use Brickoo\Http\Resolver\UriResolver,
    PHPUnit_Framework_TestCase;

/**
 * UriResolver
 *
 * Test suite for the UriResolver class.
 * @see Brickoo\Http\Resolver\UriResolver
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class UriResolverTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Http\Resolver\UriResolver::__construct
     * @covers Brickoo\Http\Resolver\UriResolver::getScheme
     */
    public function testGetSchemeFromForwardedProtocol() {
        $messageHeader = $this->getMessageHeaderMock("X-Forwarded-Proto", $this->getHeaderStub("HTTPS"));
        $uriResolver = new UriResolver($messageHeader);
        $this->assertEquals("https", $uriResolver->getScheme());
    }

    /**
     * @covers Brickoo\Http\Resolver\UriResolver::getScheme
     * @covers Brickoo\Http\Resolver\UriResolver::getServerVar
     */
    public function testGetSchemeFromServerValues() {
        $uriResolver = new UriResolver($this->getMessageHeaderStub(), ["HTTPS" => "on"]);
        $this->assertEquals("https", $uriResolver->getScheme());
    }

    /**
     * @covers Brickoo\Http\Resolver\UriResolver::__construct
     * @covers Brickoo\Http\Resolver\UriResolver::getHostname
     */
    public function testGetHostnameFromHeader() {
        $messageHeader = $this->getMessageHeaderMock("Host", $this->getHeaderStub("example.org"));
        $uriResolver = new UriResolver($messageHeader);
        $this->assertEquals("example.org", $uriResolver->getHostname());
    }

    /**
     * @covers Brickoo\Http\Resolver\UriResolver::getHostname
     * @covers Brickoo\Http\Resolver\UriResolver::getServerVar
     */
    public function testGetHostnameFromServerValues() {
        $uriResolver = new UriResolver($this->getMessageHeaderStub(), ["SERVER_NAME" => "example.org"]);
        $this->assertEquals("example.org", $uriResolver->getHostname());
    }

    /**
     * @covers Brickoo\Http\Resolver\UriResolver::__construct
     * @covers Brickoo\Http\Resolver\UriResolver::getPort
     */
    public function testGetPortFromForwardedPort() {
        $messageHeader = $this->getMessageHeaderMock("X-Forwarded-Port", $this->getHeaderStub(8080));
        $uriResolver = new UriResolver($messageHeader);
        $this->assertEquals(8080, $uriResolver->getPort());
    }

    /**
     * @covers Brickoo\Http\Resolver\UriResolver::getPort
     * @covers Brickoo\Http\Resolver\UriResolver::getServerVar
     */
    public function testGetPortFromServerValues() {
        $uriResolver = new UriResolver($this->getMessageHeaderStub(), ["SERVER_PORT" => 8080]);
        $this->assertEquals(8080, $uriResolver->getPort());
    }

    /**
     * @covers Brickoo\Http\Resolver\UriResolver::getPath
     * @covers Brickoo\Http\Resolver\UriResolver::getIISRequestUri
     */
    public function testGetPathWithourProviders() {
        $uriResolver = new UriResolver($this->getMessageHeaderStub(), array());
        $this->assertEquals("/", $uriResolver->getPath());
    }

    /**
     * @covers Brickoo\Http\Resolver\UriResolver::getPath
     * @covers Brickoo\Http\Resolver\UriResolver::getServerVar
     */
    public function testGetPathFromRequestUri() {
        $uriResolver = new UriResolver($this->getMessageHeaderStub(), ["REQUEST_URI" => "/path/to/app"]);
        $this->assertEquals("/path/to/app", $uriResolver->getPath());
    }

    /**
     * @covers Brickoo\Http\Resolver\UriResolver::getPath
     * @covers Brickoo\Http\Resolver\UriResolver::getServerVar
     */
    public function testGetPathFromOriginalPathInfo() {
        $uriResolver = new UriResolver($this->getMessageHeaderStub(), ["ORIG_PATH_INFO" => "/path/to/app"]);
        $this->assertEquals("/path/to/app", $uriResolver->getPath());
    }

    /**
     * @covers Brickoo\Http\Resolver\UriResolver::getPath
     * @covers Brickoo\Http\Resolver\UriResolver::getIISRequestUri
     */
    public function testGetPathFromISSOriginalUrlHeader() {
        $uriResolver = new UriResolver($this->getMessageHeaderMock("X-Original-Url", $this->getHeaderStub("/path/to/app")));
        $this->assertEquals("/path/to/app", $uriResolver->getPath());
    }

    /**
     * @covers Brickoo\Http\Resolver\UriResolver::getPath
     * @covers Brickoo\Http\Resolver\UriResolver::getIISRequestUri
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
        $uriResolver = new UriResolver($messageHeader);
        $this->assertEquals("/path/to/app", $uriResolver->getPath());
    }

    /**
     * @covers Brickoo\Http\Resolver\UriResolver::getQueryString
     * @covers Brickoo\Http\Resolver\UriResolver::getServerVar
     */
    public function testGetQueryStringFromServerValue() {
        $uriResolver = new UriResolver($this->getMessageHeaderStub(), ["QUERY_STRING" => "a=b&c=d"]);
        $this->assertEquals("a=b&c=d", $uriResolver->getQueryString());
    }

    /**
     * @covers Brickoo\Http\Resolver\UriResolver::getQueryString
     * @covers Brickoo\Http\Resolver\UriResolver::getServerVar
     */
    public function testGetQueryStringFromGlobalGetVariable() {
        $backupGlobalGet = $_GET;
        $_GET = ["a" => "b", "c" => "d"];
        $uriResolver = new UriResolver($this->getMessageHeaderStub(), array());
        $this->assertEquals("a=b&c=d", $uriResolver->getQueryString());
        $_GET = $backupGlobalGet;
    }

    /** @covers Brickoo\Http\Resolver\UriResolver::getFragment */
    public function testGetFragment() {
        $uriResolver = new UriResolver($this->getMessageHeaderStub(), array());
        $this->assertEquals("", $uriResolver->getFragment());
    }

    /**
     * Returns a message header stub.
     * @return \Brickoo\Http\MessageHeader
     */
    private function getMessageHeaderStub() {
        return $this->getMockBuilder("\\Brickoo\\Http\\MessageHeader")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a message header mock.
     * @param string $headerName
     * @param \Brickoo\Http\Header $headerStub
     * @return \Brickoo\Http\MessageHeader
     */
    private function getMessageHeaderMock($headerName, $headerStub) {
        $messageHeader = $this->getMockBuilder("\\Brickoo\\Http\\MessageHeader")
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
     * @return \Brickoo\Http\Header
     */
    private function getHeaderStub($headerValue) {
        $header = $this->getMockBuilder("\\Brickoo\\Http\\HttpHeader")
            ->disableOriginalConstructor()
            ->getMock();
        $header->expects($this->any())
               ->method("getValue")
               ->will($this->returnValue($headerValue));
        return $header;
    }

}