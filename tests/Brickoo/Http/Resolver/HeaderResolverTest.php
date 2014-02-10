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

use Brickoo\Http\Resolver\HeaderResolver,
    PHPUnit_Framework_TestCase;

/**
 * HeaderResolver
 *
 * Test suite for the HeaderResolver class.
 * @see Brickoo\Http\Resolver\HeaderResolver
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HeaderResolverTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Http\Resolver\HeaderResolver::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidMapTypeThrowsException() {
        new HeaderResolver(["wrongType"], $this->getHeaderResolverPluginStub());
    }

    /**
     * @covers Brickoo\Http\Resolver\HeaderResolver::__construct
     * @covers Brickoo\Http\Resolver\Exception\FileDoesNotExistException
     * @expectedException \Brickoo\Http\Resolver\Exception\FileDoesNotExistException
     */
    public function testConstructorMapFileDoesNotExistThrowsException() {
        new HeaderResolver("doesNotExist.map", $this->getHeaderResolverPluginStub());
    }

    /**
     * @covers Brickoo\Http\Resolver\HeaderResolver::__construct
     * @covers Brickoo\Http\Resolver\Exception\FileIsNotReadableException
     * @expectedException \Brickoo\Http\Resolver\Exception\FileIsNotReadableException
     */
    public function testConstructorMapFileNotReadableThrowsException() {
        $filename = sys_get_temp_dir()."/brickoo_unittest_unreadable_".time().".map";
        file_put_contents($filename, "");
        @chmod($filename, "111");
        new HeaderResolver($filename, $this->getHeaderResolverPluginStub());
    }

    /**
     * @covers Brickoo\Http\Resolver\HeaderResolver::__construct
     * @covers Brickoo\Http\Resolver\HeaderResolver::getHeaders
     * @covers Brickoo\Http\Resolver\HeaderResolver::loadHeaders
     * @covers Brickoo\Http\Resolver\HeaderResolver::loadHeaderMap
     * @covers Brickoo\Http\Resolver\HeaderResolver::normalizeHeaders
     * @covers Brickoo\Http\Resolver\Exception\WrongHeaderMapTypeException
     * @expectedException \Brickoo\Http\Resolver\Exception\WrongHeaderMapTypeException
     */
    public function testGetHeadersInvalidMapFileThrowsException() {
        $headerLoader = $this->getHeaderResolverPluginStub();
        $headerLoader->expects($this->any())
                     ->method("getHeaders")
                     ->will($this->returnValue(array()));
        $headerResolver = new HeaderResolver(realpath(__DIR__)."/Assets/invalidHeader.map", $headerLoader);
        $headerResolver->getHeaders();
    }

    /**
     * @covers Brickoo\Http\Resolver\HeaderResolver::getHeaders
     * @covers Brickoo\Http\Resolver\HeaderResolver::loadHeaders
     * @covers Brickoo\Http\Resolver\HeaderResolver::loadHeaderMap
     * @covers Brickoo\Http\Resolver\HeaderResolver::hasMappingHeaderClass
     * @covers Brickoo\Http\Resolver\HeaderResolver::getHeader
     * @covers Brickoo\Http\Resolver\HeaderResolver::createMappingHeader
     * @covers Brickoo\Http\Resolver\HeaderResolver::createGenericHeader
     * @covers Brickoo\Http\Resolver\HeaderResolver::normalizeHeaders
     */
    public function testGetHeadersWithValidMap() {
        $headerLoader = $this->getHeaderResolverPluginStub();
        $headerLoader->expects($this->any())
                     ->method("getHeaders")
                     ->will($this->returnValue([
                         "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
                         "Connection" => "keep-alive"
                     ]));
        $headerResolver = new HeaderResolver(realpath(__DIR__)."/Assets/validHeader.map", $headerLoader);
        $headers = $headerResolver->getHeaders();
        $this->assertInternalType("array", $headers);
        foreach ($headers as $header) {
            $this->assertInstanceOf("\\Brickoo\\Http\\Header\\GenericHeader", $header);
            if ($header->getName() == "Accept") {
                $this->assertInstanceOf("\\Brickoo\\Http\\Header\\AcceptHeader", $header);
            }
        }
    }

    /**
     * @covers Brickoo\Http\Resolver\HeaderResolver::getHeaders
     * @covers Brickoo\Http\Resolver\HeaderResolver::loadHeaders
     * @covers Brickoo\Http\Resolver\HeaderResolver::loadHeaderMap
     * @covers Brickoo\Http\Resolver\HeaderResolver::hasMappingHeaderClass
     * @covers Brickoo\Http\Resolver\HeaderResolver::getHeader
     * @covers Brickoo\Http\Resolver\HeaderResolver::createMappingHeader
     * @covers Brickoo\Http\Resolver\HeaderResolver::createGenericHeader
     * @covers Brickoo\Http\Resolver\HeaderResolver::normalizeHeaders
     * @covers Brickoo\Http\Resolver\Exception\HeaderClassNotFoundException
     * @expectedException Brickoo\Http\Resolver\Exception\HeaderClassNotFoundException
     */
    public function testGetHeadersWithNotExistingHeaderThrowsException() {
        $headerLoader = $this->getHeaderResolverPluginStub();
        $headerLoader->expects($this->any())
                     ->method("getHeaders")
                     ->will($this->returnValue([
                         "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8"
                     ]));
        $headerResolver = new HeaderResolver(realpath(__DIR__)."/Assets/wrongHeader.map", $headerLoader);
        $headerResolver->getHeaders();
    }

    /**
     * Returns a header resolver loader stub.
     * @return \Brickoo\Http\Resolver\HeaderResolverPlugin
     */
    private function getHeaderResolverPluginStub() {
        return $this->getMockBuilder("\\Brickoo\\Http\\Resolver\\HeaderResolverPlugin")
            ->disableOriginalConstructor()
            ->getMock();
    }

}