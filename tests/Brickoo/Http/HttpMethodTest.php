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

use Brickoo\Http\HttpMethod,
    PHPUnit_Framework_TestCase;

/**
 * HttpMethod
 *
 * Test suite for the HttpMethod class.
 * @see Brickoo\Http\HttpMethod
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpMethodTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Http\HttpMethod::__construct
     * @covers Brickoo\Http\HttpMethod::isValid
     */
    public function testConstructor() {
        $httpMethod = new HttpMethod(HttpMethod::PUT);
        $this->assertInstanceOf("\\Brickoo\\Http\HttpMethod", $httpMethod);
    }

    /**
     * @covers Brickoo\Http\HttpMethod::__construct
     * @covers Brickoo\Http\HttpMethod::isValid
     * @covers Brickoo\Http\Exception\InvalidHttpMethodException
     * @expectedException Brickoo\Http\Exception\InvalidHttpMethodException
     */
    public function testConstructorInvalidVersionThrowsException() {
        new HttpMethod("http/0.9");
    }

    /** @covers Brickoo\Http\HttpMethod::toString */
    public function testVersionToStrimg() {
        $httpMethod = new HttpMethod(HttpMethod::OPTIONS);
        $this->assertEquals(HttpMethod::OPTIONS, $httpMethod->toString());
    }

}