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

    namespace Brickoo\Library\Cache\Provider;

    use Brickoo\Library\Core;
    use Brickoo\Library\Cache\Interfaces;
    use Brickoo\Library\Cache\Exceptions;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * MemcacheProvider
     *
     * Implements a memcache provider which provides caching operations.
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     */

    class MemcacheProvider implements Interfaces\CacheProviderInterface
    {

        /**
         * Holds an instance of the Memcache class.
         * @var object Memcache
         */
        protected $Memcache;

        /**
         * Returns the Memcache dependecy.
         * @throws Core\Exceptions\DependencyNotAvailableException if the Memcache dependency is not available
         * @return object Memcache dependency
         */
        public function getMemcache()
        {
            if (! $this->Memcache instanceof \Memcache)
            {
                throw new Core\Exceptions\DependencyNotAvailableException('Memcache');
            }

            return $this->Memcache;
        }

        /**
         * Injects the pre-configured Memcache instance dependency.
         * @param \Memcache $Memcache the configured Memcache instance
         * @throws Core\Exceptions\DependencyOverwriteException if trying to overwrite the dependency
         * @return object reference
         */
        public function injectMemcache(\Memcache $Memcache)
        {
            if ($this->Memcache !== null)
            {
                throw new Core\Exceptions\DependencyOverwriteException('Memcache');
            }

            $this->Memcache = $Memcache;

            return $this;
        }

        /**
         * Holds the flag for using cache compression.
         * @var boolean
         */
        protected $compression;

        /**
         * Enables cache compression (not recommended).
         * Be aware activating the comression since boolean, integer and floats can not be restored.
         * Use compression only if you are storing strings .
         * @return object reference
         */
        public function enableCompression()
        {
            $this->compression = MEMCACHE_COMPRESSED;

            return $this;
        }

        /**
         * Disables cache compression.
         * @return object refrence
         */
        public function disableCompression()
        {
            $this->compression = 0;

            return $this;
        }

        /**
         * Holds the falg configuration status.
         * @var boolean
         */
        protected $configured;

        /**
         * Checks if the Memcache isntance is configured.
         * @return boolean check result
         */
        public function isConfigured()
        {
            return ($this->configured === true);
        }

        /**
         * Injects the Memcache configuration dependency.
         * The Memcache has to be injected before, since this method use the Memcache dependecy.
         * @param \Brickoo\Library\Cache\Interfaces\MemcacheConfigInterface $MemcacheConfig the MemcacheConfig dependency
         * @throws Core\Exceptions\ConfigurationOverwriteException if trying to overwrite the configuration
         * @throws Core\Exceptions\ConfigurationEmptyException if the configuration does not contain any values
         * @return object reference
         */
        public function injectMemcacheConfig(\Brickoo\Library\Cache\Interfaces\MemcacheConfigInterface $MemcacheConfig)
        {
            if ($this->isConfigured() !== false)
            {
                throw new Core\Exceptions\DependencyOverwriteException('MemcacheConfigInterface');
            }

            if (! $servers = $MemcacheConfig->getServers())
            {
                throw new Core\Exceptions\ConfigurationMissingException('MemcacheConfig');
            }

            foreach($servers as $serverConfig)
            {
                $this->getMemcache()->addServer($serverConfig['host'], $serverConfig['port']);
            }

            $this->configured = true;

            return $this;
        }

        /**
         * Checks if the Memcache dependec is configured.
         * This is a helper method which has to be called before any Memcache operation.
         * @throws Exceptions\MemcacheNotConfiguredException if the Memcache dependency is not configured
         * @return void
         */
        protected function checkIsConfigured()
        {
            if (! $this->isConfigured())
            {
                throw new Exceptions\MemcacheNotConfiguredException();
            }
        }

        /**
        * Returns the cached content from the matching dentifier.
        * @param string $identifier the identifier to retrieve the content from
        * @return mixed the cached content
        */
        public function get($identifier)
        {
            TypeValidator::Validate('isString', array($identifier));

            $this->checkIsConfigured();

            return $this->getMemcache()->get($identifier);
        }

        /**
         * Sets the content holded by the given identifier.
         * If the identifer already exists the content will be replaced.
         * The default lifetime of the cached content is 60 seconds.
         * The maximun content length can be 1024 bytes, be sure the content does not reach the size.
         * @param string $identifier the identifier which should hold the content
         * @param mixed $content the content which should be cached
         * @param integer $lifetime the lifetime in seconds of the cached content
         * @return boolean the Memcache result value, true for success false otherwise
         */
        public function set($identifier, $content, $lifetime = 60)
        {
            TypeValidator::Validate('isString', array($identifier));
            TypeValidator::Validate('isInteger', array($lifetime));

            $this->checkIsConfigured();

            return $this->getMemcache()->set($identifier, substr($content, 0, 1024), $this->compression, $lifetime);
        }

        /**
         * Deletes the identifier and cached content.
         * @param string $identifier the identifer to remove
         * @return boolean the Memcache result value, true for success false otherwise
         */
        public function delete($identifier)
        {
            TypeValidator::Validate('isString', array($identifier));

            $this->checkIsConfigured();

            return $this->getMemcache()->delete($identifier);
        }

        /**
         * Flushes the cached values by removing (or flag as removed) any content holded.
         * @return boolean the Memcache result value, true for success false otherwise
         */
        public function flush()
        {
            $this->checkIsConfigured();

            return $this->getMemcache()->flush();
        }

        /**
         * Magic function to call other Memcache methods not implemented.
         * @param string $method the method called
         * @param array $arguments the arguments passed
         * @throws BadMethodCallException if the method is not defined
         * @return mixed Memcache method result
         */
        public function __call($method, array $arguments)
        {
            if (! method_exists($this->getMemcache(), $method))
            {
                throw new \BadMethodCallException(sprintf('The Memcache method `%s` is not defined.', $method));
            }

            $this->checkIsConfigured();

            return call_user_func_array(array($this->getMemcache(), $method), $arguments);
        }

    }

?>