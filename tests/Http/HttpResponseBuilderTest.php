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

use Brickoo\Component\Http\HttpResponseBuilder;
use Brickoo\Component\Http\HttpVersion;
use Brickoo\Component\Http\HttpStatus;
use Brickoo\Component\Http\HttpMessage;
use Brickoo\Component\Http\HttpMessageBody;
use Brickoo\Component\Http\HttpMessageHeader;
use Brickoo\Component\Http\Header\GenericHeaderField;
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
        $httpHeader = new GenericHeaderField("Unit", "Test");
        $httpResponseBuilder = new HttpResponseBuilder();
        $httpResponseBuilder->addHttpHeader($httpHeader);
        $httpMessageHeader = $httpResponseBuilder->getHttpMessageHeader();
        $this->assertEquals(1, count($httpMessageHeader));
        $this->assertSame($httpHeader, $httpMessageHeader->getField("Unit"));
    }

}
