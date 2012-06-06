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

    use Brickoo\Core;

    /**
     * Request
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface Request extends Core\Interfaces\Request {

        /**
         * Lazy initialization of the Url dependency.
         * @param \Brickoo\Http\Interfaces\Url $Url the Url odependency
         * @return \Brickoo\Http\Interfaces\Url
         */
        public function Url(\Brickoo\Http\Component\Interfaces\Url $Url = null);

        /**
         * Lazy initialization of the Headers dependency.
         * @param \Brickoo\Http\Component\Interfaces\Headers $Headers the Headers dependency
         * @return \Brickoo\Http\Component\Interfaces\Headers
         */
        public function Headers(\Brickoo\Http\Component\Interfaces\Headers $Headers = null);

        /**
         * Lazy initialization of the Query dependency.
         * @param \Brickoo\Http\Component\Interfaces\Query $Query the Query dependency
         * @return \Brickoo\Http\Component\Interfaces\Query
        */
        public function Query(\Brickoo\Http\Component\Interfaces\Query $Query = null);

        /**
         * Lazy initialization of the Post dependency.
         * @param \Brickoo\Memory\Interfaces\Container $Post the Post dependency
         * @return \Brickoo\Memory\Interfaces\Container
         */
        public function Post(\Brickoo\Memory\Interfaces\Container $Post = null);

        /**
         * Lazy initialization of the Files dependency.
         * @param \Brickoo\Memory\Interfaces\Container $Files the Files dependency to inject
         * @return \Brickoo\Memory\Interfaces\Container
         */
        public function Files(\Brickoo\Memory\Interfaces\Container $Files = null);

        /**
         * Sets the request protocol used.
         * @param string $protocol the protocol to set.
         * @return \Brickoo\Http\Request
         */
        public function setProtocol($protocol);

        /**
         * Returns the server variable value if available.
         * @param string $name the server variable name to retrieve the value from
         * @param mixed $defaultValue the default value to return
         * @return string the value of the server variable otherwise mixed the default value
         */
        public function getServerVar($name, $defaultValue = null);

        /**
         * Returns the client ip adress.
         * Passing a list of reverse proxys,
         * a deeper look into the request headers will be made.
         * @param array $proxyServers the reverse proxys to recognize
         * @return string the client ip or null if not available
         */
        public function getClientIp(array $proxyServers = null);

        /**
         * Checks if the connection is based on https.
         * @return boolean check result
         */
        public function isSecureConnection();

        /**
         * Check if the Ajax framework has sent an identifier.
         * This is not standard and is currently just supported by few javascript frameworks.
         * @return boolean check result
         */
        public function isAjaxRequest();

        /**
         * Returns the raw input of the request.
         * @return string the raw input
         */
        public function getRawInput();

        /**
         * Returns the http request headers.
         * @retrun string http request headers
         */
        public function toString();

    }