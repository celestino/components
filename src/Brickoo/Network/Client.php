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

namespace Brickoo\Network;

use Brickoo\Network\Exception\HandleAlreadyExistsException,
    Brickoo\Network\Exception\HandleNotAvailableException,
    Brickoo\Network\Exception\UnableToCreateHandleException,
    Brickoo\Validation\Argument;

/**
 * Client
 *
 * Implements an OOP wrapper for handling socket operations.
 * The resource handle is created and closed by the Client,
 * that`s the reason why fsockopen() and fclose() are not supported as magic method.
 * This class does not implement all functions available for socket handling,
 * BUT(!) you can use any socket function which expects the resource handle as first argument.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class Client {

    /** @var resource */
    protected $handle;

    /**
     * Opens a stream resource handle.
     * @see http://www.php.net/manual/en/function.stream-socket-client.php
     * @param string $hostname the hostname to connect to e.g. "http://domain.com"
     * @param integer $port the port number to use for the connection
     * @param integer $timeout the timeout in seconds of the connection
     * @param integer $connectionType (persistent:1, async:2, default:4)
     * @param resource|null $content the stream context to use
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Network\Exception\HandleAlreadyExistsException
     * @throws \Brickoo\Network\Exception\UnableToCreateHandleException
     * @return \Brickoo\Network\Client
     */
    public function open($hostname, $port, $timeout, $connectionType = STREAM_CLIENT_CONNECT, $context = null) {
        if ($this->hasHandle()) {
            throw new HandleAlreadyExistsException();
        }

        Argument::IsString($hostname);
        Argument::IsInteger($port);
        Argument::IsInteger($timeout);
        Argument::IsInteger($connectionType);

        if ($context !== null && (! $handle = @stream_socket_client($hostname .":". $port, $errorCode, $errorMessage, $timeout, $connectionType, $context))) {
            throw new UnableToCreateHandleException($hostname, $port, $errorCode, $errorMessage);
        }

        if ($context == null && (! $handle = @stream_socket_client($hostname .":". $port, $errorCode, $errorMessage, $timeout, $connectionType))) {
            throw new UnableToCreateHandleException($hostname, $port, $errorCode, $errorMessage);
        }

        $this->handle = $handle;
        return $this;
    }

    /**
     * Sends a message through the connection previously created
     * @param string $data the data to write
     * @throws \Brickoo\Network\Exception\HandleNotAvailableException
     * @return integer the bytes written
     */
    public function write($data) {
        return fwrite($this->getHandle(), $data);
    }

    /**
     * Reads the response returned by the connection.
     * @param integer $bytes the number of bytes to read
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Network\Exception\HandleNotAvailableException
     * @return string the connection response
     */
    public function read($bytes) {
        Argument::IsInteger($bytes);
        return fread($this->getHandle(), $bytes);
    }

    /**
     * Closes the data handle and frees the holded ressource.
     * @throws \Brickoo\Network\Exception\HandleNotAvailableException
     * @return \Brickoo\Network\Client
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
     * @throws \Brickoo\Network\Exception\HandleNotAvailableException
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