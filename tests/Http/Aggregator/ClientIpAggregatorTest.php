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
use Brickoo\Component\Http\Header\GenericHeaderField;
use Brickoo\Component\Http\HttpMessageHeader;
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
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIpFromHeaderField
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getServerVar
     */
    public function testGetClientIpCouldBeEmpty() {
        $clientIpAggregator = new ClientIpAggregator(new HttpMessageHeader());
        $this->assertEquals(null, $clientIpAggregator->getClientIp());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getOriginalClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getForwardedClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIpFromHeaderField
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getServerVar
     */
    public function testGetClientIpFromServerValue() {
        $clientIpAggregator = new ClientIpAggregator(
            new HttpMessageHeader(),
            ["REMOTE_ADDR" => "127.0.0.1"]
        );
        $this->assertEquals("127.0.0.1", $clientIpAggregator->getClientIp());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getOriginalClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getForwardedClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIpFromHeaderField
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getServerVar
     */
    public function testGetClientIpFromHeaderField() {
        $clientIpAggregator = new ClientIpAggregator(
            new HttpMessageHeader([
                new GenericHeaderField("Client-Ip", "127.0.0.1")
            ])
        );
        $this->assertEquals("127.0.0.1", $clientIpAggregator->getClientIp());
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getOriginalClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getForwardedClientIp
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getClientIpFromHeaderField
     * @covers Brickoo\Component\Http\Aggregator\ClientIpAggregator::getServerVar
     */
    public function testGetForwardedClientIp() {
        $messageHeader = new HttpMessageHeader([
            new GenericHeaderField("X-Forwarded-For", "127.0.0.1")
        ]);
        $clientIpAggregator = new ClientIpAggregator(
            $messageHeader,
            ["REMOTE_ADDR" => "10.20.30.40"],
            ["10.20.30.40"]
        );
        $this->assertEquals("127.0.0.1", $clientIpAggregator->getClientIp());
    }

}
