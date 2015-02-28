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

use Brickoo\Component\Http\Header\SetCookieHeaderField;
use PHPUnit_Framework_TestCase;

/**
 * SetCookieHeaderFieldTest
 *
 * Test suite for the SetCookieHeaderField class.
 * @see Brickoo\Component\Http\Header\SetCookieHeaderField
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class SetCookieHeaderFieldTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\Header\SetCookieHeaderField::__construct
     * @covers Brickoo\Component\Http\Header\SetCookieHeaderField::setCookieValue
     */
    public function testConstructorSetCookieValue() {
        $expectedValue = "12345";
        $cookieHeader = new SetCookieHeaderField("SID", $expectedValue);
        $this->assertAttributeEquals("SID", "cookieName", $cookieHeader);
        $this->assertAttributeEquals($expectedValue, "cookieValue", $cookieHeader);
    }

    /**
     * @covers Brickoo\Component\Http\Header\SetCookieHeaderField::__construct
     * @covers Brickoo\Component\Http\Header\SetCookieHeaderField::setCookieValue
     */
    public function testSetCookieValue() {
        $expectedValue = "12345";
        $cookieHeader = new SetCookieHeaderField("SID");
        $this->assertSame($cookieHeader, $cookieHeader->setCookieValue($expectedValue));
        $this->assertAttributeEquals($expectedValue, "cookieValue", $cookieHeader);
    }

    /**
     * @covers Brickoo\Component\Http\Header\SetCookieHeaderField::setCookieValue
     * @covers Brickoo\Component\Http\Header\Exception\InvalidCookieValueException
     * @expectedException \Brickoo\Component\Http\Header\Exception\InvalidCookieValueException
     */
    public function testSetInvalidCookieValueThrowsException() {
        $cookieHeader = new SetCookieHeaderField("SID");
        $cookieHeader->setCookieValue("invalid;cookie,value ");
    }

    /** @covers Brickoo\Component\Http\Header\SetCookieHeaderField::setExpirationDate */
    public function testSetExpirationDate() {
        $cookieHeader = new SetCookieHeaderField("SID");
        $this->assertSame($cookieHeader, $cookieHeader->setExpirationDate("2014-07-10 00:00:00"));
        $this->assertAttributeNotEmpty("expires", $cookieHeader);

    }

    /** @covers Brickoo\Component\Http\Header\SetCookieHeaderField::setMaxAge */
    public function testSetMaxAge() {
        $expectedValue = 3600;
        $cookieHeader = new SetCookieHeaderField("SID");
        $this->assertSame($cookieHeader, $cookieHeader->setMaxAge($expectedValue));
        $this->assertAttributeEquals($expectedValue, "maxAge", $cookieHeader);
    }

    /** @covers Brickoo\Component\Http\Header\SetCookieHeaderField::setDomain */
    public function testSetDomain() {
        $expectedValue = "brickoo.com";
        $cookieHeader = new SetCookieHeaderField("SID");
        $this->assertSame($cookieHeader, $cookieHeader->setDomain($expectedValue));
        $this->assertAttributeEquals($expectedValue, "domain", $cookieHeader);
    }

    /** @covers Brickoo\Component\Http\Header\SetCookieHeaderField::setSecure */
    public function testSetSecure() {
        $expectedValue = true;
        $cookieHeader = new SetCookieHeaderField("SID");
        $this->assertSame($cookieHeader, $cookieHeader->setSecure($expectedValue));
        $this->assertAttributeEquals($expectedValue, "secure", $cookieHeader);
    }

    /** @covers Brickoo\Component\Http\Header\SetCookieHeaderField::setHttpOnly */
    public function testSetHttpOnly() {
        $expectedValue = true;
        $cookieHeader = new SetCookieHeaderField("SID");
        $this->assertSame($cookieHeader, $cookieHeader->setHttpOnly($expectedValue));
        $this->assertAttributeEquals($expectedValue, "httpOnly", $cookieHeader);
    }

    /**
     * @covers Brickoo\Component\Http\Header\SetCookieHeaderField::getValue
     * @covers Brickoo\Component\Http\Header\SetCookieHeaderField::getAttributesRepresentation
     * @covers Brickoo\Component\Http\Header\SetCookieHeaderField::getAttributesSet
     */
    public function testGetValue() {
        $expectedValue = "SID=12345; Max-Age=3600; Domain=brickoo.com; Secure; HttpOnly";
        $cookieHeader = new SetCookieHeaderField("SID", "12345");
        $cookieHeader->setDomain("brickoo.com")
                     ->setMaxAge(3600)
                     ->setSecure(true)
                     ->setHttpOnly(true);
        $this->assertEquals($expectedValue, $cookieHeader->getValue());
    }

}
