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

use Brickoo\Component\Http\Resolver\HeaderResolver,
    PHPUnit_Framework_TestCase;

/**
 * HeaderResolver
 *
 * Test suite for the HeaderResolver class.
 * @see Brickoo\Component\Http\Resolver\HeaderResolver
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HeaderResolverTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::__construct
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::getHeaders
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::loadHeaders
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::hasMappingHeaderClass
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::getHeader
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::createMappingHeader
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::createGenericHeader
     * @covers Brickoo\Component\Http\HttpHeaderNormalizer::normalizeHeaders
     */
    public function testGetHeadersWithValidMap() {
        $headerMap = include realpath(__DIR__)."/Assets/validHeader.map";
        $headerLoader = $this->getHeaderResolverPluginStub();
        $headerLoader->expects($this->any())
                     ->method("getHeaders")
                     ->will($this->returnValue([
                         "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/web,*/*;q=0.8",
                         "Connection" => "keep-alive"
                     ]));
        $headerResolver = new HeaderResolver($headerMap, $headerLoader);
        $headers = $headerResolver->getHeaders();
        $this->assertInternalType("array", $headers);

        foreach ($headers as $header) {
            $this->assertInstanceOf("\\Brickoo\\Component\\Http\\Header\\GenericHeader", $header);
            if ($header->getName() == "Accept") {
                $this->assertInstanceOf("\\Brickoo\\Component\\Http\\Header\\AcceptHeader", $header);
            }
        }
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::__construct
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::getHeaderLists
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::loadHeaders
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::hasMappingHeaderClass
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::getHeader
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::createMappingHeader
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::createGenericHeader
     * @covers Brickoo\Component\Http\HttpHeaderNormalizer::normalizeHeaders
     */
    public function testGetHeaderLists() {
        $headerMap = include realpath(__DIR__)."/Assets/validHeader.map";
        $headerLoader = $this->getHeaderResolverPluginStub();
        $headerLoader->expects($this->any())
                     ->method("getHeaders")
                     ->will($this->returnValue([
                         "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/web,*/*;q=0.8",
                         "Connection" => "keep-alive"
                     ]));
        $headerResolver = new HeaderResolver($headerMap, $headerLoader);
        $headerLists = $headerResolver->getHeaderLists();
        $this->assertInternalType("array", $headerLists);
        $this->assertEquals(2, count($headerLists));

        foreach ($headerLists as $headerName => $headerList) {
            $this->assertInstanceOf("\\Brickoo\\Component\\Http\\HttpHeaderList", $headerList);
            if ($headerName == "Accept") {
                $this->assertInstanceOf("\\Brickoo\\Component\\Http\\Header\\GenericHeader", $headerList->first());
                $this->assertInstanceOf("\\Brickoo\\Component\\Http\\Header\\AcceptHeader", $headerList->first());
            }
        }
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::__construct
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::getHeaders
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::loadHeaders
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::hasMappingHeaderClass
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::getHeader
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::createMappingHeader
     * @covers Brickoo\Component\Http\Resolver\HeaderResolver::createGenericHeader
     * @covers Brickoo\Component\Http\HttpHeaderNormalizer::normalizeHeaders
     * @covers Brickoo\Component\Http\Resolver\Exception\HeaderClassNotFoundException
     * @expectedException \Brickoo\Component\Http\Resolver\Exception\HeaderClassNotFoundException
     */
    public function testGetHeadersWithNotExistingHeaderThrowsException() {
        $headerMap = include realpath(__DIR__)."/Assets/wrongHeader.map";
        $headerLoader = $this->getHeaderResolverPluginStub();
        $headerLoader->expects($this->any())
                     ->method("getHeaders")
                     ->will($this->returnValue([
                         "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8"
                     ]));
        $headerResolver = new HeaderResolver($headerMap, $headerLoader);
        $headerResolver->getHeaders();
    }

    /**
     * Returns a header resolver loader stub.
     * @return \Brickoo\Component\Http\Resolver\HeaderResolverPlugin
     */
    private function getHeaderResolverPluginStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Http\\Resolver\\HeaderResolverPlugin")
            ->disableOriginalConstructor()
            ->getMock();
    }

}
