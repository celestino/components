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
     * LocalCacheInterface
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface LocalCacheInterface
    {

        /**
         * Returns the cached content from the matching dentifier.
         * Before using this method is should be checked with has() if the
         * identifier is available since this method would throw an exception.
         * @param string $identifier the identifier to retrieve the content from
         * @throws IdentifierNotAvailableException if the identifier is not available
         * @return mixed the cached content
         */
        public function get($identifier);

        /**
         * Sets the content holded by the given identifier.
         * @param string $identifier the identifier which should hold the content
         * @param mixed $content the content which should be cached
         * @return \Brickoo\Cache\Interfaces\LocalCacheInterface
         */
        public function set($identifier, $content);

        /**
         * Removes the identifier and cached content.
         * Before using this method is should be checked with has() if the
         * identifier is available since this method would throw an exception.
         * @param string $identifier the identifer to remove
         * @retrun \Brickoo\Cache\Interfaces\LocalCacheInterface
         */
        public function remove($identifier);

        /**
         * Checks if the identifier is available.
         * @param string $identifier the identifier to check for availability
         * @return boolean check result
         */
        public function has($identifier);

        /**
         * Flushes the cached values by removing any content holded.
         * @return \Brickoo\Cache\Interfaces\LocalCacheInterface
         */
        public function flush();

    }