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

namespace Brickoo\Http;

use Brickoo\Http\Method,
    Brickoo\Http\Message,
    Brickoo\Http\Uri,
    Brickoo\Http\Version;

/**
 * Request
 *
 * Implements a http request.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class Request {

    /** @var \Brickoo\Http\Message */
    private $message;

    /** @var \Brickoo\Http\Uri */
    private $uri;

    /** @var \Brickoo\Http\Method */
    private $method;

    /** @var \Brickoo\Http\Version */
    private $version;

    /**
     * Class constructor.
     * @param \Brickoo\Http\Method $method
     * @param \Brickoo\Http\Version $version
     * @param \Brickoo\Http\Uri $uri
     * @param \Brickoo\Http\Message $message
     * @return void
     */
    public function __construct(Method $method, Version $version, Uri $uri, Message $message) {
        $this->method = $method;
        $this->version = $version;
        $this->uri = $uri;
        $this->message = $message;
    }

    /**
     * Returns the request Uri.
     * @return \Brickoo\Http\Uri
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * Returns the request query part.
     * @return \Brickoo\Http\Query
     */
    public function getQuery() {
        return $this->uri->getQuery();
    }

    /**
     * Returns the http method.
     * @return \Brickoo\Http\Method
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Returns the http version.
     * @return \Brickoo\Http\Version
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Returns the http message.
     * @return \Brickoo\Http\Message
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Returns the message header.
     * @return \Brickoo\Http\MessageHeader
     */
    public function getHeader() {
        return $this->message->getHeader();
    }

    /**
     * Returns the message body.
     * @return \Brickoo\Http\MessageBody
     */
    public function getBody() {
        return $this->message->getBody();
    }

    /**
     * Retruns the request string representation.
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