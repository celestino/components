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

    namespace Brickoo\Cache\Provider;

    use Brickoo\Validator\TypeValidator;

    /**
     * Memcache
     *
     * Provides caching operations based on Memcache.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Memcache implements Interfaces\Provider {

        /**
         * Holds an instance of the Memcache class.
         * @var \Memcache
         */
        protected $_Memcache;

        /**
         * Returns the Memcache dependecy.
         * @return \Memcache
         */
        public function Memcache() {
            return $this->_Memcache;
        }

        /**
         * Holds the flag for using cache compression.
         * @var integer
         */
        protected $compression;

        /**
         * Enables cache compression (not recommended).
         * Be aware activating the comression since boolean, integer and floats can not be restored.
         * Use compression only if you are storing strings .
         * @return \Brickoo\Cache\Provider\Memcache
         */
        public function enableCompression() {
            $this->compression = MEMCACHE_COMPRESSED;

            return $this;
        }

        /**
         * Disables cache compression.
         * @return object refrence
         */
        public function disableCompression() {
            $this->compression = 0;

            return $this;
        }

        /**
         * Injects the Memcache dependency.
         * Initializes the class properties.
         * @param \Memcache $Memcache the Memcache dependency
         * @return void
         */
        public function __construct(\Memcache $Memcache) {
            $this->_Memcache       = $Memcache;
            $this->compression     = 0;
        }

        /**
        * Returns the cached content from the matching dentifier.
        * @param string $identifier the identifier to retrieve the content from
        * @return mixed the cached content
        */
        public function get($identifier) {
            TypeValidator::IsStringAndNotEmpty($identifier);

            return $this->Memcache()->get($identifier);
        }

        /**
         * Sets the content holded by the given identifier.
         * If the identifer already exists the content will be replaced.
         * The default lifetime of the cached content is 60 seconds.
         * @param string $identifier the identifier which should hold the content
         * @param mixed $content the content which should be cached
         * @param integer $lifetime the lifetime in seconds of the cached content
         * @return boolean the Memcache result value, true for success false otherwise
         */
        public function set($identifier, $content, $lifetime = 60) {
            TypeValidator::IsStringAndNotEmpty($identifier);
            TypeValidator::IsInteger($lifetime);

            return $this->Memcache()->set($identifier, $content, $this->compression, $lifetime);
        }

        /**
         * Deletes the identifier and cached content.
         * @param string $identifier the identifer to remove
         * @return boolean the Memcache result value, true for success false otherwise
         */
        public function delete($identifier) {
            TypeValidator::IsStringAndNotEmpty($identifier);

            return $this->Memcache()->delete($identifier);
        }

        /**
         * Flushes the cached values by removing (or flag as removed) any content holded.
         * @return boolean the Memcache result value, true for success false otherwise
         */
        public function flush() {
            return $this->Memcache()->flush();
        }

        /**
         * Magic function to call other Memcache methods not implemented.
         * @param string $method the method called
         * @param array $arguments the arguments passed
         * @throws BadMethodCallException if the method is not defined
         * @return mixed Memcache method result
         */
        public function __call($method, array $arguments) {
            if (! method_exists($this->Memcache(), $method)) {
                throw new \BadMethodCallException(sprintf('The Memcache method `%s` is not defined.', $method));
            }

            return call_user_func_array(array($this->Memcache(), $method), $arguments);
        }

    }