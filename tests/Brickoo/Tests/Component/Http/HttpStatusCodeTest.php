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

use Brickoo\Component\Http\HttpStatusCode,
    PHPUnit_Framework_TestCase;

/**
 * HttpStatusCode
 *
 * Test suite for the HttpStatusCode class.
 * @see Brickoo\Component\Http\HttpStatusCode-
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpStatusCodeTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\HttpStatusCode::getPhrase
     * @covers Brickoo\Component\Http\HttpStatusCode::hasPhrase
     */
    public function testGetPhrase() {
        $httpStatusCode = new HttpStatusCode();
        $this->assertEquals("OK", $httpStatusCode->getPhrase(HttpStatusCode::CODE_OK));
    }

    /**
     * @covers Brickoo\Component\Http\HttpStatusCode::getPhrase
     * @covers Brickoo\Component\Http\HttpStatusCode::hasPhrase
     * @covers Brickoo\Component\Http\Exception\StatusCodeUnkownException
     * @expectedException Brickoo\Component\Http\Exception\StatusCodeUnkownException
     */
    public function testGetPhraseUnknownStatusCodeTHrowsException() {
        $httpStatusCode = new HttpStatusCode();
        $httpStatusCode->getPhrase(666);
    }

    /** @covers Brickoo\Component\Http\HttpStatusCode::hasPhrase */
    public function testHasPhrase() {
        $httpStatusCode = new HttpStatusCode();
        $this->assertTrue($httpStatusCode->hasPhrase(HttpStatusCode::CODE_OK));
        $this->assertFalse($httpStatusCode->hasPhrase(666));
    }

}