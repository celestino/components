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

use Brickoo\Network\ClientConfiguration,
    PHPUnit_Framework_TestCase;

/**
 * ClientConfigurationTest
 *
 * Test suite for the ClientConfiguration class.
 * @see Brickoo\Network\ClientConfiguration
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ClientConfigurationTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Network\ClientConfiguration::__construct
     * @covers Brickoo\Network\ClientConfiguration::getAdress
     */
    public function testGetAddress() {
        $configuration = $this->getConfigurationFixture();
        $this->assertEquals("brickoo.com", $configuration->getAdress());
    }

    /** @covers Brickoo\Network\ClientConfiguration::getPort */
    public function testGetPort() {
        $configuration = $this->getConfigurationFixture();
        $this->assertEquals(80, $configuration->getPort());
    }

    /** @covers Brickoo\Network\ClientConfiguration::getSocketAdress */
    public function testGetSocketAddress() {
        $configuration = $this->getConfigurationFixture();
        $this->assertEquals("brickoo.com:80", $configuration->getSocketAdress());
    }

    /** @covers Brickoo\Network\ClientConfiguration::getConnectionType */
    public function testGetConnectionType() {
        $configuration = $this->getConfigurationFixture();
        $this->assertEquals(STREAM_CLIENT_CONNECT, $configuration->getConnectionType());
    }

    /** @covers Brickoo\Network\ClientConfiguration::getConnectionTimeout */
    public function testGetConnectionTimeout() {
        $configuration = $this->getConfigurationFixture();
        $this->assertEquals(30, $configuration->getConnectionTimeout());
    }

    /** @covers Brickoo\Network\ClientConfiguration::getContextOptions */
    public function testGetContextOptions() {
        $configuration = $this->getConfigurationFixture();
        $this->assertEquals(["http" => ["method" => "GET"]], $configuration->getContextOptions());
    }

    /**
     * Returns a client configuration fixture.
     * @return \Brickoo\Network\ClientConfiguration
     */
    private function getConfigurationFixture() {
        return new ClientConfiguration(
            "brickoo.com", 80, 30, STREAM_CLIENT_CONNECT, ["http" => ["method" => "GET"]]
        );
    }

}