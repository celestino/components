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

    use Brickoo\Validator\Argument;

    /**
     * Memory
     *
     * Implements a memory cache for handling runtime cache operations.
     * Currently the cached content does not expire.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Memory implements Interfaces\Provider {

        /** @var array */
        private $cacheValues;

        /**
         * Class constructor.
         * @return void
         */
        public function __construct() {
            $this->cacheValues = array();
        }

        /** {@inheritDoc} */
        public function get($identifier) {
            Argument::IsString($identifier);

            if (! array_key_exists($identifier, $this->cacheValues)) {
                throw new Exceptions\IdentifierNotAvailable($identifier);
            }

            return $this->cacheValues[$identifier];
        }

        /** {@inheritDoc} */
        public function set($identifier, $content, $lifetime = 0) {
            Argument::IsString($identifier);
            $this->cacheValues[$identifier] = $content;
            return $this;
        }

        /** {@inheritDoc} */
        public function delete($identifier) {
            Argument::IsString($identifier);

            if (! array_key_exists($identifier, $this->cacheValues)) {
                throw new Exceptions\IdentifierNotAvailable($identifier);
            }

            unset($this->cacheValues[$identifier]);
            return $this;
        }

        /** {@inheritDoc} */
        public function has($identifier) {
            return array_key_exists($identifier, $this->cacheValues);
        }

        /** {@inheritDoc} */
        public function flush() {
            $this->cacheValues = array();
            return $this;
        }

    }