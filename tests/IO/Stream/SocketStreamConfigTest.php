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

use Brickoo\Component\IO\Stream\SocketStreamConfig;
use PHPUnit_Framework_TestCase;

/**
 * SocketStreamConfigTest
 *
 * Test suite for the SocketStreamConfig class.
 * @see Brickoo\Component\IO\Stream\SocketStreamConfig
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class SocketStreamConfigTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\IO\Stream\SocketStreamConfig::__construct */
    public function testCreateSocketStreamConfig() {
        $socketStreamConfig = new SocketStreamConfig(
            "tcp://brickoo.com", 80, 30, STREAM_CLIENT_CONNECT, array()
        );
        $this->assertInstanceOf("\\Brickoo\\Component\\IO\\Stream\\SocketStreamConfig", $socketStreamConfig);
    }

    /** @covers Brickoo\Component\IO\Stream\SocketStreamConfig::getAddress */
    public function testGetAddress() {
        $address = "tcp://brickoo.com";
        $socketStreamConfig = new SocketStreamConfig($address, 80);
        $this->assertEquals($address, $socketStreamConfig->getAddress());
    }

    /** @covers Brickoo\Component\IO\Stream\SocketStreamConfig::getPort */
    public function testGetPort() {
        $port = 8080;
        $socketStreamConfig = new SocketStreamConfig("tcp://brickoo.com", $port);
        $this->assertEquals($port, $socketStreamConfig->getPort());
    }

    /** @covers Brickoo\Component\IO\Stream\SocketStreamConfig::getSocketAddress */
    public function testGetSocketAddress() {
        $address = "tcp://brickoo.com";
        $port = 8080;
        $socketStreamConfig = new SocketStreamConfig($address, $port);
        $this->assertEquals($address.":".$port, $socketStreamConfig->getSocketAddress());
    }

    /** @covers Brickoo\Component\IO\Stream\SocketStreamConfig::getConnectionTimeout */
    public function testGetConnectionTimeout() {
        $timeout = 60;
        $socketStreamConfig = new SocketStreamConfig("tcp://brickoo.com", 80, $timeout);
        $this->assertEquals($timeout, $socketStreamConfig->getConnectionTimeout());
    }

    /** @covers Brickoo\Component\IO\Stream\SocketStreamConfig::getConnectionType */
    public function testGetConnectionType() {
        $connectionType = STREAM_CLIENT_PERSISTENT;
        $socketStreamConfig = new SocketStreamConfig("tcp://brickoo", 80, 30, $connectionType);
        $this->assertEquals($connectionType, $socketStreamConfig->getConnectionType());
    }

    /** @covers Brickoo\Component\IO\Stream\SocketStreamConfig::getContext */
    public function testGetContext() {
        $context = ["http" => [
            "method" => "GET",
            "header"=>"Host: google.com\r\n".
                "Accept-language: en\r\n",
            "max_redirects" => "5",
            "ignore_errors" => "1"
        ]];
        $socketStreamConfig = new SocketStreamConfig("tcp://brickoo", 80, 30, STREAM_CLIENT_CONNECT, $context);
        $this->assertEquals($context, $socketStreamConfig->getContext());
    }

}
