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

    namespace Brickoo\Http\Request\Interfaces;

    /**
     * Uri
     *
     * Describes a uniform resource identifier description.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface Uri {

        /**
         * Returns the request uri protocol scheme.
         * @return string the uri scheme
         */
        public function getScheme();

        /**
         * Returns the host name or ip address of the uri hostname.
         * @return string the host name
         */
        public function getHostname();

        /**
         * Returns the port used by the location.
         * @return integer the port
         */
        public function getPort();

        /**
         * Returns the uri path.
         * @return string the uri path
         */
        public function getPath();

        /**
         * Returns the uri path info.
         * Commonly used for routing.
         * The path info distinct from the uri path
         * by extracting the script location parts.
         * In most cases and specialy using rewrite rules
         * the value equals the uri path.
         * @return string the uri path info
         */
        public function getPathInfo();

        /**
         * Returns the uri query dependency.
         * @return \Brickoo\Http\Request\Interfaces\Query
         */
        public function getQuery();

        /**
         * Returns the string representation of the uri.
         * @return string the uri representation
         */
        public function toString();

    }