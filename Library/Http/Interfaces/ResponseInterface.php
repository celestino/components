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

    namespace Brickoo\Library\Http\Interfaces;

    /**
     * ResponseInterface
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface ResponseInterface
    {

        /**
         * Checks if a response header is set.
         * @param string $headerName the header name to check
         * @return boolean check result
         */
        public function hasHeader($headerName);

        /**
         * Sends the headers to the output buffer.
         * @return \Brickoo\Library\Http\Response
         */
        public function sendHeaders();

        /**
         * Returns then response protocol.
         * @return string the response protocol
         */
        public function getProtocol();

        /**
         * Sets the response protocol used.
         * @param string $protocol the response protocol
         * @return \Brickoo\Library\Http\Response
         */
        public function setProtocol($protocol);

        /**
         * Returns the status code of te response.
         * @return integer the status code
         */
        public function getStatusCode();

        /**
         * Sets the status code of the response.
         * @param integer $statusCode the status code
         * @return \Brickoo\Library\Http\Response
         */
        public function setStatusCode($statusCode);

        /**
         * Checks if the status code has the passed value.
         * If passing an array chek if the array contains the status code.
         * @param integer|array $statusCode the status code to check
         * @return boolean check result
         */
        public function hasStatusCode($statusCode);

        /**
         * Returns the status code phrase.
         * @param integer|null $statusCode the status code to return the phrase from
         * @throws Exceptions\StatusCodeUnknownException if the status code is unknowed
         * @return string the status code phrase
         */
        public function getStatusPhrase($statusCode = null);

        /**
         * Sets or adds an status code and phrase to the knowed list.
         * @param integer $statusCode the status code to add or overwrite
         * @param string $statusPhrase the phrase to bind to the status code
         * @return \Brickoo\Library\Http\Response
         */
        public function setStatusPhrase($statusCode, $statusPhrase);

        /**
         * Returns the assigned response content.
         * @return string the response content
         */
        public function getContent();

        /**
         * Sets the response content to sent.
         * @param string $content the response content
         * @return \Brickoo\Library\Http\Response
         */
        public function setContent($content);

        /**
         * Sends the response content to the output buffer.
         * @return \Brickoo\Library\Http\Response
         */
        public function sendContent();

        /**
         * Sends the output to the output buffer.
         * @return void
         */
        public function send();

        /**
         * Returns the converted response as a string.
         * @return string the converted response
         */
        public function toString();

    }