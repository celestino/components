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
 * HttpRequest
 *
 * Implements a http request.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpRequest {

    /** @var \Brickoo\Component\Http\HttpMessage */
    private $message;

    /** @var \Brickoo\Component\Http\Uri */
    private $uri;

    /** @var \Brickoo\Component\Http\HttpMethod */
    private $method;

    /** @var \Brickoo\Component\Http\HttpVersion */
    private $version;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Http\HttpMethod $method
     * @param \Brickoo\Component\Http\HttpVersion $version
     * @param \Brickoo\Component\Http\Uri $uri
     * @param \Brickoo\Component\Http\HttpMessage $message
     */
    public function __construct(HttpMethod $method, HttpVersion $version, Uri $uri, HttpMessage $message) {
        $this->method = $method;
        $this->version = $version;
        $this->uri = $uri;
        $this->message = $message;
    }

    /**
     * Returns the request Uri.
     * @return \Brickoo\Component\Http\Uri
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * Returns the request query part.
     * @return \Brickoo\Component\Http\UriQuery
     */
    public function getQuery() {
        return $this->uri->getQuery();
    }

    /**
     * Returns the http method.
     * @return \Brickoo\Component\Http\HttpMethod
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Returns the http version.
     * @return \Brickoo\Component\Http\HttpVersion
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Returns the http message.
     * @return \Brickoo\Component\Http\HttpMessage
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Returns the message header.
     * @return \Brickoo\Component\Http\HttpMessageHeader
     */
    public function getHeader() {
        return $this->message->getHeader();
    }

    /**
     * Returns the message body.
     * @return \Brickoo\Component\Http\HttpMessageBody
     */
    public function getBody() {
        return $this->message->getBody();
    }

    /**
     * Returns the request string representation.
     * @return string the request representation
     */
    public function toString() {
        $queryString = (($queryString = $this->getQuery()->toString()) ? "?". $queryString : "");

        $request  = sprintf("%s %s %s\r\n",
            $this->getMethod()->toString(),
            $this->getUri()->getPath() . $queryString,
            $this->getVersion()->toString()
        );
        $request .= rtrim($this->getHeader()->toString(), "\r\n");
        $request .= "\r\n\r\n". $this->getBody()->getContent();

        return $request;
    }

 }
