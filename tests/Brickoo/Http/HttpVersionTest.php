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

use Brickoo\Http\HttpVersion,
    PHPUnit_Framework_TestCase;

/**
 * HttpVersion
 *
 * Test suite for the HttpVersion class.
 * @see Brickoo\Http\HttpVersion
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpVersionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Http\HttpVersion::__construct
     * @covers Brickoo\Http\HttpVersion::isValid
     */
    public function testConstructor() {
        $httpVersion = new HttpVersion(HttpVersion::HTTP_2_0);
        $this->assertInstanceOf("\\Brickoo\\Http\HttpVersion", $httpVersion);
    }

    /**
     * @covers Brickoo\Http\HttpVersion::__construct
     * @covers Brickoo\Http\HttpVersion::isValid
     * @covers Brickoo\Http\Exception\InvalidHttpVersionException
     * @expectedException Brickoo\Http\Exception\InvalidHttpVersionException
     */
    public function testConstructorInvalidVersionThrowsException() {
        new HttpVersion("v1.0.0");
    }

    /** @covers Brickoo\Http\HttpVersion::toString */
    public function testVersionToStrimg() {
        $httpVersion = new HttpVersion(HttpVersion::HTTP_2_0);
        $this->assertEquals(HttpVersion::HTTP_2_0, $httpVersion->toString());
    }

}