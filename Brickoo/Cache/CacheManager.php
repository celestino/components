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

    namespace Brickoo\Cache;

    use Brickoo\Validator\TypeValidator;

    /**
     * CacheManager
     *
     * Implements the caching routines with an added CacheProvider.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class CacheManager implements Interfaces\CacheManagerInterface
    {

        /**
         * Holds the cache provider implementing the Cache\Interfaces\CacheProviderInterface.
         * @var \Brickoo\Cache\Provider\Interfaces\CacheProviderInterface
         */
        protected $_CacheProvider;

        /**
         * Returns the CacheProvider dependency.
         * @return \Brickoo\Cache\Provider\Interfaces\CacheProviderInterface
         */
        public function CacheProvider()
        {
            return $this->_CacheProvider;
        }

        /**
         * Holds the class dependencies.
         * @var array
         */
        protected $dependencies;

        /**
         * Returns the dependency holded, created or overwritten.
         * @param string $name the name of the dependency
         * @param string $interface the interface which has to be implemented by the dependency
         * @param callback $callback the callback to create a new dependency
         * @param object $Dependency the dependecy to inject
         * @return object CacheManager if overwritten otherwise the dependency
         */
        protected function getDependency($name, $interface, $callback, $Dependency = null)
        {
            if ($Dependency instanceof $interface) {
                $this->dependencies[$name] = $Dependency;
                return $this;
            }
            elseif ((! isset($this->dependencies[$name])) || (! $this->dependencies[$name] instanceof $interface)) {
                $this->dependencies[$name] = call_user_func($callback, $this);
            }
            return $this->dependencies[$name];
        }

        /**
         * Lazy initialization of the LocalCache.
         * @param \Brickoo\Cache\Interfaces\LocalCacheInterface $LocalCache the LocalCache dependecy to inject
         * @return \Brickoo\Cache\Interfaces\LocalCacheInterface
         */
        public function LocalCache(\Brickoo\Cache\Interfaces\LocalCacheInterface $LocalCache = null)
        {
            return $this->getDependency(
                'LocalCache',
                '\Brickoo\Cache\Interfaces\LocalCacheInterface',
                function(){return new LocalCache();},
                $LocalCache
            );
        }

        /**
         * Flushes the LocalCache cached content.
         * This method can be called with the local cache disabled
         * to make sure the local cache can be cleaned up after disabling it.
         * @return void
         */
        public function flushLocalCache()
        {
            $this->LocalCache()->flush();
        }

        /**
         * Holds the flag to enable the local cache.
         * @var boolean
         */
        protected $enableLocalCache;

        /**
         * Enables the use of the local cache.
         * This provides less call to the cache provider for indentifiers already loaded.
         * @return \Brickoo\Cache\CacheManager
         */
        public function enableLocalCache()
        {
            $this->enableLocalCache = true;

            return $this;
        }

        /**
         * Disables the use of the local cache.
         * If the local cache is disabled, all call to the same indentifier will be loaded
         * through the cache provider. This could be a performance decreasement.
         * @return \Brickoo\Cache\CacheManager
         */
        public function disableLocalCache()
        {
            $this->enableLocalCache = false;

            return $this;
        }

        /**
         * Checks if the local cache is enabled.
         * @return boolean check result
         */
        public function isLocalCacheEnabled()
        {
            return $this->enableLocalCache;
        }

        /**
         * Injects the CacheProvide dependency.
         * Enables the local cache for duplicate get calls to the same identifier.
         * @param \Brickoo\Cache\Provider\Interfaces\CacheProviderInterface $CacheProvider the CacheProvider dependency
         * @return void
         */
        public function __construct(\Brickoo\Cache\Provider\Interfaces\CacheProviderInterface $CacheProvider)
        {
            $this->_CacheProvider      = $CacheProvider;
            $this->enableLocalCache    = true;
            $this->dependencies        = array();
        }

        /**
         * Return a cached content assigned to the identifier.
         * If the cached content is not available, it will be retrieved
         * with the given callback and cached again with the lifetime expiration.
         * This method should also work with a closure function.
         * @param string $identifier the cache identifier of the content
         * @param callable $callback the callback to call if the content is not cached
         * @param array $arguments the arguments to pass to the callback
         * @param integer $lifetime the lifetime in seconds for the cached content to set
         */
        public function getByCallback($identifier, $callback, array $arguments, $lifetime)
        {
            TypeValidator::IsString($identifier);
            TypeValidator::IsInteger($lifetime);

            if (! $cacheContent = $this->get($identifier)) {
                $this->set($identifier, ($cacheContent = call_user_func_array($callback, $arguments)), $lifetime);
            }

            return $cacheContent;
        }

        /**
         * Returns the cached content using the identifier.
         * Stores the content into the local cache.
         * @param string $identifier the identifier to retrieve the content from
         * @return mixed the cached content
         */
        public function get($identifier)
        {
            TypeValidator::IsString($identifier);

            if ($this->isLocalCacheEnabled() && $this->LocalCache()->has($identifier)) {
                return $this->LocalCache()->get($identifier);
            }

           if (($cachedContent = $this->CacheProvider()->get($identifier)) && $this->isLocalCacheEnabled()) {
               $this->LocalCache()->set($identifier, $cachedContent);
           }

            return $cachedContent;
        }

        /**
         * Sets a content to be cached under the given identifier.
         * If the identifier already exists, the content will be replaced.
         * Stores the content into the local cache.
         * @param string $identifier the identifier which holds the content
         * @param mixed $content the content to cache
         * @param integer $lifetime the lifetime in seconds of the cached content
         * @return \Brickoo\Cache\CacheManager
         */
        public function set($identifier, $content, $lifetime)
        {
            TypeValidator::IsString($identifier);
            TypeValidator::IsInteger($lifetime);

            if ($this->isLocalCacheEnabled()) {
                $this->LocalCache()->set($identifier, $content);
            }

            $this->CacheProvider()->set($identifier, $content, $lifetime);

            return $this;
        }

        /**
         * Deletes the cached content which is holded by the identifier.
         * Removes the local cached content.
         * @param string $identifier the identifier which holds the content
         * @return \Brickoo\Cache\CacheManager
         */
        public function delete($identifier)
        {
            TypeValidator::IsString($identifier);

            if ($this->isLocalCacheEnabled() && $this->LocalCache()->has($identifier)) {
                $this->LocalCache()->remove($identifier);
            }

            $this->CacheProvider()->delete($identifier);

            return $this;
        }

        /**
         * Flushes the cache which (flag as removed) removes the cached content.
         * Flushes the local cache.
         * @return \Brickoo\Cache\CacheManager
         */
        public function flush()
        {
            if ($this->isLocalCacheEnabled()) {
                $this->LocalCache()->flush();
            }

            $this->CacheProvider()->flush();

            return $this;
        }

    }