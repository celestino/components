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

    namespace Brickoo\Library\System;

    use Brickoo\Library\System\Interfaces;
    use Brickoo\Library\System\Exceptions;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * SocketObject
     *
     * Implements an OOP wrapper for handling socket operations.
     * The resource handle is created and closed by the SocketObject,
     * that`s the reason why fsockopen() and fclose() are not supported as magic method.
     * The managing of the handle makes it possible to configure the SocketObject at any time
     * and the handle handle will be just created if socket accessing is realy done later on.
     * This class does not implement all functions available for socket handling,
     * BUT(!) you can use any socket function which expects the handle handle as first argument.
     * Example:
     * <code>
     *     // Not implemented fwrite(), feof() and fread() but supported, notify the handle handle is not passed !
     *     $SocketObject =  new Brickoo\Library\System\SocketObject();
     *     $SocketObject->setServerAdress('sourceforge.net')
     *                  ->setServerPort(80)
     *                  ->setTimeout(10);
     *     $SocketObject->fwrite("GET /projects/brickoo/ HTTP/1.1\r\n"); // default php function
     *     $SocketObject->fwrite("Host: " . $SocketObject->getServerAdress() . "\r\n");
     *     $SocketObject->fwrite("\r\n\r\n");
     *
     *     $HTML = '';
     *     while(! $SocketObject->feof()) //default php function
     *     {
     *         $HTML .= $SocketObject->fread(1024); // default php function
     *     }
     *     $SocketObject->close();
     *     echo($HTML);
     * </code>
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class SocketObject implements Interfaces\SocketObjectInterface
    {

        /**
         * Holds the protocol using.
         * @var string
         */
        protected $protocol;

        /**
         * Returns the current protocol used.
         * @return string the current protocol or empty string
         */
        public function getProtocol()
        {
            if ($this->protocol === null)
            {
                return '';
            }

            return $this->protocol;
        }

        /**
         * Sets the protocol to use with the adress.
         * @param string $protocol the protocol to use
         * @throws Exceptions\HandleAlreadyExistsException if the handle already exists
         * @return object reference
         */
        public function setProtocol($protocol)
        {
            TypeValidator::IsString($protocol);

            if ($this->hasHandle())
            {
                throw new Exceptions\HandleAlreadyExistsException();
            }

            $this->protocol = $protocol. '://';

            return $this;
        }

        /**
         * Holds the server adress to connect to.
         * @var string
         */
        protected $serverAdress;

        /**
         * Returns the current server adress used.
         * @throws \UnexpectedValueException if server adress is not set
         * @return string the server adress
         */
        public function getServerAdress()
        {
            if ($this->serverAdress === null)
            {
                throw new \UnexpectedValueException('The server adress is `null`.');
            }
            return $this->serverAdress;
        }

        /**
         * Sets the severAdress to connect to.
         * @param string $severAdress the serverAdress to use
         * @throws Exceptions\HandleAlreadyExistsException if the handle already exists
         * @return object reference
         */
        public function setServerAdress($serverAdress)
        {
            TypeValidator::IsString($serverAdress);

            if ($this->hasHandle())
            {
                throw new Exceptions\HandleAlreadyExistsException();
            }

            $this->serverAdress = $serverAdress;

            return $this;
        }

        /**
         * Holds the server port number to connect to.
         * @var integer
         */
        protected $serverPort;

        /**
         * Returns the server port assigned.
         * @throws \UnexpectedValueException if the server port is not set
         * @return integer the server port number
         */
        public function getServerPort()
        {
            if ($this->serverPort === null)
            {
                throw new \UnexpectedValueException('The server port is `null`.');
            }

            return $this->serverPort;
        }

        /**
         * Sets the server port to connect to.
         * @param integer $serverPort the server port to use
         * @throws Exceptions\HandleAlreadyExistsException if the handle already exists
         * @return object reference
         */
        public function setServerPort($serverPort)
        {
            TypeValidator::IsInteger($serverPort);

            if ($this->hasHandle())
            {
                throw new Exceptions\HandleAlreadyExistsException();
            }

            $this->serverPort = $serverPort;

            return $this;
        }

        /**
         * Holds the connection timeout in seconds.
         * @var integer
         */
        protected $timeout;

        /**
         * Returns the connection timeout in seconds.
         * @throws \UnexpectedValueException if the timeout is not set
         * @return integer the connection timeout in seconds
         */
        public function getTimeout()
        {
            if ($this->timeout === null)
            {
                throw new \UnexpectedValueException('The timeout is `null`.');
            }

            return $this->timeout;
        }

        /**
         * Sets the connection timeout in seconds.
         * @param integer $timeout the connection timeout to use
         * @throws Exceptions\HandleAlreadyExistsException if the handle already exists
         * @return object reference
         */
        public function setTimeout($timeout)
        {
            TypeValidator::IsInteger($timeout);

            if ($this->hasHandle())
            {
                throw new Exceptions\HandleAlreadyExistsException();
            }

            $this->timeout = $timeout;

            return $this;
        }

        /**
         * Holds the opened handle handler.
         * @var handle
         */
        protected $handle;

        /**
         * Opens the file to store the handle handle.
         * @throws Exceptions\HandleAlreadyExistsException if the handle already exists
         * @throws Exceptions\UnableToCreateHandleException if the handle can not be opened
         * @return reource the file handle handle
         */
        public function open()
        {
            if ($this->hasHandle())
            {
                throw new Exceptions\HandleAlreadyExistsException();
            }

            if
            (
                ! $this->handle = @fsockopen
                (
                    $this->getProtocol() . $this->getServerAdress(),
                    $this->getServerPort(),
                    $errorNumber,
                    $errorMessage,
                    $this->getTimeout()
                )
            )
            {
                throw new Exceptions\UnableToCreateHandleException($this->getProtocol() . $this->getServerAdress());
            }

            return $this->handle;
        }

        /**
         * Lazy handle handle creation.
         * Returns the current used handle.
         * @return handle the handle handle
         */
        public function getHandle()
        {
            if (! $this->hasHandle())
            {
                $this->open();
            }

            return $this->handle;
        }

        /**
         * Checks if a handle has been created.
         * @return boolean check result
         */
        public function hasHandle()
        {
            return is_resource($this->handle);
        }

        /**
         * Removes the holded handle by closing the data handle.
         * This method does not throw an exception like the explicit FileObject::close does.
         * @return object reference
         */
        public function removeHandle()
        {
            if ($this->hasHandle())
            {
                $this->close();
            }

            $this->handle = null;

            return $this;
        }

        /**
        * Class constructor.
        * Initializes the class properties.
        * @return void
        */
        public function __construct()
        {
            $this->reset();
        }

        /**
        * Clears the class properties.
        * @return object reference
        */
        public function reset()
        {
            $this->protocol        = null;
            $this->serverAdress    = null;
            $this->serverPort      = null;
            $this->timeout         = null;
            $this->handle        = null;

            return $this;
        }

        /**
         * Removes the handle handle if available.
         * @return void
         */
        public function __destruct()
        {
            $this->removeHandle();
        }

        /**
         * Closes the data handle and frees the holded ressource.
         * @throws Exceptions\HandleNotAvailableException if the handle is not initialized
         * @return object reference
         */
        public function close()
        {
            if (! $this->hasHandle())
            {
                throw new Exceptions\HandleNotAvailableException();
            }

            fclose($this->getHandle());

            $this->handle = null;

            return $this;
        }

        /**
         * Provides the posibility to call not implemented file functions.
         * @param string $function the function name to call
         * @param array $arguments the arguments to pass
         * @throws BadMethodCallException if the trying to call fopen() or fclose()
         * @return mixed the calles function return value
         */
        public function __call($function, array $arguments)
        {
            if
            (
                ($function == 'fsockopen') ||
                ($function == 'fclose')
            )
            {
                throw new \BadMethodCallException
                (
                    sprintf('The method `%s`is not allowed to be called.', $function)
                );
            }

            $arguments = array_merge(array($this->getHandle()), $arguments);

            return call_user_func_array($function, $arguments);
        }

    }

?>