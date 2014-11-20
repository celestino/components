<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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
 * HttpResponse
 *
 * Implements a http response.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpResponse {

    /** @var \Brickoo\Component\Http\HttpStatus */
    protected $status;

    /** @var \Brickoo\Component\Http\HttpVersion */
    protected $version;

    /** @var \Brickoo\Component\Http\HttpMessage */
    protected $message;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Http\HttpVersion $version
     * @param \Brickoo\Component\Http\HttpStatus $status
     * @param \Brickoo\Component\Http\HttpMessage $message
     */
    public function __construct(HttpVersion $version, HttpStatus $status, HttpMessage $message) {
        $this->version = $version;
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * Returns the response status.
     * @return \Brickoo\Component\Http\HttpStatus
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Returns the response http version.
     * @return \Brickoo\Component\Http\HttpVersion
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Returns the response message.
     * @return \Brickoo\Component\Http\HttpMessage
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Returns the response message header.
     * @return \Brickoo\Component\Http\HttpMessageHeader
     */
    public function getHeader() {
        return $this->message->getHeader();
    }

    /**
     * Returns the response message body.
     * @return \Brickoo\Component\Http\HttpMessageBody
     */
    public function getBody() {
        return $this->message->getBody();
    }

    /**
     * Inject the dependencies from an other response.
     * @param HttpResponse $httpResponse
     * @return \Brickoo\Component\Http\HttpResponse
     */
    public function inject(HttpResponse $httpResponse) {
        $this->version = $httpResponse->getVersion();
        $this->status = $httpResponse->getStatus();
        $this->message = $httpResponse->getMessage();
        return $this;
    }

    /**
     * Returns a string response representation.
     * @return string the response representation
     */
    public function toString() {
        $response  = sprintf("%s %s\r\n", $this->getVersion()->toString(), $this->getStatus()->toString());
        $response .= $this->getHeader()->toString();

        $statusCode = $this->getStatus()->getCode();
        if (($statusCode > 199) && ($statusCode != 204) && ($statusCode != 304)) {
            $response .= "\r\n".$this->getBody()->getContent();
        }
        return $response;
    }

}
