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

use Brickoo\Component\Http\HttpMessage;
use Brickoo\Component\Http\HttpMessageBody;
use Brickoo\Component\Http\HttpMessageHeader;
use Brickoo\Component\Http\HttpMethod;
use Brickoo\Component\Http\HttpRequestBuilder;
use Brickoo\Component\Http\HttpVersion;
use PHPUnit_Framework_TestCase;

/**
 * HttpRequestBuilderTest
 *
 * Test suite for the HttpRequestBuilder class.
 * @see Brickoo\Component\Http\HttpRequestBuilder
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpRequestBuilderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\HttpRequestBuilder::__construct
     * @covers Brickoo\Component\Http\HttpRequestBuilder::buildRequestMethod
     * @covers Brickoo\Component\Http\HttpRequestBuilder::buildProtocolVersion
     * @covers Brickoo\Component\Http\HttpRequestBuilder::buildMessageHeader
     * @covers Brickoo\Component\Http\HttpRequestBuilder::buildMessage
     * @covers Brickoo\Component\Http\HttpRequestBuilder::buildUri
     * @covers Brickoo\Component\Http\HttpRequestBuilder::build
     * @covers Brickoo\Component\Http\HttpRequestBuilder::getServerVariable
     */
    public function testHttpRequestBuild() {
        $serverVars = [
            "SERVER_PROTOCOL" => "HTTP/1.0"
        ];
        $builder = new HttpRequestBuilder($serverVars);
        $builder->buildRequestMethod()
                ->buildProtocolVersion()
                ->buildMessageHeader()
                ->buildMessage()
                ->buildUri();
        $this->assertInstanceOf(
            "\\Brickoo\\Component\\Http\\HttpRequest",
            $builder->build()
        );
    }

    /** @covers Brickoo\Component\Http\HttpRequestBuilder::buildRequestMethod */
    public function testSetHttpMethodDependency() {
        $httpMethod = new HttpMethod(HttpMethod::GET);
        $builder = new HttpRequestBuilder([]);
        $this->assertSame($builder, $builder->buildRequestMethod($httpMethod));
        $this->assertAttributeSame($httpMethod, "method", $builder);
    }

    /** @covers Brickoo\Component\Http\HttpRequestBuilder::buildProtocolVersion */
    public function testSetHttpProtocolVersionDependency() {
        $httpProtocol = new HttpVersion(HttpVersion::HTTP_1_0);
        $builder = new HttpRequestBuilder([]);
        $this->assertSame($builder, $builder->buildProtocolVersion($httpProtocol));
        $this->assertAttributeSame($httpProtocol, "version", $builder);
    }

    /** @covers Brickoo\Component\Http\HttpRequestBuilder::buildMessageHeader */
    public function testSetHttpMessageHeaderDependency() {
        $httpMessageHeader = new HttpMessageHeader();
        $builder = new HttpRequestBuilder([]);
        $this->assertSame($builder, $builder->buildMessageHeader($httpMessageHeader));
        $this->assertAttributeSame($httpMessageHeader, "messageHeader", $builder);
    }

    /** @covers Brickoo\Component\Http\HttpRequestBuilder::buildMessage */
    public function testSetHttpMessageDependency() {
        $httpMessage = new HttpMessage(new HttpMessageHeader(), new HttpMessageBody());
        $builder = new HttpRequestBuilder([]);
        $this->assertSame($builder, $builder->buildMessage($httpMessage));
        $this->assertAttributeSame($httpMessage, "message", $builder);
    }

    /**
     * @covers Brickoo\Component\Http\HttpRequestBuilder::buildMessage
     * @covers Brickoo\Component\Http\Exception\MissingBuilderDependencyException
     * @expectedException \Brickoo\Component\Http\Exception\MissingBuilderDependencyException
     */
    public function testBuildHttpMessageThrowsMissingMessageHeaderException() {
        $builder = new HttpRequestBuilder([]);
        $builder->buildMessage();
    }

    /** @covers Brickoo\Component\Http\HttpRequestBuilder::buildUri */
    public function testSetUriDependency() {
        $uri = $this->getMockBuilder("\\Brickoo\\Component\\Http\\Uri")
            ->disableOriginalConstructor()
            ->getMock();
        $builder = new HttpRequestBuilder([]);
        $this->assertSame($builder, $builder->buildUri($uri));
        $this->assertAttributeSame($uri, "uri", $builder);
    }

    /**
     * @covers Brickoo\Component\Http\HttpRequestBuilder::buildUri
     * @covers Brickoo\Component\Http\Exception\MissingBuilderDependencyException
     * @expectedException \Brickoo\Component\Http\Exception\MissingBuilderDependencyException
     */
    public function testBuildHttpUriThrowsMissingMessageHeaderException() {
        $builder = new HttpRequestBuilder([]);
        $builder->buildUri();
    }

}
