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

    use Brickoo\Memory;

    /**
     * Header
     *
     * Implements a http message header container.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Header extends Memory\Container implements Interfaces\Header {

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