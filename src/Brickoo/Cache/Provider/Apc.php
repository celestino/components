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

    namespace Brickoo\Cache\Provider;

    use Brickoo\Validator\Argument;

    /**
     * Apc
     *
     * Provides caching operations using the APC extension.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Apc implements Interfaces\Provider {

        /** {@inheritDoc} */
        public function get($identifier) {
            Argument::IsString($identifier);
            return apc_fetch($identifier);
        }

        /** {@inheritDoc} */
        public function set($identifier, $content, $lifetime) {
            Argument::IsString($identifier);
            Argument::IsInteger($lifetime);

            apc_store($identifier, $content, $lifetime);
            return $this;
        }

        /** {@inheritDoc} */
        public function delete($identifier) {
            Argument::IsString($identifier);
            apc_delete($identifier);
            return $this;
        }

        /** {@inheritDoc} */
        public function flush() {
            apc_clear_cache("user");
            return $this;
        }

        /** {@inheritDoc} */
        public function isReady() {
            return (extension_loaded("apc") && in_array(ini_get("apc.enabled"), array("On", "1")));
        }

        /**
         * Magic function to call other APC functions not implemented.
         * @param string $method the method called
         * @param array $arguments the arguments passed
         * @throws \BadMethodCallException if the method is not defined
         * @return mixed the called APC method result
         */
        public function __call($method, array $arguments) {
            if ((substr($method, 0, 4) != "apc_") || (! function_exists($method))) {
                throw new \BadMethodCallException(sprintf("The APC method `%s` is not defined.", $method));
            }

            return call_user_func_array($method, $arguments);
        }

    }