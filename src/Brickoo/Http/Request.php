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

    use Brickoo\Validator\Argument;

    /**
     * Request
     *
     * Implements a http request.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Request implements Interfaces\Request {

        /** @var \Brickoo\Http\Message\Interfaces\Header */
        private $Header;

        /** @var \Brickoo\Http\Message\Interfaces\Body */
        private $Body;

        /** @var \Brickoo\Http\Request\Interfaces\Uri */
        private $Uri;

        /** @var string */
        private $method;

        /** @var string */
        private $version;

        /**
         * Class constructor.
         * @param \Brickoo\Http\Message\Interfaces\Header $Header
         * @param \Brickoo\Http\Message\Interfaces\Body $Body
         * @param \Brickoo\Http\Request\Interfaces\Uri $Uri
         * @param string|null $method the http request method
         * @param string|null $version the http request protocol version
         * @throws \InvalidArgumentException if an argument is not valid
         * @return void
         */
        public function __construct(
            \Brickoo\Http\Message\Interfaces\Header $Header,
            \Brickoo\Http\Message\Interfaces\Body $Body,
            \Brickoo\Http\Request\Interfaces\Uri $Uri,
            $method = null, $version = null
        ){
            if ($method !== null) {
                Argument::IsString($method);
            }

            if ($version !== null) {
                Argument::IsString($version);
            }

            $this->Header = $Header;
            $this->Body = $Body;
            $this->Uri = $Uri;
            $this->method = $method;
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
        public function getUri() {
            return $this->Uri;
        }

        /** {@inheritDoc} */
        public function getQuery() {
            return $this->Uri->getQuery();
        }

        /** {@inheritDoc} */
        public function getMethod() {
            if ($this->method === null) {
                $this->method = $this->getServerVar("REQUEST_METHOD", "GET");
            }
            return $this->method;
        }

        /** {@inheritDoc} */
        public function getVersion() {
            if ($this->version === null) {
                $this->version = $this->getServerVar("SERVER_PROTOCOL", self::HTTP_VERSION_1);
            }
            return $this->version;
        }

        /** {@inheritDoc} */
        public function getServerVar($name, $defaultValue = null) {
            Argument::IsString($name);

            if (! isset($_SERVER[$name])) {
                return $defaultValue;
            }

            return $_SERVER[$name];
        }

        /** {@inheritDoc} */
        public function toString() {
            $queryString = (($queryString = $this->getQuery()->toString()) ? "?". $queryString : "");

            $request  = sprintf("%s %s %s\r\n", $this->getMethod(), $this->getUri()->getPath() . $queryString, $this->getVersion());
            $request .= rtrim($this->getHeader()->toString(), "\r\n");
            $request .= "\r\n\r\n". $this->getBody()->getContent();

            return $request;
        }

     }