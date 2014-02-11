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

namespace Tests\Brickoo\Network;

use Brickoo\Network\Client,
    Brickoo\Network\Exception\UnableToCreateHandleException,
    PHPUnit_Framework_TestCase;

/**
 * ClientTest
 *
 * Test suite for the Client class.
 * @see Brickoo\Network\Client
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ClientTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Network\Client::__construct
     * @covers Brickoo\Network\Client::open
     * @covers Brickoo\Network\Client::__destruct
     */
    public function testOpenNetworkConnection() {
        $networkClient = new Client($this->getConfigurationStub());

        try {
            $networkClient->open();
        } catch(UnableToCreateHandleException $Exception) {
            $this->markTestSkipped($Exception->getMessage());
        }

        $this->assertAttributeInternalType("resource", "handle", $networkClient);
    }

    /**
     * @covers Brickoo\Network\Client::open
     * @covers Brickoo\Network\Exception\HandleAlreadyExistsException
     * @expectedException Brickoo\Network\Exception\HandleAlreadyExistsException
     */
    public function testOpenSocketTwiceThrowsHandleExistsException() {
        $networkClient = new Client($this->getConfigurationStub());

        try {
            $networkClient->open();
        } catch(UnableToCreateHandleExceptionException $Exception) {
            return $this->markTestSkipped($Exception->getMessage());
        }

        $networkClient->open();
    }

    /**
     * @covers Brickoo\Network\Client::open
     * @covers Brickoo\Network\Exception\UnableToCreateHandleException
     * @expectedException Brickoo\Network\Exception\UnableToCreateHandleException
     */
    public function testOpenHandleException() {
        $networkClient = new Client($this->getConfigurationStub("tcp://brickoo.com:111"));
        $networkClient->open();
    }

    /**
     * @covers Brickoo\Network\Client::write
     * @covers Brickoo\Network\Client::getHandle
     * @covers Brickoo\Network\Client::hasHandle
     */
    public function testWriteToStream() {
        $data = "GET / HTTP/1.0\r\n".
                "Host: brickoo.com\r\n\r\n\r\n";

        $networkClient = new Client($this->getConfigurationStub());
        $networkClient->open();
        $this->assertEquals(strlen($data), $networkClient->write($data));
    }

    /**
     * @covers Brickoo\Network\Client::write
     * @covers Brickoo\Network\Client::getHandle
     * @covers Brickoo\Network\Exception\HandleNotAvailableException
     * @expectedException Brickoo\Network\Exception\HandleNotAvailableException
     */
    public function testWriteThrowsHandleNotAvailableException() {
        $networkClient = new Client($this->getConfigurationStub());
        $networkClient->write("failure");
    }

    /**
     * @covers Brickoo\Network\Client::read
     * @covers Brickoo\Network\Client::getHandle
     * @covers Brickoo\Network\Client::hasHandle
     */
    public function testReadFromStream() {
        $expectedData = "HTTP/1.1 200 OK";
        $data = "GET / HTTP/1.0\r\n".
                "Host: www.brickoo.com\r\n\r\n\r\n";

        $networkClient = new Client($this->getConfigurationStub());
        $networkClient->open();
        $networkClient->write($data);
        $this->assertTrue(preg_match("~^HTTP\/1\.(0|1) [0-9]{3}~", $networkClient->read(strlen($expectedData))) == 1);
    }

    /**
     * @covers Brickoo\Network\Client::read
     * @covers Brickoo\Network\Client::getHandle
     * @covers Brickoo\Network\Exception\HandleNotAvailableException
     * @expectedException Brickoo\Network\Exception\HandleNotAvailableException
     */
    public function testReadThrowsHandleNotAvailableException() {
        $networkClient = new Client($this->getConfigurationStub());
        $networkClient->read(1024);
    }

    /** @covers Brickoo\Network\Client::close */
    public function testCloseConnection() {
        $networkClient = new Client($this->getConfigurationStub());
        $networkClient->open();
        $this->assertAttributeInternalType("resource", "handle", $networkClient);
        $networkClient->close();
        $this->assertAttributeEquals(null, "handle", $networkClient);
    }

    /**
     * @covers Brickoo\Network\Client::close
     * @covers Brickoo\Network\Exception\HandleNotAvailableException
     * @expectedException Brickoo\Network\Exception\HandleNotAvailableException
     */
    public function testCloseThrowsHandleNotAvailableException() {
        $networkClient = new Client($this->getConfigurationStub());
        $networkClient->close();
    }

    /** @covers Brickoo\Network\Client::__call */
    public function testMagicMethodCall() {
        $data = "GET / HTTP/1.0\r\n".
                "Host: brickoo.com\r\n\r\n\r\n";

        $networkClient = new Client($this->getConfigurationStub());
        $networkClient->open();
        $this->assertEquals(strlen($data), $networkClient->fwrite($data));
    }

    /**
     * @covers Brickoo\Network\Client::__call
     * @expectedException BadMethodCallException
     */
    public function testCallCloseThrowsException() {
        $networkClient = new Client($this->getConfigurationStub());
        $networkClient->fclose();
    }

    /**
     * Returns a client configuration stub.
     * @return \Brickoo\Network\ClientConfiguration
     */
    private function getConfigurationStub($socketAdress = "brickoo.com:80") {
        $configuration = $this->getMockBuilder("\\Brickoo\\Network\\ClientConfiguration")
            ->disableOriginalConstructor()
            ->getMock();

        $configuration->expects($this->any())
                      ->method("getSocketAdress")
                      ->will($this->returnValue($socketAdress));
        $configuration->expects($this->any())
                      ->method("getAdress")
                      ->will($this->returnValue("brickoo.com"));
        $configuration->expects($this->any())
                      ->method("getPort")
                      ->will($this->returnValue(80));
        $configuration->expects($this->any())
                      ->method("getConnectionType")
                      ->will($this->returnValue(STREAM_CLIENT_CONNECT));
        $configuration->expects($this->any())
                      ->method("getContextOptions")
                      ->will($this->returnValue([]));
        $configuration->expects($this->any())
                      ->method("getConnectionTimeout")
                      ->will($this->returnValue(10));
        return $configuration;
    }

}