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

use Brickoo\Component\Http\Resolver\ClientIpResolver,
    PHPUnit_Framework_TestCase;

/**
 * ClientIpResolver
 *
 * Test suite for the ClientIpResolver class.
 * @see Brickoo\Component\Http\Resolver\ClientIpResolver
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ClientIpResolverTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::__construct
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getClientIp
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getOriginalClientIp
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getForwardedClientIp
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getClientIpFromHeaders
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getServerVar
     */
    public function testGetClientIpCouldBeEmpty() {
        $clientIpResolver = new ClientIpResolver($this->getMessageHeaderStub());
        $this->assertEquals("", $clientIpResolver->getClientIp());
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getClientIp
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getOriginalClientIp
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getForwardedClientIp
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getClientIpFromHeaders
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getServerVar
     */
    public function testGetClientIpCouldReturnProxyIp() {
        $clientIpResolver = new ClientIpResolver($this->getMessageHeaderStub(), ["REMOTE_ADDR" => "10.20.30.40"], ["10.20.30.40"]);
        $this->assertEquals("10.20.30.40", $clientIpResolver->getClientIp());
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getClientIp
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getOriginalClientIp
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getForwardedClientIp
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getClientIpFromHeaders
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getServerVar
     */
    public function testGetClientIpFromServerValue() {
        $clientIpResolver = new ClientIpResolver($this->getMessageHeaderStub(), ["REMOTE_ADDR" => "127.0.0.1"]);
        $this->assertEquals("127.0.0.1", $clientIpResolver->getClientIp());
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getClientIp
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getOriginalClientIp
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getForwardedClientIp
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getClientIpFromHeaders
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getServerVar
     */
    public function testGetClientIpFromClientIPHeader() {
        $headerChecks = [["X-Forwarded-For", false], ["Client-Ip", true]];
        $messageHeader = $this->getMessageHeaderStub();
        $messageHeader->expects($this->any())
                      ->method("contains")
                      ->will($this->returnValueMap($headerChecks));
        $messageHeader->expects($this->any())
                      ->method("getHeader")
                      ->with("Client-Ip")
                      ->will($this->returnValue($this->getHeaderStub("127.0.0.1")));
        $clientIpResolver = new ClientIpResolver($messageHeader, ["REMOTE_ADDR" => "10.20.30.40"], ["10.20.30.40"]);
        $this->assertEquals("127.0.0.1", $clientIpResolver->getClientIp());
    }

    /**
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getClientIp
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getOriginalClientIp
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getForwardedClientIp
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getClientIpFromHeaders
     * @covers Brickoo\Component\Http\Resolver\ClientIpResolver::getServerVar
     */
    public function testGetClientIpForwarded() {
        $clientIpResolver = new ClientIpResolver(
            $this->getMessageHeaderMock("X-Forwarded-For", $this->getHeaderStub("127.0.0.1, 88.99.100.101")),
            ["REMOTE_ADDR" => "10.20.30.40"],
            ["10.20.30.40"]
        );
        $this->assertEquals("127.0.0.1", $clientIpResolver->getClientIp());
    }

    /**
     * Returns a message header stub.
     * @return \Brickoo\Component\Http\HttpMessageHeaders
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
