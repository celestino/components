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
        if (! $this->httpVersion instanceof HttpVersion) {
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
        if (! $this->httpStatus instanceof HttpStatus) {
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
     * @param HttpHeader $httpHeader
     * @return \Brickoo\Component\Http\HttpResponseBuilder
     */
    public function addHttpHeader(HttpHeader $httpHeader) {
        $this->getHttpMessageHeader()->addHeader($httpHeader);
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
        if(! $this->httpMessage instanceof HttpMessage) {
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
