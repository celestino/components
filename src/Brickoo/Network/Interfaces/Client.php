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

    namespace Brickoo\Network\Interfaces;

    /**
     * Client
     *
     * Describes a client based on a socket connection to communicate through the network.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface Client {

        /**
         * Opens a stream resource handle.
         * @see http://www.php.net/manual/en/function.stream-socket-client.php
         * @param string $hostname the hostname to connect to e.g. "http://domain.com"
         * @param integer $port the port number to use for the connection
         * @param integer $timeout the timeout in seconds of the connection
         * @param integer $connectionType (persistent:1, async:2, default:4)
         * @param resource|null $content the stream context to use
         * @throws \InvalidArgumentException if an argument is not valid
         * @throws \Brickoo\Network\Exceptions\HandleAlreadyExists if the handle already exists
         * @throws \Brickoo\Network\Exceptions\UnableToCreateHandle if the handle can not be opened
         * @return \Brickoo\Network\Interfaces\Client
         */
        public function open($hostname, $port, $timeout, $connectionType = STREAM_CLIENT_CONNECT, $context = null);

        /**
         * Sends a message through the connection previously created
         * @param string $data the data to write
         * @throws \Brickoo\Network\Exceptions\HandleNotAvailable if the handle is not initialized
         * @return integer the bytes written
         */
        public function write($data);

        /**
         * Reads the response returned by the connection.
         * @param integer $bytes the number of bytes to read
         * @throws \InvalidArgumentException if an argument is not valid
         * @throws \Brickoo\Network\Exceptions\HandleNotAvailable if the handle is not initialized
         * @return string the connection response
         */
        public function read($bytes);

        /**
         * Closes the data handle and frees the holded ressource.
         * @throws \Brickoo\Network\Exceptions\HandleNotAvailable if the handle is not initialized
         * @return \Brickoo\Network\Interfaces\Client
         */
        public function close();

    }