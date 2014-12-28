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

use Brickoo\Component\Http\Header\GenericHeader;
use Brickoo\Component\Http\HttpHeaderList;
use Brickoo\Component\Http\HttpMessage;
use Brickoo\Component\Http\HttpMessageHeader;
use Brickoo\Component\Http\HttpResponse;
use Brickoo\Component\Http\HttpStatus;
use Brickoo\Component\Http\HttpVersion;
use Brickoo\Component\Http\HttpMessageBody;
use Brickoo\Component\Http\HttpResponseSender;
use PHPUnit_Framework_TestCase;

/**
 * HttpResponseSenderTest
 *
 * Test suite for the HttpResponseSender class.
 * @see Brickoo\Component\Http\HttpResponseSender
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpResponseSenderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\HttpResponseSender::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidFunctionThrowsException() {
        new HttpResponseSender("functionNotDefined");
    }

    /**
     * @covers Brickoo\Component\Http\HttpResponseSender::__construct
     * @covers Brickoo\Component\Http\HttpResponseSender::send
     * @covers Brickoo\Component\Http\HttpResponseSender::sendStatus
     * @covers Brickoo\Component\Http\HttpResponseSender::sendMessageHeader
     * @covers Brickoo\Component\Http\HttpResponseSender::sendHeaderList
     * @covers Brickoo\Component\Http\HttpResponseSender::sendHttpMessageBody
     * @covers Brickoo\Component\Http\HttpResponseSender::checkStatusAllowsHttpMessageBodyContent
     * @covers Brickoo\Component\Http\HttpResponseSender::statusDoesNotAllowBody
     */
    public function testSendResponse() {
        $headerFunction = function(){};
        $expectedBody = "response body";

        $httpResponse = new HttpResponse(
            new HttpVersion(HttpVersion::HTTP_1_1),
            new HttpStatus(HttpStatus::CODE_OK),
            new HttpMessage(
                new HttpMessageHeader(["Location" => new HttpHeaderList([new GenericHeader("Location", "brickoo.com")])]),
                new HttpMessageBody($expectedBody)
            )
        );

        $responseSender = new HttpResponseSender($headerFunction);
        $responseSender->send($httpResponse);
        $this->expectOutputString($expectedBody);
    }

    /**
     * @covers Brickoo\Component\Http\HttpResponseSender::send
     * @covers Brickoo\Component\Http\HttpResponseSender::checkStatusAllowsHttpMessageBodyContent
     * @covers Brickoo\Component\Http\Exception\StatusCodeDoesNotAllowMessageBodyException
     * @expectedException \Brickoo\Component\Http\Exception\StatusCodeDoesNotAllowMessageBodyException
     */
    public function testStatusDoesContentNotAllowThrowsException() {

        $httpResponse = new HttpResponse(
            new HttpVersion(HttpVersion::HTTP_1_1),
            new HttpStatus(HttpStatus::CODE_NOT_MODIFIED),
            new HttpMessage(new HttpMessageHeader(), new HttpMessageBody("content allowed with this status"))
        );

        $responseSender = new HttpResponseSender();
        $responseSender->send($httpResponse);
    }

}
