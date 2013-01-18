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

    namespace Brickoo\Cache\Provider;

    use Brickoo\Validator\Argument;

    /**
     * Memcache
     *
     * Provides caching operations based on Memcache.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Memcache implements Interfaces\Provider {

        /** @var \Memcache */
        private $Memcache;

        /** @var integer */
        private $cacheCompression;

        /**
         * Class constructor.
         * @param \Memcache $Memcache the Memcache dependency to inject
         * @param integer $cacheCompression the \Memcache compression flag
         * @throws \InvalidArgumentException if an argument is not valid
         * @return void
         */
        public function __construct(\Memcache $Memcache, $cacheCompression = MEMCACHE_COMPRESSED) {
            Argument::IsInteger($cacheCompression);

            $this->Memcache = $Memcache;
            $this->cacheCompression = $cacheCompression;
        }

        /** {@inheritDoc} */
        public function get($identifier) {
            Argument::IsString($identifier);
            return $this->Memcache->get($identifier);
        }

        /** {@inheritDoc} */
        public function set($identifier, $content, $lifetime) {
            Argument::IsString($identifier);
            Argument::IsInteger($lifetime);

            $this->Memcache->set($identifier, $content, $this->cacheCompression, $lifetime);
            return $this;
        }

        /** {@inheritDoc} */
        public function delete($identifier) {
            Argument::IsString($identifier);

            $this->Memcache->delete($identifier);
            return $this;
        }

        /** {@inheritDoc} */
        public function flush() {
            $this->Memcache->flush();
            return $this;
        }

        /**
         * Magic function to call other Memcache methods not implemented.
         * @param string $method the method called
         * @param array $arguments the arguments passed
         * @throws \BadMethodCallException if the method is not defined
         * @return mixed the called Memcache method result
         */
        public function __call($method, array $arguments) {
            if (! method_exists($this->Memcache, $method)) {
                throw new \BadMethodCallException(sprintf('The Memcache method `%s` is not defined.', $method));
            }

            return call_user_func_array(array($this->Memcache, $method), $arguments);
        }

    }