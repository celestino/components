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

use Brickoo\Component\Http\Aggregator\HttpRequestUriAggregator;
use Brickoo\Component\Http\Aggregator\HeaderAggregator;
use Brickoo\Component\Http\Aggregator\Strategy\PhpHeaderAggregatorStrategy;
use Brickoo\Component\Http\Exception\MissingBuilderDependencyException;

/**
 * Class HttpRequestBuilder
 * Build a http request object with dependencies.
 * @package Brickoo\Component\Http
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpRequestBuilder {

    /** @var  array */
    private $serverVariables;

    /** @var \Brickoo\Component\Http\HttpMethod */
    private $method;

    /** @var \Brickoo\Component\Http\HttpVersion */
    private $version;

    /** @var \Brickoo\Component\Http\Uri */
    private $uri;

    /** @var \Brickoo\Component\Http\HttpMessage */
    private $message;

    /** @var \Brickoo\Component\Http\HttpMessageHeader */
    private $messageHeader;

    /**
     * Class constructor
     * @param array $serverVariables the PHP $_SERVER variables
     */
    public function __construct(array $serverVariables) {
        $this->serverVariables = $serverVariables;
        $this->method = null;
        $this->version = null;
        $this->uri = null;
        $this->message = null;
        $this->messageHeader = null;
    }

    /**
     * Build the http request object with
     * prepared dependencies.
     * @return \Brickoo\Component\Http\HttpRequest
     */
    public function build() {
        return new HttpRequest(
            $this->method,
            $this->version,
            $this->uri,
            $this->message
        );
    }

    /**
     * Build the request method.
     * @param null|\Brickoo\Component\Http\HttpMethod $requestMethod
     * @return \Brickoo\Component\Http\HttpRequestBuilder
     */
    public function buildRequestMethod(HttpMethod $requestMethod = null) {
        if ($requestMethod === null) {
            $requestMethod = new HttpMethod(
                $this->getServerVariable("REQUEST_METHOD", HttpMethod::GET)
            );
        }
        $this->method = $requestMethod;
        return $this;
    }

    /**
     * Build the request protocol version.
     * @param null|\Brickoo\Component\Http\HttpVersion $protocolVersion
     * @return \Brickoo\Component\Http\HttpRequestBuilder
     */
    public function buildProtocolVersion(HttpVersion $protocolVersion = null) {
        if ($protocolVersion === null) {
            $protocolVersion = new HttpVersion(
                $this->getServerVariable("SERVER_PROTOCOL", HttpVersion::HTTP_1_0)
            );
        }
        $this->version = $protocolVersion;
        return $this;
    }

    /**
     * Build the uri dependency.
     * @param null|\Brickoo\Component\Http\Uri $uri
     * @throws \Brickoo\Component\Http\Exception\MissingBuilderDependencyException
     * @return \Brickoo\Component\Http\HttpRequestBuilder
     */
    public function buildUri(Uri $uri = null) {
        if ($uri === null) {
            $this->checkMessageHeaderDependency();

            $uri = (new UriFactory)->create(
                new HttpRequestUriAggregator(
                    $this->messageHeader,
                    $this->serverVariables
                )
            );
        }
        $this->uri = $uri;
        return $this;
    }

    /**
     * Build the request message dependency.
     * @param null|\Brickoo\Component\Http\HttpMessage $message
     * @throws \Brickoo\Component\Http\Exception\MissingBuilderDependencyException
     * @return \Brickoo\Component\Http\HttpRequestBuilder
     */
    public function buildMessage(HttpMessage $message = null) {
        if ($message === null) {
            $this->checkMessageHeaderDependency();

            $message = new HttpMessage(
                $this->messageHeader,
                new HttpMessageBody(file_get_contents("php://input"))
            );
        }
        $this->message = $message;
        return $this;
    }

    /**
     * Build the request message header dependency.
     * @param null|\Brickoo\Component\Http\HttpMessageHeader $messageHeader
     * @return \Brickoo\Component\Http\HttpRequestBuilder
     */
    public function buildMessageHeader(HttpMessageHeader $messageHeader = null) {
        if ($messageHeader === null) {
            $messageHeader = new HttpMessageHeader(
                (new HeaderAggregator([], new PhpHeaderAggregatorStrategy()))->getHeaderLists()
            );
        }
        $this->messageHeader = $messageHeader;
        return $this;
    }

    /**
     * Return the server variable value.
     * @param string $variableName
     * @param null|string $defaultValue
     * @return null|string
     */
    private function getServerVariable($variableName, $defaultValue = null) {
        if (isset($this->serverVariables[$variableName])) {
            return $this->serverVariables[$variableName];
        }
        return $defaultValue;
    }

    /**
     * Check if the message header dependency is set.
     * @throws MissingBuilderDependencyException
     * @return void
     */
    private function checkMessageHeaderDependency() {
        if (! $this->messageHeader instanceof HttpMessageHeader) {
            throw new MissingBuilderDependencyException("HttpMessageHeader");
        }
    }

}
