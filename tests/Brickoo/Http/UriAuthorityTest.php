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

use Brickoo\Http\UriAuthority,
    PHPUnit_Framework_TestCase;

/**
 * UriAuthorityTest
 *
 * Test suite for the UriAuthority class.
 * @see Brickoo\Http\UriAuthority
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class UriAuthorityTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Http\UriAuthority::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidHostnameThrowsException() {
        new UriAuthority(["wrongType"]);
    }

    /**
     * @covers Brickoo\Http\UriAuthority::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidPortNumberThrowsException() {
        new UriAuthority("example.org", ["wrongType"]);
    }

    /**
     * @covers Brickoo\Http\UriAuthority::__construct
     * @covers Brickoo\Http\UriAuthority::getHostname
     */
    public function testGetHostname() {
        $hostname = "example.org";
        $uriAuthority = new UriAuthority($hostname);
        $this->assertEquals($hostname, $uriAuthority->getHostname());
    }

    /** @covers Brickoo\Http\UriAuthority::getPortNumber */
    public function testGetPortnumber() {
        $portNumber = 8080;
        $uriAuthority = new UriAuthority("example.org", $portNumber);
        $this->assertEquals($portNumber, $uriAuthority->getPortNumber());
    }

    /** @covers Brickoo\Http\UriAuthority::toString */
    public function testToString() {
        $hostname = "example.org";
        $portNumber = 8080;
        $uriAuthority = new UriAuthority($hostname, $portNumber);
        $this->assertEquals(sprintf("%s:%s", $hostname, $portNumber), $uriAuthority->toString());
    }

}