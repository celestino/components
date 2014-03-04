<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
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

namespace Brickoo\Component\Network;

use Brickoo\Component\Network\ClientConfiguration,
    Brickoo\Component\Network\Exception\HandleAlreadyExistsException,
    Brickoo\Component\Network\Exception\HandleNotAvailableException,
    Brickoo\Component\Network\Exception\UnableToCreateHandleException,
    Brickoo\Component\Validation\Argument;

/**
 * Client
 *
 * Implements an OOP wrapper for handling socket operations.
 * The resource handle is created and closed by the Client,
 * that is the reason why fclose() is not supported as magic method.
 * This class does not implement all functions available for socket handling,
 * BUT(!) you can use any socket function which expects the resource handle as first argument.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class Client {

    /** @var resource */
    protected $handle;

    /** @var \Brickoo\Component\Network\ClientConfiguration */
    protected $configuration;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Network\ClientConfiguration $configuration
     */
    public function __construct(ClientConfiguration $configuration) {
        $this->configuration = $configuration;
    }

    /**
     * Opens a stream resource handle.
     * @see http://www.php.net/manual/en/function.stream-socket-client.php
     * @throws \Brickoo\Component\Network\Exception\HandleAlreadyExistsException
     * @throws \Brickoo\Component\Network\Exception\UnableToCreateHandleException
     * @return \Brickoo\Net work\Client
     */
    public function open() {
        if ($this->hasHandle()) {
            throw new HandleAlreadyExistsException();
        }

        $errorCode = null;
        $errorMessage = null;

        if (! $handle = @stream_socket_client(
            $this->configuration->getSocketAdress(),
            $errorCode, $errorMessage,
            $this->configuration->getConnectionTimeout(),
            $this->configuration->getConnectionType(),
            stream_context_create($this->configuration->getContextOptions())
        )){
            throw new UnableToCreateHandleException($this->configuration->getSocketAdress(), $errorCode, $errorMessage);
        }

        $this->handle = $handle;
        return $this;
    }

    /**
     * Sends a message through the connection previously created
     * @param string $data the data to write
     * @throws \Brickoo\Component\Network\Exception\HandleNotAvailableException
     * @return integer the bytes written
     */
    public function write($data) {
        return fwrite($this->getHandle(), $data);
    }

    /**
     * Reads the response returned by the connection.
     * @param integer $bytes the number of bytes to read
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Network\Exception\HandleNotAvailableException
     * @return string the connection response
     */
    public function read($bytes) {
        Argument::IsInteger($bytes);
        return fread($this->getHandle(), $bytes);
    }

    /**
     * Closes the data handle and frees the holded ressource.
     * @throws \Brickoo\Component\Network\Exception\HandleNotAvailableException
     * @return \Brickoo\Component\Network\Client
     */
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
     * @throws \Brickoo\Component\Network\Exception\HandleNotAvailableException
     * @return resource the resource handle
     */
    private function getHandle() {
        if (! $this->hasHandle()) {
            throw new HandleNotAvailableException();
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
     * @throws \BadMethodCallException if the trying to call fclose()
     * @return mixed the called function return value
     */
    public function __call($function, array $arguments) {
        if ($function == "fclose") {
            throw new \BadMethodCallException(
                sprintf("The method `%s` is not allowed to be called.", $function)
            );
        }
        return call_user_func_array($function, array_merge(array($this->getHandle()), $arguments));
    }

}