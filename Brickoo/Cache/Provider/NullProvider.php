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

    /**
     * NullProvider
     *
     * Does not do anything.
     * This can be used if the backend cache is not need.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class NullProvider implements Interfaces\CacheProviderInterface
    {

        /**
        * Returns boolean false like the content is not available.
        * @param string $identifier the identifier to retrieve the content from
        * @return boolean false
        */
        public function get($identifier)
        {
            return false;
        }

        /**
         * Returns boolean true like the content did be cached.
         * @param string $identifier the identifier which should hold the content
         * @param mixed $content the content which should be cached
         * @param integer $lifetime the lifetime in seconds of the cached content
         * @return boolean true
         */
        public function set($identifier, $content, $lifetime)
        {
            return true;
        }

        /**
         * Returns boolean true like the identifier and its cached content did be deleted.
         * @param string $identifier the identifer to remove
         * @return boolean true
         */
        public function delete($identifier)
        {
            return true;
        }

        /**
         * Returns boolean true like the cache content did be flushed.
         * @return boolean true
         */
        public function flush()
        {
            return true;
        }

    }