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

namespace Brickoo\Tests\Component\Http\Response;

use Brickoo\Component\Http\Header\GenericHeaderField;
use Brickoo\Component\Http\Response\SuccessfullyResponse;
use PHPUnit_Framework_TestCase;

/**
 * SuccessfullyResponseTest
 *
 * Test suite for the SuccessfullyResponse class.
 * @see Brickoo\Component\Http\Response\SuccessfullyResponse
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class SuccessfullyResponseTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\Response\SuccessfullyResponse::__construct
     * @covers Brickoo\Component\Http\Response\SuccessfullyResponse::createMessageHeader
     */
    public function testSuccessfullyResponseWithoutArguments() {
        $response = new SuccessfullyResponse();
        $expectedResponse = "HTTP/1.1 200 OK\r\n\r\n";
        $this->assertEquals($expectedResponse, $response->toString());
    }

    /** @covers Brickoo\Component\Http\Response\SuccessfullyResponse::createMessageHeader */
    public function testSuccessfullyResponseWithBodyContent() {
        $content = "success";
        $response = new SuccessfullyResponse($content);
        $expectedResponse = "HTTP/1.1 200 OK\r\n\r\n";
        $expectedResponse .= $content;
        $this->assertEquals($expectedResponse, $response->toString());
    }

    /** @covers Brickoo\Component\Http\Response\SuccessfullyResponse::createMessageHeader */
    public function testSuccessfullyResponseWithHeaderFields() {
        $content = "success";
        $response = new SuccessfullyResponse($content, [new GenericHeaderField("X-Unit-Test", "ok")]);
        $expectedResponse = "HTTP/1.1 200 OK\r\n";
        $expectedResponse .= "X-Unit-Test: ok\r\n\r\n";
        $expectedResponse .= $content;
        $this->assertEquals($expectedResponse, $response->toString());
    }

}
