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

namespace Brickoo\Component\Http;

/**
 * Builds a http response with
 * default or configured dependencies.
 */
class HttpResponseBuilder {

    /** @var \Brickoo\Component\Http\HttpVersion */
    private $httpVersion;

    /** @var \Brickoo\Component\Http\HttpStatus */
    private $httpStatus;

    /** @var \Brickoo\Component\Http\HttpMessage */
    private $httpMessage;

    /** @var \Brickoo\Component\Http\HttpMessageHeader */
    private $httpMessageHeader;

    /** @var \Brickoo\Component\Http\HttpMessageBody */
    private $httpMessageBody;

    /**
     * Build the configured http response.
     * @return \Brickoo\Component\Http\HttpResponse
     */
    public function build() {
        return new HttpResponse(
            $this->getHttpVersion(),
            $this->getHttpStatus(),
            $this->getHttpMessage()
        );
    }

    /**
     * Set the http version dependency.
     * @param HttpVersion|null $httpVersion
     * @return \Brickoo\Component\Http\HttpResponseBuilder
     */
    public function setHttpVersion(HttpVersion $httpVersion = null) {
        $this->httpVersion = $httpVersion;
        return $this;
    }

    /**
     * Return the http version dependency.
     * @return HttpVersion
     */
    public function getHttpVersion() {
        if (!$this->httpVersion instanceof HttpVersion) {
            $this->httpVersion = new HttpVersion(HttpVersion::HTTP_1_1);
        }
        return $this->httpVersion;
    }

    /**
     * Set the  http status dependency.
     * @param HttpStatus|null $httpStatus
     * @return \Brickoo\Component\Http\HttpResponseBuilder
     */
    public function setHttpStatus(HttpStatus $httpStatus = null) {
        $this->httpStatus = $httpStatus;
        return $this;
    }

    /**
     * Return the http status dependency.
     * @return HttpStatus
     */
    public function getHttpStatus() {
        if (!$this->httpStatus instanceof HttpStatus) {
            $this->httpStatus = new HttpStatus(HttpStatus::CODE_OK);
        }
        return $this->httpStatus;
    }

    /**
     * Return the http message header dependency.
     * @return HttpMessageHeader
     */
    public function getHttpMessageHeader() {
        if ($this->httpMessageHeader === null) {
            $this->httpMessageHeader = new HttpMessageHeader();
        }
        return $this->httpMessageHeader;
    }

    /**
     * Add a http header to the response message.
     * @param HttpHeaderField $httpHeader
     * @return \Brickoo\Component\Http\HttpResponseBuilder
     */
    public function addHttpHeader(HttpHeaderField $httpHeader) {
        $this->getHttpMessageHeader()->addField($httpHeader);
        return $this;
    }

    /**
     * Set the http message header dependency.
     * @param HttpMessageHeader|null $httpMessageHeader
     * @return \Brickoo\Component\Http\HttpResponseBuilder
     */
    public function setHttpMessageHeader(HttpMessageHeader $httpMessageHeader = null) {
        $this->httpMessageHeader = $httpMessageHeader;
        return $this;
    }

    /**
     * Return the http message body dependency.
     * @return HttpMessageBody
     */
    public function getHttpMessageBody() {
        if ($this->httpMessageBody === null) {
            $this->httpMessageBody = new HttpMessageBody();
        }
        return $this->httpMessageBody;
    }

    /**
     * Return the http message dependency.
     * @return HttpMessage
     */
    public function getHttpMessage() {
        if (!$this->httpMessage instanceof HttpMessage) {
            $this->httpMessage = new HttpMessage(
                $this->getHttpMessageHeader(),
                $this->getHttpMessageBody()
            );
        }
        return $this->httpMessage;
    }

    /**
     * Set the response http message dependency.
     * @param HttpMessage|null $httpMessage
     * @return \Brickoo\Component\Http\HttpResponseBuilder
     */
    public function setHttpMessage(HttpMessage $httpMessage = null) {
        $this->httpMessage = $httpMessage;
        return $this;
    }

}
