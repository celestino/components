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

    namespace Brickoo\Cache\Interfaces;

    /**
     * ManagerInterface
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface ManagerInterface
    {

         /**
         * Return a cached content assigned to the identifier.
         * If the cached content is not available, it will be retrieved
         * with the given callback and cached again with the lifetime expiration.
         * This method should also work with a closure function.
         * @param string $cacheIdentifier the cache identifier of the content
         * @param callable $callback the callback to call if the content is not cached
         * @param array $arguments the arguments to pass to the callback
         * @param integer $lifetime the lifetime for the cached content to set
         */
        public function getByCallback($cacheIdentifier, $callback, array $arguments, $lifetime);

        /**
         * Returns the cached content using the identifier.
         * Stores the content into the local cache.
         * @param string $identifier the identifier to retrieve the content from
         * @return mixed the cached content
         */
        public function get($identifier);

        /**
         * Sets the content holded by the given identifier.
         * If the identifer already exists the content will be replaced.
         * Stores the content into the local cache.
         * @param string $identifier the identifier which holds the content
         * @param mixed $content the content to cache
         * @param integer $lifetime the lifetime of the cached content
         * @return \Brickoo\Cache\Interfaces\CacheProviderInterface
         */
        public function set($identifier, $content, $lifetime);

        /**
         * Deletes the cached content which is holded by the identifier.
         * Removes the local cached content.
         * @param string $identifier the identifier which holds the content
         * @return \Brickoo\Cache\Interfaces\CacheProviderInterface
         */
        public function delete($identifier);

        /**
         * Flushes the cache which (flag as removed) removes the cached content.
         * Flushes the local cache.
         * @return \Brickoo\Cache\Interfaces\CacheProviderInterface
         */
        public function flush();

    }