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

namespace Brickoo\Tests\Component\IO\Stream;

use Brickoo\Component\IO\Stream\SocketStream;
use Brickoo\Component\IO\Stream\SocketStreamConfig;
use PHPUnit_Framework_TestCase;

/**
 * SocketStreamTest
 *
 * Test suite for the SocketStream class.
 * @see Brickoo\Component\IO\Stream\SocketStream
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class SocketStreamTest extends PHPUnit_Framework_TestCase {

    /** {@inheritDoc} */
    public function setUp() {
        $resource = @fsockopen("www.google.com", 80, $errorNumber, $errorMessage, 5);
        if (is_resource($resource)) {
            return fclose($resource);
        }
        $this->markTestSkipped("Unable to connect to host.");
    }

    /**
     * @covers Brickoo\Component\IO\Stream\SocketStream::__construct
     * @covers Brickoo\Component\IO\Stream\SocketStream::open
     * @covers Brickoo\Component\IO\Stream\SocketStream::getConfiguration
     */
    public function testOpenSocketStream() {
        $socketStream = new SocketStream($this->getSocketStreamConfigurationFixture());
        $this->assertInternalType("resource", $socketStream->open());
    }

    /**
     * @covers Brickoo\Component\IO\Stream\SocketStream::open
     * @covers Brickoo\Component\IO\Stream\SocketStream::hasResource
     */
    public function testOpenSocketStreamTwiceReturnsSameResource() {
        $socketStream = new SocketStream($this->getSocketStreamConfigurationFixture());
        $this->assertInternalType("resource", ($resource_1 = $socketStream->open()));
        $this->assertInternalType("resource", ($resource_2 = $socketStream->open()));
        $this->assertSame($resource_1, $resource_2);
    }

    /**
     * @covers Brickoo\Component\IO\Stream\SocketStream::open
     * @covers \Brickoo\Component\IO\Stream\Exception\UnableToOpenStreamException
     * @expectedException \Brickoo\Component\IO\Stream\Exception\UnableToOpenStreamException
     */
    public function testOpenSocketStreamFailureThrowsException() {
        $socketStream = new SocketStream(new SocketStreamConfig("tcp://localhost", 12345));
        $socketStream->open();
    }

    /**
     * @covers Brickoo\Component\IO\Stream\SocketStream::close
     * @covers Brickoo\Component\IO\Stream\SocketStream::open
     * @covers Brickoo\Component\IO\Stream\SocketStream::hasResource
     */
    public function testOpenAndCloseSocketStream() {
        $socketStream = new SocketStream($this->getSocketStreamConfigurationFixture());
        $this->assertInternalType("resource", $socketStream->open());
        $socketStream->close();
        $this->assertAttributeEquals(null, "resource", $socketStream);
    }

    /**
     * @covers Brickoo\Component\IO\Stream\SocketStream::close
     * @covers Brickoo\Component\IO\Stream\SocketStream::open
     * @covers Brickoo\Component\IO\Stream\SocketStream::__destruct
     */
    public function testDestructionClosesSocketStream() {
        $socketStream = new SocketStream($this->getSocketStreamConfigurationFixture());
        $this->assertInternalType("resource", $socketStream->open());
        unset($socketStream);
        $socketStream = null;
    }

    /** @covers Brickoo\Component\IO\Stream\SocketStream::reconfigure */
    public function testSocketStreamReconfiguration() {
        $config_1 = $this->getSocketStreamConfigurationFixture();
        $config_2 = $this->getSocketStreamConfigurationFixture();
        $socketStream = new SocketStream($config_1);
        $this->assertSame($config_1, $socketStream->getConfiguration());
        $this->assertSame($socketStream, $socketStream->reconfigure($config_2));
        $this->assertSame($config_2, $socketStream->getConfiguration());
    }

    /**
     * Returns a socket stream configuration fixture.
     * @return \Brickoo\Component\IO\Stream\SocketStreamConfig
     */
    private function getSocketStreamConfigurationFixture() {
        $context = ["http" => [
            "method" => "GET",
            "header"=>"Host: google.com\r\n".
                "Accept-language: en\r\n",
            "max_redirects" => "5",
            "ignore_errors" => "1"
        ]];
        return  new SocketStreamConfig("tcp://google.com", 80, 30, STREAM_CLIENT_CONNECT, $context);
    }

}
