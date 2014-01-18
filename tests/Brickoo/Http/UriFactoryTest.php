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

namespace Brickoo\Tests\Http;

use Brickoo\Http\UriFactory,
    PHPUnit_Framework_TestCase;

/**
 * UriFactoryTest
 *
 * Test suite for the UriFactory class.
 * @see Brickoo\Http\UriFactory
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class UriFactoryTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Http\UriFactory::create
     * @covers Brickoo\Http\UriFactory::createAuthority
     * @covers Brickoo\Http\UriFactory::createQuery
     */
    public function testCreateUri() {
        $uriResolver = $this->getMockBuilder("\\Brickoo\\Http\\Resolver\\UriResolver")
            ->disableOriginalConstructor()->getMock();
        $uriResolver->expects($this->any())
                    ->method("getScheme")
                    ->will($this->returnValue("http"));
        $uriResolver->expects($this->any())
                    ->method("getHostname")
                    ->will($this->returnValue("example.org"));
        $uriResolver->expects($this->any())
                    ->method("getPort")
                    ->will($this->returnValue(80));
        $uriResolver->expects($this->any())
                    ->method("getQueryString")
                    ->will($this->returnValue("a=b&c=d"));
        $uriResolver->expects($this->any())
                    ->method("getFragment")
                    ->will($this->returnValue("section_1"));
        $uriResolver->expects($this->any())
                    ->method("getPath")
                    ->will($this->returnValue("/blog/post/1"));

        $uri = (new UriFactory())->create($uriResolver);
        $this->assertInstanceOf("\\Brickoo\\Http\\Uri", $uri);
    }

}