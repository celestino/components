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
     * RequestInterface
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface RequestInterface extends Core\Interfaces\RequestInterface
    {

        /**
         * Lazy initialization of the Url dependency.
         * @param \Brickoo\Http\Interfaces\UrlInterface $Url the Url odependency
         * @return \Brickoo\Http\Interfaces\UrlInterface
         */
        public function Url(\Brickoo\Http\Component\Interfaces\UrlInterface $Url = null);

        /**
         * Lazy initialization of the Headers dependency.
         * @param \Brickoo\Http\Component\Interfaces\HeadersInterface $Headers the Headers dependency
         * @return \Brickoo\Http\Component\Interfaces\HeadersInterface
         */
        public function Headers(\Brickoo\Http\Component\Interfaces\HeadersInterface $Headers = null);

        /**
         * Lazy initialization of the Query dependency.
         * @param \Brickoo\Http\Component\Interfaces\QueryInterface $Query the Query dependency
         * @return \Brickoo\Http\Component\Interfaces\QueryInterface
        */
        public function Query(\Brickoo\Http\Component\Interfaces\QueryInterface $Query = null);

        /**
         * Lazy initialization of the Post dependency.
         * @param \Brickoo\Memory\Interfaces\ContainerInterface $Post the Post dependency
         * @return \Brickoo\Memory\Interfaces\ContainerInterface
         */
        public function Post(\Brickoo\Memory\Interfaces\ContainerInterface $Post = null);

        /**
         * Lazy initialization of the Files dependency.
         * @param \Brickoo\Memory\Interfaces\ContainerInterface $Files the Files dependency to inject
         * @return \Brickoo\Memory\Interfaces\ContainerInterface
         */
        public function Files(\Brickoo\Memory\Interfaces\ContainerInterface $Files = null);

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
         * Returns the http request headers.
         * @retrun string http request headers
         */
        public function toString();

    }