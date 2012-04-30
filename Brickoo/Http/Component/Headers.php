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

    namespace Brickoo\Http\Component;

    use Brickoo\Memory,
        Brickoo\Validator\TypeValidator;

    /**
     * Headers
     *
     * Implements methods to handle the http headers.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Headers extends Memory\Container implements Interfaces\HeadersInterface {

        /**
         * Holds the accept types supported.
         * @var array
         */
        protected $acceptTypes;

        /**
         * Returns the accept types supported by the request client.
         * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
         * @param string $neededType the type which is needed if supported
         * @return array accept types sorted by priority descening otherwise null
         */
        public function getAcceptTypes() {
            if (empty($this->acceptTypes) && ($acceptHeader = $this->get('Accept'))) {
                $this->acceptTypes = $this->getAcceptHeaderByRegex(
                    '~^(?<type>[a-z\/\+\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?~i',
                    'type',
                    $acceptHeader
                );
            }

            return $this->acceptTypes;
        }

        /**
         * Checks if the passed type is supported.
         * @param string $acceptType the accept type to check
         * @return boolean check result
         */
        public function isTypeSupported($acceptType) {
            TypeValidator::IsString($acceptType);

            return array_key_exists($acceptType, $this->getAcceptTypes());
        }

        /**
         * Holds the accept languages supported.
         * @var array
         */
        protected $acceptLanguages;

        /**
         * Returns the accept languages supported by the request client.
         * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
         * @return array the languages sorted by priority descening
         */
        public function getAcceptLanguages() {
            if (empty($this->acceptLanguages) && ($acceptLanguageHeader = $this->get('Accept-Language'))) {
                $this->acceptLanguages = $this->getAcceptHeaderByRegex(
                    '~^(?<language>[a-z\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i',
                    'language',
                    $acceptLanguageHeader
                );
            }

            return $this->acceptLanguages;
        }

        /**
         * Checks if the passed language is supported.
         * @param string $acceptLanguage the accept language to check
         * @return boolean check result
         */
        public function isLanguageSupported($acceptLanguage) {
            TypeValidator::IsString($acceptLanguage);

            return array_key_exists($acceptLanguage, $this->getAcceptLanguages());
        }

        /**
         * Holds the accept encodings supported.
         * @var array
         */
        protected $acceptEncodings;

        /**
         * Returns the accept encodings supported by the request client.
         * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.3
         * @return array the encondings sorted by priority descening
         */
        public function getAcceptEncodings() {
            if (empty($this->acceptEncodings) && ($acceptEncodingHeader = $this->get('Accept-Encoding'))) {
                $this->acceptEncodings = $this->getAcceptHeaderByRegex(
                    '~^(?<encoding>[a-z\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i',
                    'encoding',
                    $acceptEncodingHeader
                );
            }

            return $this->acceptEncodings;
        }

        /**
         * Checks if the passed encoding type is supported.
         * @param string $acceptEncoding the accept encoding type to check
         * @return boolean check result
         */
        public function isEncodingSupported($acceptEncoding) {
            TypeValidator::IsString($acceptEncoding);

            return array_key_exists($acceptEncoding, $this->getAcceptEncodings());
        }

        /**
         * Holds the accept charsets supported.
         * @var array
         */
        protected $acceptCharsets;

        /**
         * Returns the accept charsets supported by the request client.
         * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.2
         * @return array the charsets sorted by priority descening
         */
        public function getAcceptCharsets() {
            if (empty($this->acceptCharsets) && ($acceptEncodingHeader = $this->get('Accept-Charset'))) {
                $this->acceptCharsets = $this->getAcceptHeaderByRegex(
                    '~^(?<charset>[a-z0-9\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i',
                    'charset',
                    $acceptEncodingHeader
                );
            }

            return $this->acceptCharsets;
        }

        /**
         * Checks if the passed encoding type is supported.
         * @param string $acceptEncoding the accept encoding type to check
         * @return boolean check result
         */
        public function isCharsetSupported($acceptCharset) {
            TypeValidator::IsString($acceptCharset);

            return array_key_exists($acceptCharset, $this->getAcceptCharsets());
        }

        /**
         * Returns the accept header value sorted by quality.
         * @param string $regex the regular expression to use
         * @param string $keyName the key name to assign the quality to
         * @param string $acceptHeader the accept header to retireve the values from
         * @return array the result containing the header values
         */
        public function getAcceptHeaderByRegex($regex, $keyName, $acceptHeader) {
            TypeValidator::isString($regex);
            TypeValidator::isString($keyName);
            TypeValidator::isString($acceptHeader);

            $results = array();
            $fields = explode(',', $acceptHeader);

            foreach ($fields as $field) {
                if (preg_match($regex, trim($field), $matches) && isset($matches[$keyName])) {
                    $matches['quality'] = (isset($matches['quality']) ? $matches['quality'] : 1);
                    $results[trim($matches[$keyName])] = (float)$matches['quality'];
                }
            }

            arsort($results);
            return $results;
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct() {
            parent::__construct();

            $this->acceptTypes        = array();
            $this->acceptLanguages    = array();
            $this->acceptEncodings    = array();
            $this->acceptCharsets     = array();
        }

        /**
         * Imports the request http headers.
         * @return \Brickoo\Http\Component\Headers
         */
        public function importFromGlobals() {
            $this->merge($this->getRequestHeaders());
            $this->normalizeHeaders();

            return $this;
        }

        /**
         * Returns the request http headers.
         * Adds the HTTP_*** headers to the local container.
         * Looks also for the Apache headers if availale.
         * @return array the collected http headers
         */
        public function getRequestHeaders() {
            $headers = array();

            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) == 'HTTP_') {
                    $headers[substr($key, 5)] = $value;
                }
            }

            if (function_exists('apache_request_headers') && ($apacheHeaders = apache_request_headers())) {
                $headers = array_merge($headers, $apacheHeaders);
            }

            return $headers;
        }

        /**
         * Imports the headers from a string.
         * @param string $headers the headers to import
         * @return \Brickoo\Http\Component\Headers
         */
        public function importFromString($headers) {
            TypeValidator::IsString($headers);

            $importedHeader = array();
            $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $headers));

            foreach ($fields as $field) {
                if (preg_match('/(?<name>[^:]+): (?<value>.+)/m', $field, $match)) {
                    $match['name'] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match['name'])));

                    if( isset($importedHeader[$match['name']])) {
                        if(! is_array($importedHeader[$match['name']])) {
                            $importedHeader[$match['name']] = array($importedHeader[$match['name']]);
                        }
                        $importedHeader[$match['name']] = array_merge($importedHeader[$match['name']], array($match['value']));
                    }
                    else {
                        $importedHeader[$match['name']] = trim($match['value']);
                    }
                }
            }

            $this->merge($importedHeader);
            $this->normalizeHeaders();

            return $this;
        }

        /**
         * Normalizes the headers keys.
         * @return \Brickoo\Http\Component\Headers
         */
        public function normalizeHeaders() {
            $normalizedHeaders = array();

            foreach ($this->container as $headerName => $headerValue) {
                $headerName = str_replace(' ', '-', ucwords(
                    strtolower(str_replace(array('_', '-'), ' ', $headerName))
                ));
                $normalizedHeaders[$headerName] = $headerValue;
            }

            $this->container = $normalizedHeaders;

            return $this;
        }

        /**
         * Coverts headers to a headers string.
         * @return string the headers string
         */
        public function toString() {
            $headers = '';

            $this->normalizeHeaders();

            foreach ($this->container as $headerName => $headerValue) {
                $headerValues = (is_array($headerValue) ? $headerValue : array($headerValue));
                foreach ($headerValues as $value) {
                    $headerName = str_replace(' ', '-', ucwords(
                        strtolower(str_replace(array('_', '-'), ' ', $headerName))
                    ));
                    $headers .= sprintf("%s: %s\r\n", $headerName, $value);
                }
            }

            return $headers;
        }

        /**
         * Supporting casting to string.
         * @return string the headers string
         */
        public function __toString() {
            return $this->toString();
        }

    }