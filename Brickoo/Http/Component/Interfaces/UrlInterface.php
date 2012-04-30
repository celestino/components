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

    namespace Brickoo\Http\Component\Interfaces;

    /**
     * UrlInterface
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface UrlInterface {

        /**
         * Returns the request scheme.
         * @throws \UnexpectedValueException if the the scheme is not set
         * @return string the url scheme
         */
        public function getScheme();

        /**
         * Returns the host name or ip adress of the host.
         * @throws \UnexpectedValueException if the host is not set
         * @return string
         */
        public function getHost();

        /**
         * Returns the port handling request.
         * @return string the url included port or null if not set
         */
        public function getPort();

        /**
         * Returns the url query string.
         * @return string the url query string or null if not set
         */
        public function getQuery();

        /**
         * Returns the url path.
         * @throws \UnexpectedException if the path is not set
         * @return string the url path
         */
        public function getPath();

        /**
         * Returns the request format.
         * @return string the request format or null if not set
         */
        public function getFormat();

        /**
         * Imports the url parts from string.
         * @param string $url the url to import from
         * @return \Brickoo\Http\Component\Url
         */
        public function importFromString($url);

        /**
         * Imports the request configuration.
         * @return void
         */
        public function importFromGlobals();

        /**
         * Returns the full url.
         * @param boolean $withHost flag to include the host
         * @return string the full url
         */
        public function toString($withHost = true);

    }