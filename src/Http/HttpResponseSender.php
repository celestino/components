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

use InvalidArgumentException;
use Brickoo\Component\Http\Exception\StatusCodeDoesNotAllowMessageBodyException;

/**
 * HttpResponseSender
 *
 * Implements a default response sender using php output functions.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpResponseSender {

    /* @var string */
    private $headerFunction;

    /**
     * @param string $headerFunction
     * @throws \InvalidArgumentException
     */
    public function __construct($headerFunction = "header") {
        if (!is_callable($headerFunction)) {
            throw new InvalidArgumentException("Header function must be callable.");
        }
        $this->headerFunction = $headerFunction;
    }

    /**
     * Sends the http response to the output buffer.
     * @param \Brickoo\Component\Http\HttpResponse
     * @return void
     */
    public function send(HttpResponse $response) {
        $this->checkStatusAllowsHttpMessageBodyContent($response);
        $this->sendStatus(
            $response->getStatus()->toString(),
            $response->getVersion()->toString()
        );
        $this->sendMessageHeader($response->getHeader());
        $this->sendHttpMessageBody($response->getBody());
    }

    /**
     * Sends the status headers line to the output buffer.
     * @param string $httpStatus
     * @param string $httpVersion
     * @return void
     */
    private function sendStatus($httpStatus, $httpVersion) {
        call_user_func($this->headerFunction, sprintf(
            "%s %s", $httpVersion, $httpStatus
        ));
    }

    /**
     * Send the message headers to the output buffer.
     * @param \Brickoo\Component\Http\HttpMessageHeader $messageHeader
     * @return \Brickoo\Component\Http\HttpResponseSender
     */
    private function sendMessageHeader(HttpMessageHeader $messageHeader) {
        foreach ($messageHeader as $headerField) {
            call_user_func($this->headerFunction,
                sprintf("%s: %s", $headerField->getName(), $headerField->getValue())
            );
        }
        return $this;
    }

    /**
     * Sends the body to the output buffer.
     * @param \Brickoo\Component\Http\HttpMessageBody $messageBody
     * @return \Brickoo\Component\Http\HttpResponseSender
     */
    private function sendHttpMessageBody(HttpMessageBody $messageBody) {
        echo $messageBody->getContent();
        return $this;
    }

    /**
     * Checks if the status code does allow message body content.
     * @param \Brickoo\Component\Http\HttpResponse $response
     * @throws StatusCodeDoesNotAllowMessageBodyException
     * @return \Brickoo\Component\Http\HttpResponseSender
     */
    private function checkStatusAllowsHttpMessageBodyContent(HttpResponse $response) {
        $statusCode = $response->getStatus()->getCode();
        if (($response->getBody()->getContent() != "") && $this->statusDoesNotAllowBody($statusCode)) {
            throw new StatusCodeDoesNotAllowMessageBodyException($statusCode);
        }
        return $this;
    }

    /**
     * Check if the status does not allow to have message body content.
     * @param integer $statusCode
     * @return boolean check result
     */
    private function statusDoesNotAllowBody($statusCode) {
        return (
            ($statusCode >= 100 && $statusCode <= 199)
            || ($statusCode == 204)
            || ($statusCode == 304)
        );
    }
}
