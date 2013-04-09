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
        private $Provider;

        /** @var \Brickoo\Cache\Interfaces\ProviderPool */
        private $ProviderPool;

        /**
         * Class constructor.
         * @param \Brickoo\Cache\Interfaces\ProviderPool $ProviderPool
         * @return void
         */
        public function __construct(\Brickoo\Cache\Interfaces\ProviderPool $ProviderPool) {
            $this->Provider = null;
            $this->ProviderPool = $ProviderPool;
        }

        /** {@inheritDoc} */
        public function getByCallback($identifier, $callback, array $callbackArguments, $lifetime) {
            Argument::IsString($identifier);
            Argument::IsCallable($callback);
            Argument::IsInteger($lifetime);

            if ((! $content = $this->get($identifier))
                && ($content = call_user_func_array($callback, $callbackArguments))
            ){
                $this->set($identifier, $content, $lifetime);
            }

            return $content;
        }

        /** {@inheritDoc} */
        public function get($identifier) {
            Argument::IsString($identifier);

            return $this->getProvider()->get($identifier);
        }

        /** {@inheritDoc} */
        public function set($identifier, $content, $lifetime) {
            Argument::IsString($identifier);
            Argument::IsInteger($lifetime);

            $this->getProvider()->set($identifier, $content, $lifetime);
            return $this;
        }

        /** {@inheritDoc} */
        public function delete($identifier) {
            Argument::IsString($identifier);

            $providerEntryKey = $this->ProviderPool->key();

            $this->ProviderPool->rewind();
            while ($this->ProviderPool->valid()) {
                $this->ProviderPool->current()->delete($identifier);
                $this->ProviderPool->next();
            }

            $this->ProviderPool->select($providerEntryKey);
            return $this;
        }

        /** {@inheritDoc} */
        public function flush() {
            $providerEntryKey = $this->ProviderPool->key();

            $this->ProviderPool->rewind();
            while ($this->ProviderPool->valid()) {
                $this->ProviderPool->current()->flush();
                $this->ProviderPool->next();
            }

            $this->ProviderPool->select($providerEntryKey);
            return $this;
        }

        /**
         * Returns a responsible provider entry from pool
         * which have to be ready.
         * @return \Brickoo\Cache\Provider\Interfaces\Provider
         */
        private function getProvider() {
            if ($this->Provider === null) {
                if ($this->ProviderPool->isEmpty()) {
                    throw new Exceptions\ProviderNotFound();
                }

                $this->ProviderPool->rewind();
                while ($this->Provider === null && $this->ProviderPool->valid()) {
                    if ($this->ProviderPool->current()->isReady()) {
                        $this->Provider = $this->ProviderPool->current();
                        $readyProviderEntryKey = $this->ProviderPool->key();
                    }
                    $this->ProviderPool->next();
                }

                if ($this->Provider === null) {
                    throw new Exceptions\ProviderNotReady();
                }

                $this->ProviderPool->select($readyProviderEntryKey);
            }

            return $this->Provider;
        }

    }