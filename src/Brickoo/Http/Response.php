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

    use Brickoo\Validation\Argument;

    /**
     * Response
     *
     * Implements a http response.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Response implements Interfaces\Response {

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

        /** @var \Brickoo\Http\Message\Interfaces\Header */
        protected $Header;

        /** @var \Brickoo\Http\Message\Interfaces\Body */
        protected $Body;

        /** @var integer */
        protected $status;

        /** @var string */
        protected $version;

        /**
         * Class constructor.
         * @param \Brickoo\Http\Message\Interfaces\Header $Header
         * @param \Brickoo\Http\Message\Interfaces\Body $Body
         * @param number $status
         * @param string $body
         * @param string $version
         * @throws Exceptions\StatusCodeUnknown
         * @return void
         */
        public function __construct(
            \Brickoo\Http\Message\Interfaces\Header $Header,
            \Brickoo\Http\Message\Interfaces\Body $Body,
            $status = 200, $version = "HTTP/1.0") {
            Argument::IsInteger($status);
            Argument::IsString($version);

            if (! array_key_exists($status, $this->statusPhrases)) {
                throw new Exceptions\StatusCodeUnknown($status);
            }

            $this->Header = $Header;
            $this->Body = $Body;
            $this->status = $status;
            $this->version = $version;
        }

        /** {@inheritDoc} */
        public function getHeader() {
            return $this->Header;
        }

        /** {@inheritDoc} */
        public function getBody() {
            return $this->Body;
        }

        /** {@inheritDoc} */
        public function getStatus() {
            return $this->status;
        }

        /** {@inheritDoc} */
        public function getVersion() {
            return $this->version;
        }

        /**
         * {@inheritDoc}
         * @param callable $callback this argument should only be used for testing purposes
         */
        public function send($callback = null) {
            $this->sendStatus($callback);
            $this->getHeader()->send();
            $this->getBody()->send();

            return $this;
        }

        /** {@inheritDoc} */
        public function toString() {
            $response  = sprintf("%s %d %s\r\n", $this->version, $this->getStatus(), $this->getStatusPhrase());
            $response .= $this->getHeader()->toString();
            $response .= "\r\n" . $this->getBody()->getContent();

            return $response;
        }

        /**
         * Sends the status headers line to the output buffer.
         * @param string $callback the callback to use for sending the status line
         * @return void
         */
        private function sendStatus($callback) {
            $function = (is_callable($callback) ? $callback : "header");

            call_user_func(
                $function,
                sprintf("%s %d %s", $this->version, $this->getStatus(), $this->getStatusPhrase())
            );
        }

        /**
         * Returns the status code corresponding phrase.
         * @return string the status code phrase
         */
        private function getStatusPhrase() {
            return $this->statusPhrases[$this->status];
        }

    }