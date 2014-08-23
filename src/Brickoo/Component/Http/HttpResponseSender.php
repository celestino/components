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
        if (! is_callable($headerFunction)) {
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
        foreach($messageHeader as $headerList) {
            $this->sendHeaderList($headerList);
        }
        return $this;
    }

    /**
     * Send the headers from the header list.
     * @param HttpHeaderList $list
     * @return \Brickoo\Component\Http\HttpResponseSender
     */
    private function sendHeaderList(HttpHeaderList $list) {
        foreach ($list as $header) {
            call_user_func($this->headerFunction,
                sprintf("%s: %s", $header->getName(), $header->getValue())
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
        if ((($statusCode >= 100 && $statusCode <= 199)
                || ($statusCode == 204)
                || ($statusCode == 304))
            && ($response->getBody()->getContent() != "")
        ){
            throw new StatusCodeDoesNotAllowMessageBodyException($statusCode);
        }
        return $this;
    }
}
