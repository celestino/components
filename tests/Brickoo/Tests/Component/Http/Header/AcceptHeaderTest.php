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

use Brickoo\Component\Http\Header\AcceptHeader,
    PHPUnit_Framework_TestCase;

/**
 * AcceptHeaderTest
 *
 * Test suite for the AcceptHeader class.
 * @see Brickoo\Component\Http\HeaderAcceptHeader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AcceptHeaderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\Header\AcceptHeader::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidHeaderValueThrowsException() {
        new AcceptHeader(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Http\Header\AcceptHeader::__construct
     * @covers Brickoo\Component\Http\Header\AcceptHeader::setType
     * @expectedException \InvalidArgumentException
     */
    public function testSetTypeInvalidArgumentThrowsException() {
        $acceptHeader = new AcceptHeader("text/html");
        $acceptHeader->setType(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Http\Header\AcceptHeader::setType
     * @covers Brickoo\Component\Http\Header\AcceptHeader::getTypes
     * @covers Brickoo\Component\Http\Header\AcceptHeader::buildValue
     */
    public function testSetType() {
        $acceptHeader = new AcceptHeader("text/html");
        $this->assertSame($acceptHeader, $acceptHeader->setType("text/xml", 0.8));
        $this->assertEquals("text/html, text/xml;q=0.8", $acceptHeader->getValue());
    }

    /**
     * @covers Brickoo\Component\Http\Header\AcceptHeader::getTypes
     * @covers Brickoo\Component\Http\Header\AcceptHeader::getHeaderValues
     * @covers Brickoo\Component\Http\Header\AcceptHeader::getExtractedHeaderValuesByRegex
     */
    public function testgetTypes() {
        $acceptHeader = new AcceptHeader("text/html,text/xml;q=0.8");
        $this->assertEquals(["text/html" => 1, "text/xml" => 0.8], $acceptHeader->getTypes());
    }

    /**
     * @covers Brickoo\Component\Http\Header\AcceptHeader::isTypeSupported
     * @covers Brickoo\Component\Http\Header\AcceptHeader::getTypes
     * @covers Brickoo\Component\Http\Header\AcceptHeader::getHeaderValues
     * @covers Brickoo\Component\Http\Header\AcceptHeader::getExtractedHeaderValuesByRegex
     */
    public function testIsTypeSupported() {
        $acceptHeader = new AcceptHeader("text/html,text/xml;q=0.8");
        $this->assertTrue($acceptHeader->isTypeSupported("text/xml"));
        $this->assertFalse($acceptHeader->isTypeSupported("application/json"));
    }

}