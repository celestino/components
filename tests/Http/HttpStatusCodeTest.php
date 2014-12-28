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

namespace Brickoo\Tests\Component\Http;

use Brickoo\Component\Http\HttpStatusCode;
use PHPUnit_Framework_TestCase;

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
     * @covers Brickoo\Component\Http\Exception\StatusCodeUnknownException
     * @expectedException \Brickoo\Component\Http\Exception\StatusCodeUnknownException
     */
    public function testGetPhraseUnknownStatusCodeThrowsException() {
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
