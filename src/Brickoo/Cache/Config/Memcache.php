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

    namespace Brickoo\Cache\Config;

    use Brickoo\Validator\TypeValidator;

    /**
     * Memcache
     *
     * Implements the configuration for a MemcacheProvider class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Memcache implements Interfaces\Memcache {

        /**
         * Holds the servers list with configurations for a Memcache object.
         * @var array
         */
        protected $servers;

        /**
         * Returns the available servers list.
         * @return array the avialable servers list
         */
        public function getServers() {
            return $this->servers;
        }

        /**
         * Adds a server configuration to the server list.
         * @param string $host the host to connect to
         * @param integer $port the port to connect to
         * @return \Brickoo\Cache\Config\Memcache
         */
        public function addServer($host, $port = 11211) {
            TypeValidator::IsStringAndNotEmpty($host);
            TypeValidator::IsInteger($port);

            $this->servers[] = array('host' => $host, 'port' => $port);

            return $this;
        }

        /**
         * Sets the Memcache servers.
         * @param array $servers the servers to set
         * @return \Brickoo\Cache\Config\Memcache
         */
        public function setServers(array $servers) {
            $this->servers = $servers;
            return $this;
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct(array $servers = array()) {
            $this->setServers($servers);
        }

        /**
         * Configures the Memcache instance using the available servers list.
         * @param \Memcache $Memcache the Memcache instance to configure
         * @return \Memcache
         */
        public function configure(\Memcache $Memcache) {
            foreach($this->getServers() as $serverConfig) {
                $Memcache->addServer($serverConfig['host'], $serverConfig['port']);
            }

            return $Memcache;
        }

    }