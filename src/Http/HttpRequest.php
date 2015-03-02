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
        $queryString = (($queryString = $this->getQuery()->toString()) ? "?".$queryString : "");

        $request = sprintf("%s %s %s\r\n",
            $this->getMethod()->toString(),
            $this->getUri()->getPath().$queryString,
            $this->getVersion()->toString()
        );
        $request .= rtrim($this->getHeader()->toString(), "\r\n");
        $request .= "\r\n\r\n".$this->getBody()->getContent();

        return $request;
    }

 }
