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
