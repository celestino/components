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

    namespace Brickoo\Network;

    use Brickoo\Validator\Argument;

    /**
     * Client
     *
     * Implements an OOP wrapper for handling socket operations.
     * The resource handle is created and closed by the Client,
     * that`s the reason why fsockopen() and fclose() are not supported as magic method.
     * This class does not implement all functions available for socket handling,
     * BUT(!) you can use any socket function which expects the resource handle as first argument.
     * Example:
     * <code>
     *     // Not implemented fwrite() or feof() but supported, notify the resource handle is not passed !
     *     $Client =  new Brickoo\Network\Client();
     *     $Client->open("tcp://somedomain.com", 80, 30);
     *     $Client->write("GET /projects/brickoo/ HTTP/1.1\r\n");
     *     $Client->write("Host: somedomain.com\r\n");
     *
     *     $Client->fwrite("\r\n\r\n");
     *
     *     $HTML = "";
     *     while(! $Client->feof()) {
     *         $HTML .= $Client->read(1024);
     *     }
     *     $Client->close();
     *     echo($HTML);
     * </code>
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Client implements Interfaces\Client {

        /** @var resource */
        protected $handle;

        /** {@inheritDoc} */
        public function open($hostname, $port, $timeout, $connectionType = STREAM_CLIENT_CONNECT, $context = null) {
            if ($this->hasHandle()) {
                throw new Exceptions\HandleAlreadyExists();
            }

            Argument::IsString($hostname);
            Argument::IsInteger($port);
            Argument::IsInteger($timeout);
            Argument::IsInteger($connectionType);

            if ($context !== null && (! $handle = @stream_socket_client($hostname .":". $port, $errorCode, $errorMessage, $timeout, $connectionType, $context))) {
                throw new Exceptions\UnableToCreateHandle($hostname, $port, $errorCode, $errorMessage);
            }

            if ($context == null && (! $handle = @stream_socket_client($hostname .":". $port, $errorCode, $errorMessage, $timeout, $connectionType))) {
                throw new Exceptions\UnableToCreateHandle($hostname, $port, $errorCode, $errorMessage);
            }

            $this->handle = $handle;
            return $this;
        }

        /** {@inheritDoc} */
        public function write($data) {
            return fwrite($this->getHandle(), $data);
        }

        /** {@inheritDoc} */
        public function read($bytes) {
            Argument::IsInteger($bytes);
            return fread($this->getHandle(), $bytes);
        }

        /** {@inheritDoc} */
        public function close() {
            fclose($this->getHandle());
            $this->handle = null;
            return $this;
        }

        /**
         * Removes the resource handle on destruction.
         * @return void
         */
        public function __destruct() {
            if ($this->hasHandle()) {
                $this->close();
            }
        }

        /**
         * Returns the current used handle.
         * @throws \Brickoo\Network\Exceptions\HandleNotAvailable if the handle is not available
         * @return resource the resource handle
         */
        private function getHandle() {
            if (! $this->hasHandle()) {
                throw new Exceptions\HandleNotAvailable();
            }

            return $this->handle;
        }

        /**
         * Checks if a handle has been already created.
         * @return boolean check result
         */
        private function hasHandle() {
            return is_resource($this->handle);
        }

        /**
         * Provides the possibility to call not implemented socket functions.
         * @param string $function the function name to call
         * @param array $arguments the arguments to pass
         * @throws \BadMethodCallException if the trying to call fopen() or fclose()
         * @return mixed the calles function return value
         */
        public function __call($function, array $arguments) {
            if ($function == 'fclose') {
                throw new \BadMethodCallException(
                    sprintf('The method `%s` is not allowed to be called.', $function)
                );
            }
            return call_user_func_array($function, array_merge(array($this->getHandle()), $arguments));
        }

    }