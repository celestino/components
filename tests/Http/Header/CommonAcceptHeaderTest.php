<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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
     * @covers  Brickoo\Component\Http\Header\CommonAcceptHeader::getValue
     * @covers  Brickoo\Component\Http\Header\CommonAcceptHeader::setEntry
     */
    public function testBuildHeaderValue() {
        $acceptHeader = new CommonAcceptHeader("Accept", "text/html,application/xml;q=0.9");
        $this->assertSame($acceptHeader, $acceptHeader->setEntry("application/xml", 0.5));
        $this->assertEquals("text/html,application/xml;q=0.5", $acceptHeader->getValue());
    }

}
