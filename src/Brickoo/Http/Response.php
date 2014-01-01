<?php

/*
 * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

use Brickoo\Http\Message,
    Brickoo\Http\Version,
    Brickoo\Http\Exception\StatusCodeUnknownException,
    Brickoo\Validation\Argument;

/**
 * Response
 *
 * Implements a http response.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class Response {

    /**
     * Holds the corresponding status code phrases.
     * 1xx: Informational - Request received, continuing process
     * 2xx: Success - The action was successfully received, understood, and accepted
     * 3xx: Redirection - Further action must be taken in order to complete the request
     * 4xx: Client Error - The request contains bad syntax or cannot be fulfilled
     * 5xx: Server Error - The server failed to fulfill an apparently valid request
     * @link http://tools.ietf.org/html/rfc2616#page-40
     * @var array
     */
    protected $statusPhrases = array(
        100 => "Continue",
        101 => "Switching Protocols",
        200 => "OK",
        201 => "Created",
        202 => "Accepted",
        203 => "Non-Authoritative Information",
        204 => "No Content",
        205 => "Reset Content",
        206 => "Partial Content",
        300 => "Multiple Choices",
        301 => "Moved Permanently",
        302 => "Found",
        303 => "See Other",
        304 => "Not Modified",
        305 => "Use Proxy",
        307 => "Temporary Redirect",
        308 => "Permanent Redirect",
        400 => "Bad Request",
        401 => "Unauthorized",
        402 => "Payment Required",
        403 => "Forbidden",
        404 => "Not Found",
        405 => "Method Not Allowed",
        406 => "Not Acceptable",
        407 => "Proxy Authentication Required",
        408 => "Request Time-out",
        409 => "Conflict",
        410 => "Gone",
        411 => "Length Required",
        412 => "Precondition Failed",
        413 => "Request Entity Too Large",
        414 => "Request-URI Too Large",
        415 => "Unsupported Media Type",
        416 => "Requested range not satisfiable",
        417 => "Expectation Failed",
        500 => "Internal Server Error",
        501 => "Not Implemented",
        502 => "Bad Gateway",
        503 => "Service Unavailable",
        504 => "Gateway Time-out",
        505 => "HTTP Version not supported"
    );

    /** @var integer */
    protected $status;

    /** @var \Brickoo\Http\version */
    protected $version;

    /** @var \Brickoo\Http\Message */
    protected $message;

    /**
     * Class constructor.
     * @param number $status
     * @param \Brickoo\Http\Version
     * @param \Brickoo\Http\Message $message
     * @throws \Brickoo\Http\Exception\StatusCodeUnknown
     * @return void
     */
    public function __construct($status, Version $version, Message $message) {
        Argument::IsInteger($status);

        if (! array_key_exists($status, $this->statusPhrases)) {
            throw new StatusCodeUnknownException($status);
        }

        $this->status = $status;
        $this->version = $version;
        $this->message = $message;
    }

    /**
     * Returns the response status number.
     * @return integer the response status number
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Returns the status code corresponding phrase.
     * @return string the status code phrase
     */
    public function getStatusPhrase() {
        return $this->statusPhrases[$this->status];
    }

    /**
     * Returns the response http version.
     * @return \Brickoo\Http\Version
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Returns the response message.
     * @return \Brickoo\Http\Message
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Returns the response message header.
     * @return \Brickoo\Http\MessageHeader
     */
    public function getHeader() {
        return $this->message->getHeader();
    }

    /**
     * Returns the response message body.
     * @return \Brickoo\Http\MessageBody
     */
    public function getBody() {
        return $this->message->getBody();
    }

    /**
     * Returns a string response representation.
     * @return string the response representation
     */
    public function toString() {
        $response  = sprintf("%s %d %s\r\n", $this->version->toString(), $this->getStatus(), $this->getStatusPhrase());
        $response .= $this->getHeader()->toString();
        $response .= "\r\n" . $this->getBody()->getContent();

        return $response;
    }

}