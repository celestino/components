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

use Brickoo\Component\Http\Header\CommonAcceptHeader;
use PHPUnit_Framework_TestCase;

/**
 * CommonAcceptHeaderTest
 *
 * Test suite for the CommonAcceptHeader class.
 * @see Brickoo\Component\Http\Header\CommonAcceptHeader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class CommonAcceptHeaderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers  Brickoo\Component\Http\Header\CommonAcceptHeader::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidHeaderValueThrowsException() {
        new CommonAcceptHeader("Accept", ["wrongType"]);
    }

    /**
     * @covers  Brickoo\Component\Http\Header\CommonAcceptHeader::__construct
     * @covers  Brickoo\Component\Http\Header\CommonAcceptHeader::getEntries
     * @covers  Brickoo\Component\Http\Header\CommonAcceptHeader::getHeaderListEntries
     * @covers  Brickoo\Component\Http\Header\CommonAcceptHeader::getExtractedHeaderValuesByRegex
     */
    public function testGetEntries() {
        $acceptHeader = new CommonAcceptHeader("Accept", "text/html,application/xml;q=0.9");
        $this->assertEquals(["text/html" => 1.0, "application/xml" => 0.9], $acceptHeader->getEntries());
    }

    /**
     * @covers  Brickoo\Component\Http\Header\CommonAcceptHeader::setEntry
     * @covers  Brickoo\Component\Http\Header\CommonAcceptHeader::getEntries
     */
    public function testOverrideEntry() {
        $acceptHeader = new CommonAcceptHeader("Accept", "text/html,application/xml;q=0.9");
        $this->assertSame($acceptHeader, $acceptHeader->setEntry("application/xml", 0.5));
        $this->assertEquals(["text/html" => 1.0, "application/xml" => 0.5], $acceptHeader->getEntries());
    }

    /**
     * @covers  Brickoo\Component\Http\Header\CommonAcceptHeader::isSupported
     * @covers  Brickoo\Component\Http\Header\CommonAcceptHeader::getEntries
     */
    public function testIsCharsetSupported() {
        $acceptHeader = new CommonAcceptHeader("Accept", "text/html,application/xml;q=0.9");
        $this->assertTrue($acceptHeader->isSupported("text/html"));
        $this->assertFalse($acceptHeader->isSupported("text/xml"));
    }

    /**
     * @covers  Brickoo\Component\Http\Header\CommonHeaderStructure::getValue
     * @covers  Brickoo\Component\Http\Header\CommonAcceptHeader::setEntry
     * @covers  Brickoo\Component\Http\Header\CommonAcceptHeader::build
     */
    public function testBuildHeaderValue() {
        $acceptHeader = new CommonAcceptHeader("Accept", "text/html,application/xml;q=0.9");
        $this->assertSame($acceptHeader, $acceptHeader->setEntry("application/xml", 0.5));
        $this->assertEquals("text/html,application/xml;q=0.5", $acceptHeader->getValue());
    }

}
