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

    namespace Brickoo\Library\Cache;

    use Brickoo\Library\Core;
    use Brickoo\Library\Cache\Interfaces;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * CacheManager
     *
     * Implements the caching with an added CacheHandler.
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     */

    class CacheManager implements Interfaces\CacheManagerInterface
    {

        /**
         * Holds the cache handler implementing the Cache\Interfaces\CacheHandlerInterface.
         * @var Brickoo\Library\Cache\Interfaces\CacheHandlerInterface
         */
        protected $CacheHandler;

        /**
         * Returns the CacheHandler dependency.
         * @throws Core\Exceptions\DependencyNotAvailableException if the dependency is not available
         * @return object the CacheHandler implementing the Cache\Interfaces\CacheHandlerInterface
         */
        public function getCacheHandler()
        {
            if (! $this->CacheHandler instanceof Interfaces\CacheHandlerInterface)
            {
                throw new Core\Exceptions\DependencyNotAvailableException('Cache\Interfaces\CacheHandlerInterface');
            }

            return $this->CacheHandler;
        }

        /**
         * Injects the CacheHandler dependency to use.
         * @param \Brickoo\Library\Cache\Interfaces\CacheHandlerInterface $CacheHandler the Cachehandler dependecy
         * @throws Core\Exceptions\DependencyOverwriteException if trying to overwrite the dependency
         * @return object reference
         */
        public function injectCacheHandler(\Brickoo\Library\Cache\Interfaces\CacheHandlerInterface $CacheHandler)
        {
            if ($this->CacheHandler !== null)
            {
                throw new Core\Exceptions\DependencyOverwriteException('Cache\Interfaces\CacheHandlerInterface');
            }

            $this->CacheHandler = $CacheHandler;

            return $this;
        }

        /**
         * Holds an instance of the LocalCache implementing the Cache\Interface\LocalCacheInterface.
         * @var Brickoo\Library\Cache\Interfaces\LocalCacheInterface
         */
        protected $LocalCache;

        /**
         * Lazy initialization of the LocalCache.
         * Returns the LocalCache dependency.
         * @return object the LocalCache implementing the Cache\Interfaces\LocalCacheInterface
         */
        public function getLocalCache()
        {
            if (! $this->LocalCache instanceof Interfaces\LocalCacheInterface)
            {
                $this->injectLocalCache(new LocalCache());
            }

            return $this->LocalCache;
        }

        /**
        * Injects the LocalCache dependency to use.
        * @param \Brickoo\Library\Cache\Interfaces\LocalCacheInterface $LocalCache the LocalCache dependecy
        * @throws Core\Exceptions\DependencyOverwriteException if trying to overwrite the dependency
        * @return object reference
        */
        public function injectLocalCache(\Brickoo\Library\Cache\Interfaces\LocalCacheInterface $LocalCache)
        {
            if ($this->LocalCache !== null)
            {
                throw new Core\Exceptions\DependencyOverwriteException('Cache\Interfaces\LocalCacheInterface');
            }

            $this->LocalCache = $LocalCache;

            return $this;
        }

        /**
         * Return a cached content assigned to the identifier.
         * If the cached content is not available, it will be retrieved
         * with the given callback and cached again with the lifetime expiration.
         * This method should also work with a closure function.
         * @param string $identifier the cache identifier of the content
         * @param callable $callback hte callback to callif the content is not cached
         * @param array $arguments the arguments to pass to the callback
         * @param integer $lifetime the lifetime in seconds for the cached content to set
         */
        public function getCacheCallback($identifier, $callback, array $arguments, $lifetime)
        {
            TypeValidator::Validate('isString', array($identifier));
            TypeValidator::Validate('isInteger', array($lifetime), TypeValidator::FLAG_INTEGER_CAN_NOT_BE_ZERO);

            if (! $cacheContent = $this->get($identifier))
            {
                $cacheContent = call_user_func_array($callback, $arguments);

                $this->add($identifier, $cacheContent, $lifetime);
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
            TypeValidator::Validate('isString', array($identifier));

            $LocalCache = $this->getLocalCache();

            if ($LocalCache->has($identifier))
            {
                return $LocalCache->get($identifier);
            }

           if ($cachedContent = $this->getCacheHandler()->get($identifier))
           {
               $LocalCache->add($identifier, $cachedContent);
           }

            return $cachedContent;
        }

        /**
         * Adds a content to be cached under the given identifier.
         * Stores the content into the local cache.
         * @param string $identifier the identifier which holds the content
         * @param mixed $content the content to cache
         * @param integer $lifetime the lifetime in seconds of the cached content
         * @return object reference
         */
        public function add($identifier, $content, $lifetime)
        {
            TypeValidator::Validate('isString', array($identifier));
            TypeValidator::Validate('isInteger', array($lifetime));

            $this->getLocalCache()->add($identifier, $content);

            $this->getCacheHandler()->add($identifier, $content, $lifetime);

            return $this;
        }

        /**
         * Deletes the cached content which is holded by the identifier.
         * Removes the local cached content.
         * @param string $identifier the identifier which holds the content
         * @return object reference
         */
        public function delete($identifier)
        {
            TypeValidator::Validate('isString', array($identifier));

            $LocalCache = $this->getLocalCache();

            if ($LocalCache->has($identifier))
            {
                $LocalCache->remove($identifier);
            }

            $this->getCacheHandler()->delete($identifier);

            return $this;
        }

        /**
         * Flushes the cache which (flag as removed) removes the cached content.
         * Flushes the local cache.
         * @return object reference
         */
        public function flush()
        {
            $this->getLocalCache()->flush();
            $this->getCacheHandler()->flush();

            return $this;
        }

    }

?>