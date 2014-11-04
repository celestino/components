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

namespace Brickoo\Tests\Component\Http;

use Brickoo\Component\Http\HttpMethod;
use PHPUnit_Framework_TestCase;

/**
 * HttpMethod
 *
 * Test suite for the HttpMethod class.
 * @see Brickoo\Component\Http\HttpMethod
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpMethodTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\HttpMethod::__construct
     * @covers Brickoo\Component\Http\HttpMethod::isValid
     */
    public function testConstructor() {
        $httpMethod = new HttpMethod(HttpMethod::PUT);
        $this->assertInstanceOf("\\Brickoo\\Component\\Http\\HttpMethod", $httpMethod);
    }

    /**
     * @covers Brickoo\Component\Http\HttpMethod::__construct
     * @covers Brickoo\Component\Http\HttpMethod::isValid
     * @covers Brickoo\Component\Http\Exception\InvalidHttpMethodException
     * @expectedException \Brickoo\Component\Http\Exception\InvalidHttpMethodException
     */
    public function testConstructorInvalidVersionThrowsException() {
        new HttpMethod("http/0.9");
    }

    /** @covers Brickoo\Component\Http\HttpMethod::toString */
    public function testVersionToString() {
        $httpMethod = new HttpMethod(HttpMethod::OPTIONS);
        $this->assertEquals(HttpMethod::OPTIONS, $httpMethod->toString());
    }

}
