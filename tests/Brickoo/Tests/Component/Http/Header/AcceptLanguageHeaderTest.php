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

use Brickoo\Component\Http\Header\AcceptLanguageHeader,
    PHPUnit_Framework_TestCase;

/**
 * AcceptLanguageHeaderTest
 *
 * Test suite for the AcceptLanguageHeader class.
 * @see Brickoo\Component\Http\Header\AcceptLanguageHeader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AcceptLanguageHeaderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers  Brickoo\Component\Http\Header\AcceptLanguageHeader::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidHeaderValueThrowsException() {
        new AcceptLanguageHeader(["wrongType"]);
    }

    /**
     * @covers  Brickoo\Component\Http\Header\AcceptLanguageHeader::__construct
     * @covers  Brickoo\Component\Http\Header\AcceptLanguageHeader::getLanguages
     * @covers  Brickoo\Component\Http\Header\CommonAcceptRoutines::getHeaderValues
     * @covers  Brickoo\Component\Http\Header\CommonAcceptRoutines::getExtractedHeaderValuesByRegex
     */
    public function testGetLanguages() {
        $acceptLanguageHeader = new AcceptLanguageHeader("gzip,deflate;q=0.9");
        $this->assertEquals(["gzip" => 1.0, "deflate" => 0.9], $acceptLanguageHeader->getLanguages());
    }

    /**
     * @covers  Brickoo\Component\Http\Header\AcceptLanguageHeader::setLanguage
     * @covers  Brickoo\Component\Http\Header\AcceptLanguageHeader::getLanguages
     * @covers  Brickoo\Component\Http\Header\CommonAcceptRoutines::getHeaderValues
     * @covers  Brickoo\Component\Http\Header\CommonAcceptRoutines::getExtractedHeaderValuesByRegex
     * @covers  Brickoo\Component\Http\Header\CommonAcceptRoutines::buildValue
     */
    public function testSetLanguagesToExistingOnes() {
        $acceptLanguageHeader = new AcceptLanguageHeader("gzip");
        $this->assertEquals(["gzip" => 1.0], $acceptLanguageHeader->getLanguages());
        $this->assertSame($acceptLanguageHeader, $acceptLanguageHeader->setLanguage("deflate", 0.9));
        $this->assertEquals(["gzip" => 1.0, "deflate" => 0.9], $acceptLanguageHeader->getLanguages());
        $this->assertEquals("gzip, deflate;q=0.9", $acceptLanguageHeader->getValue());
    }

    /**
     * @covers  Brickoo\Component\Http\Header\AcceptLanguageHeader::isLanguageSupported
     * @covers  Brickoo\Component\Http\Header\AcceptLanguageHeader::getLanguages
     * @covers  Brickoo\Component\Http\Header\CommonAcceptRoutines::getHeaderValues
     * @covers  Brickoo\Component\Http\Header\CommonAcceptRoutines::getExtractedHeaderValuesByRegex
     */
    public function testIsLanguageSupported() {
        $acceptLanguageHeader = new AcceptLanguageHeader("gzip");
        $this->assertTrue($acceptLanguageHeader->isLanguageSupported("gzip"));
        $this->assertFalse($acceptLanguageHeader->isLanguageSupported("deflate"));
    }

}