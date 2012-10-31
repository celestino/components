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

    namespace Brickoo\Http\Interfaces;

    /**
     * Response
     *
     * Describes a http response.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface Response {

        /**
         * Returns the response message header dependency.
         * @return \Brickoo\Http\Message\Interfaces\Header
         */
        public function getHeader();

        /**
         * Returns the response message body dependency.
         * @return \Brickoo\Http\Message\Interfaces\Body
         */
        public function getBody();

        /**
         * Returns the response status.
         * @return integer the response status
         */
        public function getStatus();

        /**
         * Returns the response protocol version.
         * @return string the response protocol version
         */
        public function getVersion();

        /**
         * Sends the response content to the output buffer.
         * Triggers also the sending method of the response headers.
         * @return \Brickoo\Http\Interfaces\Response
         */
        public function send();

        /**
         * Returns the string representation of the response.
         * @return string the representation of the response
         */
        public function toString();

    }