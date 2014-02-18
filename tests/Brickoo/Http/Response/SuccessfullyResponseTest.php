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

namespace Brickoo\Tests\Http\Response;

use Brickoo\Http\Header\GenericHeader,
    Brickoo\Http\Response\SuccessfullyResponse,
    PHPUnit_Framework_TestCase;

/**
 * SuccessfullyResponseTest
 *
 * Test suite for the SuccessfullyResponse class.
 * @see Brickoo\Http\Response\SuccessfullyResponse
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class SuccessfullyResponseTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Http\Response\SuccessfullyResponse::__construct
     * @covers Brickoo\Http\Response\SuccessfullyResponse::createMessageHeader
     */
    public function testSuccessfullyResponseWithoutArguments() {
        $response = new SuccessfullyResponse();
        $expectedResponse = "HTTP/1.1 200 OK\r\n\r\n";
        $this->assertEquals($expectedResponse, $response->toString());
    }

    /* @covers Brickoo\Http\Response\SuccessfullyResponse::createMessageHeader */
    public function testSuccessfullyResponseWithBodyContent() {
        $content = "success";
        $response = new SuccessfullyResponse($content);
        $expectedResponse = "HTTP/1.1 200 OK\r\n\r\n";
        $expectedResponse .= $content;
        $this->assertEquals($expectedResponse, $response->toString());
    }

    /* @covers Brickoo\Http\Response\SuccessfullyResponse::createMessageHeader */
    public function testSuccessfullyResponseWithHeaders() {
        $content = "success";
        $response = new SuccessfullyResponse($content, [new GenericHeader("X-Unit-Test", "ok")]);
        $expectedResponse = "HTTP/1.1 200 OK\r\n";
        $expectedResponse .= "X-Unit-Test: ok\r\n\r\n";
        $expectedResponse .= $content;
        $this->assertEquals($expectedResponse, $response->toString());
    }

}