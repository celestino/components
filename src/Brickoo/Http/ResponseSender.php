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

use Brickoo\Http\MessageBody,
    Brickoo\Http\MessageHeader,
    Brickoo\Http\Response;

/**
 * ResponseSender
 *
 * Implements a default response sender using php output functions.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ResponseSender {

    /**
     * Sends the http response.
     * @param \Brickoo\Http\Response
     * @param callable $callback this argument should only be used for testing purposes
     * @return void
     */
    public function send(Response $response, $callback = null) {
        $this->sendStatus(
            $response->getStatus(),
            $response->getStatusPhrase(),
            $response->getVersion()->toString(),
            $callback
        );
        $this->sendMessageHeader($response->getHeader(), $callback);
        $this->sendMessageBody($response->getBody());
    }



    /**
     * Sends the message headers to the output buffer.
     * Argument added for for unit testing purposes
     * @param \Brickoo\Http\MessageHeader $messageHeader
     * @param callable $callback
     * @return void
     */
    public function sendMessageHeader(MessageHeader $messageHeader, $callback = null) {
        $function = (is_callable($callback) ? $callback : "header");
        foreach($messageHeader->toArray() as $key => $value) {
            call_user_func($function, sprintf("%s: %s", $key, $value));
        }
    }

    /**
     * Sends the body to the output buffer.
     * @param \Brickoo\Http\MessageBody $messageBody
     * @return \Brickoo\Http\MessageBody
     */
    public function sendMessageBody(MessageBody $messageBody) {
        echo $messageBody->getContent();
    }

    /**
     * Sends the status headers line to the output buffer.
     * @param integer $statusCode
     * @param string $statusPhrase
     * @param string $httpVersion
     * @param string $callback the callback to use for sending the status line
     * @return void
     */
    private function sendStatus($statusCode, $statusPhrase, $httpVersion, $callback) {
        $function = (is_callable($callback) ? $callback : "header");
        call_user_func($function, sprintf(
            "%s %d %s", $httpVersion, $statusCode, $statusPhrase
        ));
    }

}