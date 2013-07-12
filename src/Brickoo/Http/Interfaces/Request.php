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

    namespace Brickoo\Http\Interfaces;

    /**
     * Request
     *
     * Describes a http request and its dependencies.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface Request {

        /**
         * The http protocol versions.
         * @var string
         */
        const HTTP_VERSION_1 = "HTTP/1.0";
        const HTTP_VERSION_1_1 = "HTTP/1.1";

        /**
         * Returns the request uri dependency.
         * @return \Brickoo\Http\Request\Interfaces\Uri
         */
        public function getUri();

        /**
         * Returns the request message header dependency.
         * @return \Brickoo\Http\Message\Interfaces\Header
         */
        public function getHeader();

        /**
         * Returns the request message body dependency.
         * @return \Brickoo\Http\Message\Interfaces\Body
         */
        public function getBody();

        /**
         * Returns the request query dependency.
         * @return \Brickoo\Http\Request\Interfaces\Query
         */
        public function getQuery();

        /**
         * Returns the http request method.
         * @return string the http request method
         */
        public function getMethod();

        /**
         * Returns the http version used.
         * @return string the request http version
         */
        public function getVersion();

        /**
         * Returns a PHP global server variable value.
         * @param string $name the server variable name to retrieve the value from
         * @param mixed $defaultValue the default value to return is not exists
         * @throws \InvalidArgumentException if the variable name is not valid
         * @return string the value of the server variable otherwise mixed the default value
         */
        public function getServerVar($name, $defaultValue = null);

        /**
         * Returns the string representation of the http request.
         * @retrun string representation of the http request
         */
        public function toString();

    }