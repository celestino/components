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

    namespace Brickoo\Http\Message\Factory;

    use Brickoo\Validator\Argument;

    /**
     * Header
     *
     * Describes a factory for the http message header.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Header {

        /**
         * Creates a http message header object from the global server values.
         * @return \Brickoo\Http\Message\Header
         */
        public static function Create() {
            $headers = array();
            $includeExceptions = array("CONTENT_TYPE", "CONTENT_LENGTH");

            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) == "HTTP_") {
                    $headers[substr($key, 5)] = $value;
                }
                elseif (in_array($key, $includeExceptions)){
                    $headers[$key] = $value;
                }
            }

            if (function_exists("apache_request_headers") && ($apacheHeaders = apache_request_headers())) {
                $headers = array_merge($headers, $apacheHeaders);
            }

            return new \Brickoo\Http\Message\Header(self::NormalizeHeaders($headers));
        }

        /**
         * Creates a http message header object by extracting the header values.
         * @param string $headers the headers to extract the key/value pairs from
         * @throws \InvalidArgumentException if the argument is not valid
         * @return \Brickoo\Http\Message\Header
         */
        public static function CreateFromString($headers) {
            Argument::IsString($headers);

            $importedHeaders = array();
            $fields = explode("\r\n", preg_replace("/\x0D\x0A[\x09\x20]+/", " ", $headers));

            foreach ($fields as $field) {
                if (preg_match("/(?<name>[^:]+): (?<value>.+)/m", $field, $match)) {
                    $match["name"] = preg_replace("/(?<=^|[\x09\x20\x2D])./e", 'strtoupper("\0")', strtolower(trim($match["name"])));

                    if( isset($importedHeaders[$match["name"]])) {
                        if(! is_array($importedHeaders[$match["name"]])) {
                            $importedHeaders[$match["name"]] = array($importedHeaders[$match["name"]]);
                        }
                        $importedHeaders[$match["name"]] = array_merge($importedHeaders[$match["name"]], array($match["value"]));
                    }
                    else {
                        $importedHeaders[$match["name"]] = trim($match["value"]);
                    }
                }
            }

            return new \Brickoo\Http\Message\Header(self::NormalizeHeaders($importedHeaders));
        }

        /**
         * Normalizes the headers keys.
         * @param array $headers the headers to normalized
         * @return array the normalized headers
         */
        private static function NormalizeHeaders(array $headers) {
            $normalizedHeaders = array();

            foreach ($headers as $headerName => $headerValue) {
                $headerName = str_replace(" ", "-", ucwords(
                    strtolower(str_replace(array("_", "-"), " ", $headerName))
                ));
                $normalizedHeaders[$headerName] = $headerValue;
            }

            return $normalizedHeaders;
        }

    }