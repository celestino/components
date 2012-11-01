<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    namespace Brickoo\Http\Message;

    use Brickoo\Memory,
        Brickoo\Validator\Argument;

    /**
     * Header
     *
     * Implements a http message header container.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Header extends Memory\Container implements Interfaces\Header {

        /** @var array */
        private $acceptTypes;

        /** @var array */
        private $acceptLanguages;

        /** @var array */
        private $acceptEncodings;

        /** @var array */
        private $acceptCharsets;

        /**
         * Class constructor.
         * @param array $headers the message headers to set
         * @return void
         */
        public function __construct(array $headers = array()) {
            parent::__construct($headers);

            $this->acceptTypes = array();
            $this->acceptLanguages = array();
            $this->acceptEncodings = array();
            $this->acceptCharsets = array();
        }

        /** {@inheritDoc} */
        public function getAcceptTypes() {
            if (empty($this->acceptTypes) && ($acceptHeader = $this->get("Accept"))) {
                $this->acceptTypes = $this->getAcceptHeaderByRegex(
                    "~^(?<type>[a-z\/\+\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?~i",
                    "type",
                    $acceptHeader
                );
            }

            return $this->acceptTypes;
        }

        /** {@inheritDoc} */
        public function isTypeSupported($type) {
            Argument::IsString($type);
            return array_key_exists($type, $this->getAcceptTypes());
        }

        /** {@inheritDoc} */
        public function getAcceptLanguages() {
            if (empty($this->acceptLanguages) && ($acceptLanguageHeader = $this->get("Accept-Language"))) {
                $this->acceptLanguages = $this->getAcceptHeaderByRegex(
                    "~^(?<language>[a-z\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i",
                    "language",
                    $acceptLanguageHeader
                );
            }

            return $this->acceptLanguages;
        }

        /** {@inheritDoc} */
        public function isLanguageSupported($language) {
            Argument::IsString($language);
            return array_key_exists($language, $this->getAcceptLanguages());
        }

        /** {@inheritDoc} */
        public function getAcceptEncodings() {
            if (empty($this->acceptEncodings) && ($acceptEncodingHeader = $this->get("Accept-Encoding"))) {
                $this->acceptEncodings = $this->getAcceptHeaderByRegex(
                    "~^(?<encoding>[a-z\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i",
                    "encoding",
                    $acceptEncodingHeader
                );
            }

            return $this->acceptEncodings;
        }

        /** {@inheritDoc} */
        public function isEncodingSupported($encoding) {
            Argument::IsString($encoding);
            return array_key_exists($encoding, $this->getAcceptEncodings());
        }

        /** {@inheritDoc} */
        public function getAcceptCharsets() {
            if (empty($this->acceptCharsets) && ($acceptEncodingHeader = $this->get("Accept-Charset"))) {
                $this->acceptCharsets = $this->getAcceptHeaderByRegex(
                    "~^(?<charset>[a-z0-9\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i",
                    "charset",
                    $acceptEncodingHeader
                );
            }

            return $this->acceptCharsets;
        }

        /** {@inheritDoc} */
        public function isCharsetSupported($charset) {
            Argument::IsString($charset);
            return array_key_exists($charset, $this->getAcceptCharsets());
        }

        /**
         * Returns the accept header value sorted by quality.
         * @param string $regex the regular expression to use
         * @param string $keyName the key name to assign the quality to
         * @param string $acceptHeader the accept header to retireve the values from
         * @return array the result containing the header values
         */
        private function getAcceptHeaderByRegex($regex, $keyName, $acceptHeader) {
            $results = array();
            $fields = explode(",", $acceptHeader);

            foreach ($fields as $field) {
                if (preg_match($regex, trim($field), $matches) && isset($matches[$keyName])) {
                    $matches["quality"] = (isset($matches["quality"]) ? $matches["quality"] : 1);
                    $results[trim($matches[$keyName])] = (float)$matches["quality"];
                }
            }

            arsort($results);
            return $results;
        }

        /**
         * {@inheritDoc}
         * @param callable $callback this argument should only be used for testing purposes
         */
        public function send($callback = null) {
            $function = (is_callable($callback) ? $callback : "header");

            $header = $this->normalizeHeaders($this->toArray());
            foreach($header as $key => $value) {
                call_user_func($function, sprintf("%s: %s", $key, $value));
            }

            return $this;
        }

        /** {@inheritDoc} */
        public function toString() {
            $headerString = "";

            $header = $this->normalizeHeaders($this->toArray());
            foreach($header as $key => $value) {
                $headerString .= sprintf("%s: %s\r\n", $key, $value);
            }

            return $headerString;
        }

        /**
         * Normalizes the headers keys.
         * @param array $headers the headers to normalized
         * @return array the normalized headers
         */
        private function normalizeHeaders(array $headers) {
            $normalizedHeaders = array();

            foreach ($headers as $headerName => $headerValue) {
                $headerName = str_replace(" ", "-", ucwords(
                    strtolower(str_replace(array("_", "-"), " ", $headerName))
                ));
                $normalizedHeaders[$headerName] = $headerValue;
            }

            ksort($normalizedHeaders);
            return $normalizedHeaders;
        }

    }