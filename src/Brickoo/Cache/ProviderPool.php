<?php

    /*
     * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

    use Brickoo\Validator,
        Brickoo\Validator\Argument;

    /**
     * ProviderPool
     *
     * Implementation of a caching provider pool.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ProviderPool implements Interfaces\ProviderPool {

        /** @var array of type \Brickoo\Cache\Provider\Interfaces\Provider */
        private $poolEntries;

        /** @var array */
        private $mappingKeys;

        /** @var integer */
        private $currentKey;

        /**
         * Class constructor.
         * @param array $poolEntries
         * @throws \InvalidArgumentException if a pool entry does not match expected type
         * @return void
         */
        public function __construct(array $poolEntries) {
            $TypeValidator = new Validator\Constraint\TraversableContainsInstancesOf('\Brickoo\Cache\Provider\Interfaces\Provider');
            if (! $TypeValidator->assert($poolEntries)) {
                throw new \InvalidArgumentException(
                    "The pool entries must be instances of \Brickoo\Cache\Provider\Interfaces\Provider"
                );
            }

            $this->poolEntries = array_values($poolEntries);
            $this->mappingKeys = array_keys($poolEntries);
            $this->currentKey = 0;
        }

        /**
         * {@inheritDoc}
         * @return \Brickoo\Cache\Provider\Interfaces\Provider
         */
        public function current() {
            return $this->poolEntries[$this->currentKey];
        }

        /**
         * {@inheritDoc}
         * @return integer the current key
         */
        public function key() {
            return $this->mappingKeys[$this->currentKey];
        }

        /** {@inheritDoc}*/
        public function next() {
            $this->currentKey++;
        }

        /** {@inheritDoc}*/
        public function rewind() {
            $this->currentKey = 0;
        }

        /** {@inheritDoc}*/
        public function valid() {
            return isset($this->poolEntries[$this->currentKey]);
        }

        /** {@inheritDoc}*/
        public function select($entryKey) {
            Argument::IsStringOrInteger($entryKey);

            if (! $this->has($entryKey)) {
                throw new Exceptions\PoolEntryDoesNotExist($entryKey);
            }

            $this->currentKey = array_search($entryKey, $this->mappingKeys, true);
            return $this;
        }

        /** {@inheritDoc}*/
        public function has($entryKey) {
            Argument::IsStringOrInteger($entryKey);
            return in_array($entryKey, $this->mappingKeys, true);
        }

        /** {@inheritDoc}*/
        public function isEmpty() {
            return empty($this->poolEntries);
        }

        /** {@inheritDoc}*/
        public function count(){
            return count($this->poolEntries);
        }

    }