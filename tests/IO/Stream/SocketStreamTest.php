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

namespace Brickoo\Tests\Component\IO\Stream;

use Brickoo\Component\IO\Stream\SocketStream,
    Brickoo\Component\IO\Stream\SocketStreamConfig,
    PHPUnit_Framework_TestCase;

/**
 * SocketStreamTest
 *
 * Test suite for the SocketStream class.
 * @see Brickoo\Component\IO\Stream\SocketStream
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class SocketStreamTest extends PHPUnit_Framework_TestCase {

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
     * @param array $context
     * @return SocketStreamConfig
     */
    private function getSocketStreamConfigurationFixture($context = array()) {
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
