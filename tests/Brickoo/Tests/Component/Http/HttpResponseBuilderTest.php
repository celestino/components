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

use Brickoo\Component\Http\HttpResponseBuilder;
use Brickoo\Component\Http\HttpVersion;
use Brickoo\Component\Http\HttpStatus;
use Brickoo\Component\Http\HttpMessage;
use Brickoo\Component\Http\HttpMessageBody;
use Brickoo\Component\Http\HttpMessageHeader;
use Brickoo\Component\Http\Header\GenericHeader;
use PHPUnit_Framework_TestCase;

/**
 * HttpResponseBuilder
 *
 * Test suite for the HttpResponseBuilder class.
 * @see Brickoo\Component\Http\HttpResponseBuilder-
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpResponseBuilderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\HttpResponseBuilder::build
     * @covers Brickoo\Component\Http\HttpResponseBuilder::getHttpVersion
     * @covers Brickoo\Component\Http\HttpResponseBuilder::getHttpStatus
     * @covers Brickoo\Component\Http\HttpResponseBuilder::getHttpMessage
     */
    public function testBuildWithDefaultDependencies() {
        $httpResponseBuilder = new HttpResponseBuilder();
        $this->assertInstanceOf(
            "\\Brickoo\\Component\\Http\\HttpVersion",
            $httpResponseBuilder->getHttpVersion()
        );
        $this->assertInstanceOf(
            "\\Brickoo\\Component\\Http\\HttpStatus",
            $httpResponseBuilder->getHttpStatus()
        );
        $this->assertInstanceOf(
            "\\Brickoo\\Component\\Http\\HttpMessage",
            $httpResponseBuilder->getHttpMessage()
        );

        $httpResponse = $httpResponseBuilder->build();
        $this->assertInstanceOf("\\Brickoo\\Component\\Http\\HttpResponse", $httpResponse);
    }

    /**
     * @covers Brickoo\Component\Http\HttpResponseBuilder::build
     * @covers Brickoo\Component\Http\HttpResponseBuilder::setHttpVersion
     * @covers Brickoo\Component\Http\HttpResponseBuilder::setHttpStatus
     * @covers Brickoo\Component\Http\HttpResponseBuilder::setHttpMessage
     */
    public function testBuildWithInjectedDependencies() {
        $version = new HttpVersion(HttpVersion::HTTP_1_1);
        $status = new HttpStatus(HttpStatus::CODE_OK);
        $message = new HttpMessage(
            new HttpMessageHeader(),
            new HttpMessageBody()
        );

        $httpResponseBuilder = new HttpResponseBuilder();
        $httpResponseBuilder->setHttpVersion($version);
        $httpResponseBuilder->setHttpStatus($status);
        $httpResponseBuilder->setHttpMessage($message);

        $this->assertSame($version, $httpResponseBuilder->getHttpVersion());
        $this->assertSame($status, $httpResponseBuilder->getHttpStatus());
        $this->assertSame($message, $httpResponseBuilder->getHttpMessage());
    }

    /** @covers Brickoo\Component\Http\HttpResponseBuilder::getHttpMessageHeader */
    public function testGetMessageHeaderLazyInit() {
        $httpResponseBuilder = new HttpResponseBuilder();
        $this->assertInstanceOf(
            "\\Brickoo\\Component\\Http\\HttpMessageHeader",
            $httpResponseBuilder->getHttpMessageHeader()
        );
    }

    /** @covers Brickoo\Component\Http\HttpResponseBuilder::setHttpMessageHeader */
    public function testSetMessageHeaderDependency() {
        $messageHeader = new HttpMessageHeader();
        $httpResponseBuilder = new HttpResponseBuilder();
        $httpResponseBuilder->setHttpMessageHeader($messageHeader);
        $this->assertSame($messageHeader, $httpResponseBuilder->getHttpMessageHeader());
    }

    /** @covers Brickoo\Component\Http\HttpResponseBuilder::getHttpMessageBody */
    public function testGetMessageBodyLazyInit() {
        $httpResponseBuilder = new HttpResponseBuilder();
        $this->assertInstanceOf(
            "\\Brickoo\\Component\\Http\\HttpMessageBody",
            $httpResponseBuilder->getHttpMessageBody()
        );
    }

    /** @covers Brickoo\Component\Http\HttpResponseBuilder::addHttpHeader */
    public function testAddHeaderToHttpMessage() {
        $httpHeader = new GenericHeader("Unit", "Test");
        $httpResponseBuilder = new HttpResponseBuilder();
        $httpResponseBuilder->addHttpHeader($httpHeader);
        $httpMessageHeader = $httpResponseBuilder->getHttpMessageHeader();
        $this->assertEquals(1, count($httpMessageHeader));
        $this->assertSame($httpHeader, $httpMessageHeader->getHeader("Unit"));
    }

}
