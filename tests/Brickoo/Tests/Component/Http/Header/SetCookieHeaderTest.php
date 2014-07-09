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

use Brickoo\Component\Http\Header\SetCookieHeader,
    PHPUnit_Framework_TestCase;

/**
 * SetCookieHeaderTest
 *
 * Test suite for the SetCookieHeader class.
 * @see Brickoo\Component\Http\Header\SetCookieHeader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class SetCookieHeaderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\Header\SetCookieHeader::__construct
     * @covers Brickoo\Component\Http\Header\SetCookieHeader::setValue
     */
    public function testConstructorSetCookieValue() {
        $expectedValue = "12345";
        $cookieHeader = new SetCookieHeader("SID", $expectedValue);
        $this->assertAttributeEquals($expectedValue, "value", $cookieHeader);
    }

    /**
     * @covers Brickoo\Component\Http\Header\SetCookieHeader::__construct
     * @covers Brickoo\Component\Http\Header\SetCookieHeader::setValue
     */
    public function testSetCookieValue() {
        $expectedValue = "12345";
        $cookieHeader = new SetCookieHeader("SID");
        $this->assertSame($cookieHeader, $cookieHeader->setValue($expectedValue));
        $this->assertAttributeEquals($expectedValue, "value", $cookieHeader);
    }

    /**
     * @covers Brickoo\Component\Http\Header\SetCookieHeader::setValue
     * @covers Brickoo\Component\Http\Header\Exception\InvalidCookieValueException
     * @expectedException \Brickoo\Component\Http\Header\Exception\InvalidCookieValueException
     */
    public function testSetInvalidCookieValueThrowsException() {
        $cookieHeader = new SetCookieHeader("SID");
        $cookieHeader->setValue("invalid;cookie,value ");
    }

    /** @covers Brickoo\Component\Http\Header\SetCookieHeader::setExpirationDate */
    public function testSetExpirationDate() {
        $cookieHeader = new SetCookieHeader("SID");
        $this->assertSame($cookieHeader, $cookieHeader->setExpirationDate("2014-07-10 00:00:00"));
        $this->assertAttributeNotEmpty("expires", $cookieHeader);

    }

    /** @covers Brickoo\Component\Http\Header\SetCookieHeader::setMaxAge */
    public function testSetMaxAge() {
        $expectedValue = 3600;
        $cookieHeader = new SetCookieHeader("SID");
        $this->assertSame($cookieHeader, $cookieHeader->setMaxAge($expectedValue));
        $this->assertAttributeEquals($expectedValue, "maxAge", $cookieHeader);
    }

    /** @covers Brickoo\Component\Http\Header\SetCookieHeader::setDomain */
    public function testSetDomain() {
        $expectedValue = "brickoo.com";
        $cookieHeader = new SetCookieHeader("SID");
        $this->assertSame($cookieHeader, $cookieHeader->setDomain($expectedValue));
        $this->assertAttributeEquals($expectedValue, "domain", $cookieHeader);
    }

    /** @covers Brickoo\Component\Http\Header\SetCookieHeader::setSecure */
    public function testSetSecure() {
        $expectedValue = true;
        $cookieHeader = new SetCookieHeader("SID");
        $this->assertSame($cookieHeader, $cookieHeader->setSecure($expectedValue));
        $this->assertAttributeEquals($expectedValue, "secure", $cookieHeader);
    }

    /** @covers Brickoo\Component\Http\Header\SetCookieHeader::setHttpOnly */
    public function testSetHttpOnly() {
        $expectedValue = true;
        $cookieHeader = new SetCookieHeader("SID");
        $this->assertSame($cookieHeader, $cookieHeader->setHttpOnly($expectedValue));
        $this->assertAttributeEquals($expectedValue, "httpOnly", $cookieHeader);
    }

    /**
     * @covers Brickoo\Component\Http\Header\SetCookieHeader::getValue
     * @covers Brickoo\Component\Http\Header\SetCookieHeader::getAttributes
     */
    public function testGetValue() {
        $expectedValue = "SID=12345; Max-Age=3600; Domain=brickoo.com; Secure; HttpOnly";
        $cookieHeader = new SetCookieHeader("SID", "12345");
        $cookieHeader->setDomain("brickoo.com")
                     ->setMaxAge(3600)
                     ->setSecure(true)
                     ->setHttpOnly(true);
        $this->assertEquals($expectedValue, $cookieHeader->getValue());
    }

}
