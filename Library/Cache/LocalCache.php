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

    use Brickoo\Library\Cache\Interfaces;
    use Brickoo\Library\Cache\Exceptions;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * LocalCache
     *
     * Implements a local cache for handling local cache operations.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class LocalCache implements Interfaces\LocalCacheInterface
    {

        /**
         * Holds the cached values.
         * @var array
         */
        protected $cacheValues;

        /**
         * Returns the cached content from the matching dentifier.
         * Before using this method is should be checked with has() if the
         * identifier is available since this method would throw an exception.
         * @param string $identifier the identifier to retrieve the content from
         * @throws IdentifierNotAvailableException if the identifier is not available
         * @return mixed the cached content
         */
        public function get($identifier)
        {
            TypeValidator::IsString($identifier);

            if (! array_key_exists($identifier, $this->cacheValues))
            {
                throw new Exceptions\IdentifierNotAvailableException($identifier);
            }

            return $this->cacheValues[$identifier];
        }

        /**
         * Sets the content holded by the given identifier.
         * @param string $identifier the identifier which should hold the content
         * @param mixed $content the content which should be cached
         * @return object reference
         */
        public function set($identifier, $content)
        {
            TypeValidator::IsString($identifier);

            $this->cacheValues[$identifier] = $content;

            return $this;
        }

        /**
         * Removes the identifier and cached content.
         * Before using this method is should be checked with has() if the
         * identifier is available since this method would throw an exception.
         * @param string $identifier the identifer to remove
         * @retrun object reference
         */
        public function remove($identifier)
        {
            TypeValidator::IsString($identifier);

            if (! array_key_exists($identifier, $this->cacheValues))
            {
                throw new Exceptions\IdentifierNotAvailableException($identifier);
            }

             unset($this->cacheValues[$identifier]);

             return $this;
        }

        /**
         * Checks if the identifier is available.
         * @param string $identifier the identifier to check for availability
         * @return boolean check result
         */
        public function has($identifier)
        {
            return array_key_exists($identifier, $this->cacheValues);
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct()
        {
            $this->flush();
        }

        /**
         * Flushes the cached values by removing any content holded.
         * @return object reference
         */
        public function flush()
        {
            $this->cacheValues = array();

            return $this;
        }

    }

?>