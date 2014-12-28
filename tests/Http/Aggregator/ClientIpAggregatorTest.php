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

use Brickoo\Component\Http\Aggregator\ClientIpAggregator;
use PHPUnit_Framework_TestCase;

/**
 * ClientIpAggregator
 *
 * Test suite for the ClientIpAggregator class.
 * @see Brickoo\Component\Http\Aggregator\ClientIpAggregator
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ClientIpAggregatorTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::__construct
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getOriginalClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getForwardedClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIpFromHeaders
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getServerVar
     */
    public function testGetClientIpCouldBeEmpty() {
        $clientIpAggregator = new ClientIpAggregator($this->getMessageHeaderStub());
        $this->assertEquals("", $clientIpAggregator->getClientIp());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getOriginalClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getForwardedClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIpFromHeaders
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getServerVar
     */
    public function testGetClientIpCouldReturnProxyIp() {
        $clientIpAggregator = new ClientIpAggregator($this->getMessageHeaderStub(), ["REMOTE_ADDR" => "10.20.30.40"], ["10.20.30.40"]);
        $this->assertEquals("10.20.30.40", $clientIpAggregator->getClientIp());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getOriginalClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getForwardedClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIpFromHeaders
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getServerVar
     */
    public function testGetClientIpFromServerValue() {
        $clientIpAggregator = new ClientIpAggregator($this->getMessageHeaderStub(), ["REMOTE_ADDR" => "127.0.0.1"]);
        $this->assertEquals("127.0.0.1", $clientIpAggregator->getClientIp());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getOriginalClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getForwardedClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIpFromHeaders
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getServerVar
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
        $clientIpAggregator = new ClientIpAggregator($messageHeader, ["REMOTE_ADDR" => "10.20.30.40"], ["10.20.30.40"]);
        $this->assertEquals("127.0.0.1", $clientIpAggregator->getClientIp());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getOriginalClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getForwardedClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIpFromHeaders
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getServerVar
     */
    public function testGetClientIpForwarded() {
        $clientIpAggregator = new ClientIpAggregator(
            $this->getMessageHeaderMock("X-Forwarded-For", $this->getHeaderStub("127.0.0.1, 88.99.100.101")),
            ["REMOTE_ADDR" => "10.20.30.40"],
            ["10.20.30.40"]
        );
        $this->assertEquals("127.0.0.1", $clientIpAggregator->getClientIp());
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
