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

use Brickoo\Component\Http\Uri;
use PHPUnit_Framework_TestCase;

/**
 * UriTest
 *
 * Test suite for the Uri class.
 * @see Brickoo\Component\Http\Uri
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class UriTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\Uri::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidSchemeThrowsException() {
        new Uri(["wrongType"], $this->getUriAuthorityStub(), "/", $this->getUriQueryStub(), "");
    }

    /**
     * @covers Brickoo\Component\Http\Uri::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidPathThrowsException() {
        new Uri("http", $this->getUriAuthorityStub(), ["wrongType"], $this->getUriQueryStub(), "");
    }

    /**
     * @covers Brickoo\Component\Http\Uri::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidFragmentThrowsException() {
        new Uri("http", $this->getUriAuthorityStub(), "/", $this->getUriQueryStub(), ["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Http\Uri::__construct
     * @covers Brickoo\Component\Http\Uri::getScheme
     */
    public function testGetScheme() {
        $scheme = "https";
        $uri = new Uri($scheme, $this->getUriAuthorityStub(), "/", $this->getUriQueryStub(), "");
        $this->assertEquals($scheme, $uri->getScheme());
    }

    /** @covers Brickoo\Component\Http\Uri::getAuthority */
    public function testGetAuthority() {
        $authority = $this->getUriAuthorityStub();
        $uri = new Uri("https", $authority, "/", $this->getUriQueryStub(), "");
        $this->assertSame($authority, $uri->getAuthority());
    }

    /** @covers Brickoo\Component\Http\Uri::getHostname */
    public function testGetHostName() {
        $hostname = "example.org";
        $authority = $this->getUriAuthorityStub();
        $authority->expects($this->any())
                  ->method("getHostname")
                  ->will($this->returnValue($hostname));
        $uri = new Uri("https", $authority, "/", $this->getUriQueryStub(), "");
        $this->assertEquals($hostname, $uri->getHostname());
    }

    /** @covers Brickoo\Component\Http\Uri::getPath */
    public function testGetPath() {
        $path = "/some/path";
        $uri = new Uri("https", $this->getUriAuthorityStub(), $path, $this->getUriQueryStub(), "");
        $this->assertEquals($path, $uri->getPath());
    }

    /** @covers Brickoo\Component\Http\Uri::getQuery */
    public function testGetQuery() {
        $query = $this->getUriQueryStub();
        $uri = new Uri("https", $this->getUriAuthorityStub(), "/", $query, "");
        $this->assertSame($query, $uri->getQuery());
    }

    /** @covers Brickoo\Component\Http\Uri::getFragment */
    public function testGetFragment() {
        $fragment = "section_1";
        $uri = new Uri("https", $this->getUriAuthorityStub(), "/", $this->getUriQueryStub(), $fragment);
        $this->assertEquals($fragment, $uri->getFragment());
    }

    /** @covers Brickoo\Component\Http\Uri::toString */
    public function testToString() {
        $scheme = "https";
        $authorityString = "example.org:8080";
        $path = "/some/path";
        $queryString = "a=b";
        $fragment = "section_1";

        $authority = $this->getUriAuthorityStub();
        $authority->expects($this->any())
                  ->method("toString")
                  ->will($this->returnValue($authorityString));
        $query = $this->getUriQueryStub();
        $query->expects($this->any())
              ->method("toString")
              ->will($this->returnValue($queryString));

        $expectedUri = sprintf("%s://%s%s?%s#%s", $scheme, $authorityString, $path, $queryString, $fragment);
        $uri = new Uri($scheme, $authority, $path, $query, $fragment);
        $this->assertEquals($expectedUri, $uri->toString());
    }

    /**
     * Returns a uri authority stub.
     * @return  \Brickoo\Component\Http\UriAuthority
     */
    private function getUriAuthorityStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Http\\UriAuthority")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a uri query stub.
     * @return  \Brickoo\Component\Http\UriQuery
     */
    private function getUriQueryStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Http\\UriQuery")
            ->disableOriginalConstructor()
            ->getMock();
    }

}
