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

use Brickoo\Component\Http\Header\GenericHeader,
    Brickoo\Component\Http\HttpHeaderList,
    Brickoo\Component\Http\HttpMessage,
    Brickoo\Component\Http\HttpMessageHeader,
    Brickoo\Component\Http\HttpResponse,
    Brickoo\Component\Http\HttpStatus,
    Brickoo\Component\Http\HttpVersion,
    Brickoo\Component\Http\HttpMessageBody,
    Brickoo\Component\Http\HttpResponseSender,
    PHPUnit_Framework_TestCase;

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
