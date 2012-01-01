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

    namespace Brickoo\Library\System\Interfaces;

    /**
     * SocketObjectInterface
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     * @version $Id: FileObjectInterface.php 17 2011-12-25 02:06:06Z celestino $
     */

    interface SocketObjectInterface
    {

        /**
        * Returns the current protocol used.
        * @return string the current protocol or empty string
        */
        public function getProtocol();

        /**
         * Sets the protocol to use with the adress.
         * @param string $protocol the protocol to use
         * @throws Exceptions\HandleAlreadyExistsException if the handle already exists
         * @return object reference
         */
        public function setProtocol($protocol);

        /**
         * Returns the current server adress used.
         * @throws \UnexpectedValueException if server adress is not set
         * @return string the server adress
         */
        public function getServerAdress();

        /**
         * Sets the severAdress to connect to.
         * @param string $severAdress the serverAdress to use
         * @throws Exceptions\HandleAlreadyExistsException if the handle already exists
         * @return object reference
         */
        public function setServerAdress($serverAdress);

        /**
         * Returns the server port assigned.
         * @throws \UnexpectedValueException if the server port is not set
         * @return integer the server port number
         */
        public function getServerPort();

        /**
         * Sets the server port to connect to.
         * @param integer $serverPort the server port to use
         * @throws Exceptions\HandleAlreadyExistsException if the handle already exists
         * @return object reference
         */
        public function setServerPort($serverPort);

        /**
         * Returns the connection timeout in seconds.
         * @throws \UnexpectedValueException if the timeout is not set
         * @return integer the connection timeout in seconds
         */
        public function getTimeout();

        /**
         * Sets the connection timeout in seconds.
         * @param integer $timeout the connection timeout to use
         * @throws Exceptions\HandleAlreadyExistsException if the handle already exists
         * @return object reference
         */
        public function setTimeout($timeout);

        /**
         * Opens the file to store the handle handle.
         * @throws Exceptions\HandleAlreadyExistsException if the handle already exists
         * @throws Exceptions\UnableToCreateHandleException if the handle can not be opened
         * @return reource the file handle handle
         */
        public function open();

        /**
         * Lazy handle handle creation.
         * Returns the current used handle.
         * @return handle the handle handle
         */
        public function getHandle();

        /**
         * Checks if a handle has been created.
         * @return boolean check result
         */
        public function hasHandle();

        /**
         * Removes the holded handle by closing the data handle.
         * This method does not throw an exception like the explicit FileObject::close does.
         * @return object reference
         */
        public function removeHandle();

        /**
         * Class constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct();

        /**
         * Clears the class properties.
         * @return object reference
         */
        public function clear();

        /**
         * Removes the handle handle if available.
         * @return void
         */
        public function __destruct();

        /**
         * Closes the data handle and frees the holded ressource.
         * @throws Exceptions\HandleNotAvailableException if the handle is not initialized
         * @return object reference
         */
        public function close();

        /**
         * Provides the posibility to call not implemented file functions.
         * @param string $function the function name to call
         * @param array $arguments the arguments to pass
         * @throws BadMethodCallException if the trying to call fopen() or fclose()
         * @return mixed the calles function return value
         */
        public function __call($function, array $arguments);

    }

?>