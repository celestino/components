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

namespace Brickoo\Tests\Component\Http\Aggregator;

use Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator;
use Brickoo\Component\Http\Header\GenericHeaderField;
use Brickoo\Component\Http\HttpMessageHeader;
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
        $uriAggregator = new HttpRequestUriAggregator(
            new HttpMessageHeader([new GenericHeaderField("X-Forwarded-Proto", "HTTPS")])
        );
        $this->assertEquals("https", $uriAggregator->getScheme());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getScheme
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getServerVar
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::isForwardedFromHttps
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::isHttpsMode
     */
    public function testGetSchemeFromServerValues() {
        $uriAggregator = new HttpRequestUriAggregator(new HttpMessageHeader(), ["HTTPS" => "on"]);
        $this->assertEquals("https", $uriAggregator->getScheme());

        $uriAggregator = new HttpRequestUriAggregator(new HttpMessageHeader(), ["HTTPS" => "off"]);
        $this->assertEquals("http", $uriAggregator->getScheme());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::__construct
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getHostname
     */
    public function testGetHostnameFromHeader() {
        $uriAggregator = new HttpRequestUriAggregator(
            new HttpMessageHeader([new GenericHeaderField("Host", "example.org")])
        );
        $this->assertEquals("example.org", $uriAggregator->getHostname());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getHostname
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getServerVar
     */
    public function testGetHostnameFromServerValues() {
        $uriAggregator = new HttpRequestUriAggregator(new HttpMessageHeader(), ["SERVER_NAME" => "example.org"]);
        $this->assertEquals("example.org", $uriAggregator->getHostname());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::__construct
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getPort
     */
    public function testGetPortFromForwardedPort() {
        $uriAggregator = new HttpRequestUriAggregator(
            new HttpMessageHeader([new GenericHeaderField("X-Forwarded-Port", "8080")])
        );
        $this->assertEquals(8080, $uriAggregator->getPort());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getPort
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getServerVar
     */
    public function testGetPortFromServerValues() {
        $uriAggregator = new HttpRequestUriAggregator(new HttpMessageHeader(), ["SERVER_PORT" => 8080]);
        $this->assertEquals(8080, $uriAggregator->getPort());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getPath
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getIisRequestUri
     */
    public function testGetPathWithoutProviders() {
        $uriAggregator = new HttpRequestUriAggregator(new HttpMessageHeader());
        $this->assertEquals("/", $uriAggregator->getPath());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getPath
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getServerVar
     */
    public function testGetPathFromRequestUri() {
        $uriAggregator = new HttpRequestUriAggregator(new HttpMessageHeader(), ["REQUEST_URI" => "/path/to/app"]);
        $this->assertEquals("/path/to/app", $uriAggregator->getPath());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getPath
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getServerVar
     */
    public function testGetPathFromOriginalPathInfo() {
        $uriAggregator = new HttpRequestUriAggregator(new HttpMessageHeader(), ["ORIG_PATH_INFO" => "/path/to/app"]);
        $this->assertEquals("/path/to/app", $uriAggregator->getPath());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getPath
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getIisRequestUri
     */
    public function testGetPathFromISSOriginalUrlHeader() {
        $uriAggregator = new HttpRequestUriAggregator(
            new HttpMessageHeader([new GenericHeaderField("X-Original-Url", "/path/to/app")])
        );
        $this->assertEquals("/path/to/app", $uriAggregator->getPath());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getPath
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getIisRequestUri
     */
    public function testGetPathFromISSRewriteUrlHeader() {
        $uriAggregator = new HttpRequestUriAggregator(
            new HttpMessageHeader([new GenericHeaderField("X-Rewrite-Url", "/path/to/app")])
        );
        $this->assertEquals("/path/to/app", $uriAggregator->getPath());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getQueryString
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getServerVar
     */
    public function testGetQueryStringFromServerValue() {
        $uriAggregator = new HttpRequestUriAggregator(new HttpMessageHeader(), ["QUERY_STRING" => "a=b&c=d"]);
        $this->assertEquals("a=b&c=d", $uriAggregator->getQueryString());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getQueryString
     * @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getServerVar
     */
    public function testGetQueryStringFromGlobalGetVariable() {
        $backupGlobalGet = $_GET;
        $_GET = ["a" => "b", "c" => "d"];
        $uriAggregator = new HttpRequestUriAggregator(new HttpMessageHeader());
        $this->assertEquals("a=b&c=d", $uriAggregator->getQueryString());
        $_GET = $backupGlobalGet;
    }

    /** @covers Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator::getFragment */
    public function testGetFragment() {
        $uriAggregator = new HttpRequestUriAggregator(new HttpMessageHeader());
        $this->assertEquals("", $uriAggregator->getFragment());
    }

}
