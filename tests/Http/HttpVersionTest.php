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

use Brickoo\Component\Http\HttpVersion;
use PHPUnit_Framework_TestCase;

/**
 * HttpVersion
 *
 * Test suite for the HttpVersion class.
 * @see Brickoo\Component\Http\HttpVersion
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpVersionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\HttpVersion::__construct
     * @covers Brickoo\Component\Http\HttpVersion::isValid
     */
    public function testConstructor() {
        $httpVersion = new HttpVersion(HttpVersion::HTTP_2_0);
        $this->assertInstanceOf("\\Brickoo\\Component\\Http\HttpVersion", $httpVersion);
    }

    /**
     * @covers Brickoo\Component\Http\HttpVersion::__construct
     * @covers Brickoo\Component\Http\HttpVersion::isValid
     * @covers Brickoo\Component\Http\Exception\InvalidHttpVersionException
     * @expectedException \Brickoo\Component\Http\Exception\InvalidHttpVersionException
     */
    public function testConstructorInvalidVersionThrowsException() {
        new HttpVersion("v1.0.0");
    }

    /** @covers Brickoo\Component\Http\HttpVersion::toString */
    public function testVersionToString() {
        $httpVersion = new HttpVersion(HttpVersion::HTTP_2_0);
        $this->assertEquals(HttpVersion::HTTP_2_0, $httpVersion->toString());
    }

}
