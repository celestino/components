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

use Brickoo\Component\Http\HttpStatus;
use PHPUnit_Framework_TestCase;

/**
 * HttpStatus
 *
 * Test suite for the HttpStatus class.
 * @see Brickoo\Component\Http\HttpStatus-
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpStatusTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\HttpStatus::__construct
     * @covers Brickoo\Component\Http\HttpStatus::isValid
     */
    public function testConstructor() {
        $httpStatus = new HttpStatus(200);
        $this->assertInstanceOf("\\Brickoo\\Component\\Http\HttpStatus", $httpStatus);
    }

    /** @covers Brickoo\Component\Http\HttpStatus::getCode */
    public function testGetCode() {
        $statusCode = 200;
        $httpStatus = new HttpStatus($statusCode);
        $this->assertEquals($statusCode, $httpStatus->getCode());
    }

    /**
     * @covers Brickoo\Component\Http\HttpStatus::__construct
     * @covers Brickoo\Component\Http\HttpStatus::isValid
     * @covers Brickoo\Component\Http\Exception\InvalidHttpStatusException
     * @expectedException \Brickoo\Component\Http\Exception\InvalidHttpStatusException
     */
    public function testConstructorInvalidStatusThrowsException() {
        new HttpStatus(666);
    }

    /** @covers Brickoo\Component\Http\HttpStatus::toString */
    public function testStatusToString() {
        $httpStatus = new HttpStatus(200);
        $this->assertEquals("200 OK", $httpStatus->toString());
    }

}
