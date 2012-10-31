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

    use Brickoo\Validator\Argument;

    /**
     * Manager
     *
     * Implements caching routines to work with an injected cache provider.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Manager implements Interfaces\Manager {

        /** @var \Brickoo\Cache\Provider\Interfaces\Provider */
        private $CacheProvider;

        /**
         * Class constructor.
         * @param \Brickoo\Cache\Provider\Interfaces\Provider $CacheProvider
         * @return void
         */
        public function __construct(\Brickoo\Cache\Provider\Interfaces\Provider $CacheProvider) {
            $this->CacheProvider = $CacheProvider;
        }

        /** {@inheritDoc} */
        public function getByCallback($identifier, $callback, array $arguments) {
            Argument::IsString($identifier);
            Argument::IsCallable($callback);

            if (! $cachedContent = $this->get($identifier)) {
                $cachedContent = call_user_func_array($callback, $arguments);
            }

            return $cachedContent;
        }

        /** {@inheritDoc} */
        public function get($identifier) {
            Argument::IsString($identifier);

            return $this->CacheProvider->get($identifier);
        }

        /** {@inheritDoc} */
        public function set($identifier, $content, $lifetime) {
            Argument::IsString($identifier);
            Argument::IsInteger($lifetime);

            $this->CacheProvider->set($identifier, $content, $lifetime);
            return $this;
        }

        /** {@inheritDoc} */
        public function delete($identifier) {
            Argument::IsString($identifier);

            $this->CacheProvider->delete($identifier);
            return $this;
        }

        /** {@inheritDoc} */
        public function flush() {
            $this->CacheProvider->flush();
            return $this;
        }

    }