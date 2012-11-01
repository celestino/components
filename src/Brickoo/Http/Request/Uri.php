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

    namespace Brickoo\Http\Request;

    use Brickoo\Validator\Argument;

    /**
     * Uri
     *
     * Implements a uniform resource identifier description.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Uri implements Interfaces\Uri {

        /** @var string */
        private $scheme;

        /** @var string */
        private $hostname;

        /** @var integer */
        private $port;

        /** @var string */
        private $path;

        /** @var \Brickoo\Http\Request\Interfaces\Query */
        private $Query;

        /**
         * Class constructor.
         * @param string $scheme the url protocol scheme
         * @param string $hostname the url hostname
         * @param integer $port the url port number
         * @param string $path the url path
         * @param \Brickoo\Http\Request\Interfaces\Query $Query
         * @return void
         */
        public function __construct($scheme, $hostname, $port, $path, \Brickoo\Http\Request\Interfaces\Query $Query) {
            Argument::IsString($scheme);
            Argument::IsString($hostname);
            Argument::IsInteger($port);
            Argument::IsString($path);

            $this->scheme = $scheme;
            $this->hostname = $hostname;
            $this->port = $port;
            $this->path = $path;
            $this->Query = $Query;
        }

        /** {@inheritDoc} */
        public function getScheme() {
            return $this->scheme;
        }

        /** {@inheritDoc} */
        public function getHostname() {
            return $this->hostname;
        }

        /** {@inheritDoc} */
        public function getPort() {
            return $this->port;
        }

        /** {@inheritDoc} */
        public function getPath() {
            return $this->path;
        }

        /** {@inheritDoc} */
        public function getQuery() {
            return $this->Query;
        }

        /** {@inheritDoc} */
        public function toString() {
            $host = sprintf("%s://%s", $this->getScheme(), $this->getHostname());

            if ($this->getPort() != 80 && $this->getPort() != 443) {
                $host .= sprintf(":%s", $this->getPort());
            }

            if ($queryString = $this->getQuery()->toString()) {
                $queryString = "?". $queryString;
            }

            return  $host . $this->getPath() . $queryString;
        }

     }