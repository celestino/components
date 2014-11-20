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

namespace Brickoo\Tests\Component\Http\Aggregator;

use Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator;
use PHPUnit_Framework_TestCase;

/**
 * HttpRequestUriAggregatorTest
 *
 * Test suite for the UriAggregator class.
 * @see Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpRequestUriAggregatorTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::__construct
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getScheme
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::isForwardedFromHttps
     */
    public function testGetSchemeFromForwardedProtocolIsSecure() {
        $messageHeader = $this->getMessageHeaderMock("X-Forwarded-Proto", $this->getHeaderStub("HTTPS"));
        $uriAggregator = new HttpRequestUriAggregator($messageHeader);
        $this->assertEquals("https", $uriAggregator->getScheme());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getScheme
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getServerVar
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::isForwardedFromHttps
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::isHttpsMode
     */
    public function testGetSchemeFromServerValuesIsSecure() {
        $uriAggregator = new HttpRequestUriAggregator($this->getMessageHeaderStub(), ["HTTPS" => "on"]);
        $this->assertEquals("https", $uriAggregator->getScheme());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getScheme
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getServerVar
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::isForwardedFromHttps
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::isHttpsMode
     */
    public function testGetSchemeFromServerValuesIsNotSecure() {
        $uriAggregator = new HttpRequestUriAggregator($this->getMessageHeaderStub(), ["HTTPS" => "off"]);
        $this->assertEquals("http", $uriAggregator->getScheme());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::__construct
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getHostname
     */
    public function testGetHostnameFromHeader() {
        $messageHeader = $this->getMessageHeaderMock("Host", $this->getHeaderStub("example.org"));
        $uriAggregator = new HttpRequestUriAggregator($messageHeader);
        $this->assertEquals("example.org", $uriAggregator->getHostname());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getHostname
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getServerVar
     */
    public function testGetHostnameFromServerValues() {
        $uriAggregator = new HttpRequestUriAggregator($this->getMessageHeaderStub(), ["SERVER_NAME" => "example.org"]);
        $this->assertEquals("example.org", $uriAggregator->getHostname());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::__construct
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getPort
     */
    public function testGetPortFromForwardedPort() {
        $messageHeader = $this->getMessageHeaderMock("X-Forwarded-Port", $this->getHeaderStub(8080));
        $uriAggregator = new HttpRequestUriAggregator($messageHeader);
        $this->assertEquals(8080, $uriAggregator->getPort());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getPort
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getServerVar
     */
    public function testGetPortFromServerValues() {
        $uriAggregator = new HttpRequestUriAggregator($this->getMessageHeaderStub(), ["SERVER_PORT" => 8080]);
        $this->assertEquals(8080, $uriAggregator->getPort());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getPath
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getIisRequestUri
     */
    public function testGetPathWithoutProviders() {
        $uriAggregator = new HttpRequestUriAggregator($this->getMessageHeaderStub(), []);
        $this->assertEquals("/", $uriAggregator->getPath());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getPath
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getServerVar
     */
    public function testGetPathFromRequestUri() {
        $uriAggregator = new HttpRequestUriAggregator($this->getMessageHeaderStub(), ["REQUEST_URI" => "/path/to/app"]);
        $this->assertEquals("/path/to/app", $uriAggregator->getPath());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getPath
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getServerVar
     */
    public function testGetPathFromOriginalPathInfo() {
        $uriAggregator = new HttpRequestUriAggregator($this->getMessageHeaderStub(), ["ORIG_PATH_INFO" => "/path/to/app"]);
        $this->assertEquals("/path/to/app", $uriAggregator->getPath());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getPath
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getIisRequestUri
     */
    public function testGetPathFromISSOriginalUrlHeader() {
        $uriAggregator = new HttpRequestUriAggregator($this->getMessageHeaderMock("X-Original-Url", $this->getHeaderStub("/path/to/app")));
        $this->assertEquals("/path/to/app", $uriAggregator->getPath());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getPath
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getIisRequestUri
     */
    public function testGetPathFromISSRewriteUrlHeader() {
        $headerChecks = [["X-Original-Url", false], ["X-Rewrite-Url", true]];
        $messageHeader = $this->getMessageHeaderStub();
        $messageHeader->expects($this->any())
                      ->method("contains")
                      ->will($this->returnValueMap($headerChecks));
        $messageHeader->expects($this->any())
                      ->method("getHeader")
                      ->with("X-Rewrite-Url")
                      ->will($this->returnValue($this->getHeaderStub("/path/to/app/")));
        $uriAggregator = new HttpRequestUriAggregator($messageHeader);
        $this->assertEquals("/path/to/app", $uriAggregator->getPath());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getQueryString
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getServerVar
     */
    public function testGetQueryStringFromServerValue() {
        $uriAggregator = new HttpRequestUriAggregator($this->getMessageHeaderStub(), ["QUERY_STRING" => "a=b&c=d"]);
        $this->assertEquals("a=b&c=d", $uriAggregator->getQueryString());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getQueryString
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getServerVar
     */
    public function testGetQueryStringFromGlobalGetVariable() {
        $backupGlobalGet = $_GET;
        $_GET = ["a" => "b", "c" => "d"];
        $uriAggregator = new HttpRequestUriAggregator($this->getMessageHeaderStub(), []);
        $this->assertEquals("a=b&c=d", $uriAggregator->getQueryString());
        $_GET = $backupGlobalGet;
    }

    /** @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getFragment */
    public function testGetFragment() {
        $uriAggregator = new HttpRequestUriAggregator($this->getMessageHeaderStub(), []);
        $this->assertEquals("", $uriAggregator->getFragment());
    }

    /**
     * Returns a message header stub.
     * @return \Brickoo\Component\Http\HttpMessageHeader
     */
    private function getMessageHeaderStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Http\\HttpMessageHeader")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a message header mock.
     * @param string $headerName
     * @param \Brickoo\Component\Http\HttpHeader $headerStub
     * @return \Brickoo\Component\Http\HttpMessageHeader
     */
    private function getMessageHeaderMock($headerName, $headerStub) {
        $messageHeader = $this->getMockBuilder("\\Brickoo\\Component\\Http\\HttpMessageHeader")
            ->disableOriginalConstructor()
            ->getMock();
        $messageHeader->expects($this->any())
                      ->method("contains")
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
