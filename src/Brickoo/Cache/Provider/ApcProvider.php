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
     * ApcProvider
     *
     * Provides caching operations based on APC.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ApcProvider implements Interfaces\Provider {

        /**
         * Returns the cached content from the matching dentifier.
         * @param string $identifier the identifier to retrieve the content from
         * @return mixed the cached content
         */
        public function get($identifier) {
            TypeValidator::IsString($identifier);

            return apc_fetch($identifier);
        }

        /**
         * Sets the content holded by the given identifier.
         * If the identifer already exists the content will be replaced.
         * The default lifetime of the cached content is 60 seconds.
         * @param string $identifier the identifier which should hold the content
         * @param mixed $content the content which should be cached
         * @param integer $lifetime the lifetime in seconds of the cached content
         * @return mixed the cache provider result
         */
        public function set($identifier, $content, $lifetime = 60) {
            TypeValidator::IsString($identifier);
            TypeValidator::IsInteger($lifetime);

            return apc_store($identifier, $content, $lifetime);
        }

        /**
         * Deletes the identifier and cached content.
         * @param string $identifier the identifer to remove
         * @return mixed the cache provider result
         */
        public function delete($identifier) {
            TypeValidator::IsString($identifier);

            return apc_delete($identifier);
        }

        /**
         * Flushes the cached values by removing (or flag as removed) any content holded.
         * @return mixed the cache provider result
         */
        public function flush() {
            return apc_clear_cache('user');
        }

        /**
         * Magic function to call other APC functions not implemented.
         * @param string $method the method called
         * @param array $arguments the arguments passed
         * @throws BadMethodCallException if the method is not defined
         * @return mixed APC method result
         */
        public function __call($method, array $arguments) {
            if ((substr($method, 0, 4) != 'apc_') || (! function_exists($method))) {
                throw new \BadMethodCallException(sprintf('The APC method `%s` is not defined.', $method));
            }

            return call_user_func_array($method, $arguments);
        }

    }