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
     * UrlInterface
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface UrlInterface
    {

        /**
         * Lazy initialization of the Http\Request instance.
         * Returns the Http\Request instance.
         * @return object Http\Request implementing the Http\Interfaces\RequestInterface
         */
        public function getRequest();

        /**
         * Injects the Http\Request dependency.
         * @param \Brickoo\Library\Http\Interfaces\RequestInterface $Request the Http\Request instance
         * @throws Core\Exceptions\DependencyOverwriteException if trying to overwrite the dependecy
         * @return object reference
         */
        public function injectRequest(\Brickoo\Library\Http\Interfaces\RequestInterface $Request);

        /**
        * Returns the request scheme.
        * @return string
        */
        public function getScheme();

        /**
         * Returns the host name or ip adress of the host.
         * @return string
         */
        public function getHost();

        /**
         * Returns the port handling request.
         * @return string
         */
        public function getPort();

        /**
         * Returns the available segments.
         * @return array the request URL segments
         */
        public function getSegments();

        /**
         * Returns the segment value of the passed position.
         * @param integer $position the position of the segment to return
         * @throws RuntimeException if the position is out of range
         * @return string the segment value
         */
        public function getSegment($position);

        /**
         * Returns the request query if available.
         * @return string the request query
         */
        public function getRequestQuery();

        /**
         * Returns the request path.
         * @return string path
         */
        public function getRequestPath();

        /**
         * Returns the request url.
         * @param boolean $withPort return the url including port
         * @return string
         */
        public function getRequestUrl($withPort = false);

        /**
         * Resets the object properties.
         * @return object reference
         */
        public function reset();

    }

?>