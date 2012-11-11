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

    namespace Brickoo\Http\Builder\Interfaces;

    /**
     * RequestBuilder
     *
     * Describes a builder with a fluent interface to create a http request object.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface RequestBuilder {

        /**
         * Sets the message header dependency.
         * @param \Brickoo\Http\Message\Interfaces\Header $Header
         * @return \Brickoo\Http\Builder\Interfaces\RequestBuilder
         */
        public function setHeader(\Brickoo\Http\Message\Interfaces\Header $Header);

        /**
         * Sets the message body dependency.
         * @param \Brickoo\Http\Message\Interfaces\Body $Body
         * @return \Brickoo\Http\Builder\Interfaces\RequestBuilder
         */
        public function setBody(\Brickoo\Http\Message\Interfaces\Body $Body);

        /**
         * Sets the http request url dependency.
         * @param \Brickoo\Http\Request\Interfaces\Uri $Uri
         * @return \Brickoo\Http\Builder\Interfaces\RequestBuilder
         */
        public function setUri(\Brickoo\Http\Request\Interfaces\Uri $Uri);

        /**
         * Sets the request method (e.g. GET, POST, PUT, etc.).
         * @param string $method the request method
         * @throws \InvalidArgumentException if the argument is not valid
         * @return \Brickoo\Http\Builder\Interfaces\RequestBuilder
         */
        public function setMethod($method);

        /**
         * Sets the http protocol version.
         * @see \Brickoo\Http\Interfaces\Request
         * @param string $version the htto protocol version
         * @throws \InvalidArgumentException if the argument is not valid
         * @return \Brickoo\Http\Builder\Interfaces\RequestBuilder
         */
        public function setVersion($version);

        /**
         * Builds the http request object with the provide or generated configuration.
         * @return \Brickoo\Http\Interfaces\Request
         */
        public function build();

    }