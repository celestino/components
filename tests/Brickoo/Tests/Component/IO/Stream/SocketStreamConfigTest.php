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

use Brickoo\Component\IO\Stream\SocketStreamConfig,
    PHPUnit_Framework_TestCase;

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
