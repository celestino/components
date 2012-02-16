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

    namespace Brickoo\System;

    use Brickoo\System\Interfaces;
    use Brickoo\System\Exceptions;
    use Brickoo\Validator\TypeValidator;

    /**
     * FileObject
     *
     * Implements an OOP wrapper for handling file operations.
     * The SplFileObject has not an implementation for changing or just close
     * and open the location anytime, that`s the reason why i did created this version.
     * The resource handle is created and closed by the FileObject,
     * that`s the reason why fopen() and fclose() are not supported as magic method.
     * The managing of the handle makes it possible to configure the FileObject at any time
     * and the handle handle will be just created if file accessing is realy done later on.
     * This class does not implement all functions available for file handling,
     * BUT(!) you can use any file function which expects the handle handle as first argument.
     * Examples:
     * <code>
     *     // Not implemented fseek() and fread() but supported, notify the handle handle is not passed !
     *     $FileObject =  new Brickoo\System\FileObject();
     *     $FileObject->setLocation('/path/to/file.txt')->setMode('r');
     *     $FileObject->fseek(100);
     *     $content  = $FileObject->read(); // implemented with mode check
     *     $content .= $FileObject->fread(1024); // default php function
     *     $FileObject->close();
     *     var_dump($content);
     *
     *     // Not implemented feof() but supported, reading a file until end of file
     *     $content = ''
     *     $FileObject =  new Brickoo\System\FileObject();
     *     $FileObject->setLocation('/path/to/file.txt')->setMode('r');
     *     while(! $FileObject->feof())
     *     {
     *         $content .= $FileObject->read();
     *     }
     *     $FileObject->close();
     *     var_dump($content);
     * </code>
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class FileObject implements Interfaces\FileObjectInterface
    {

        /**
         * Holds the location to open.
         * @var string
         */
        protected $location;

        /**
         * Returns the current location used.
         * @throws \UnexpectedValueException if no location has been assigned
         * @return string the current location
         */
        public function getLocation()
        {
            if ($this->location === null) {
                throw new \UnexpectedValueException('The file location is `null`.');
            }

            return $this->location;
        }

        /**
         * Sets the location to use for file operations.
         * @param string $location the location to use
         * @throws Exceptions\HandleAlreadyExistsException if the handle already exists
         * @return object reference
         */
        public function setLocation($location)
        {
            TypeValidator::IsString($location);

            if ($this->hasHandle()) {
                throw new Exceptions\HandleAlreadyExistsException();
            }

            $this->location = $location;

            return $this;
        }

        /**
         * Holds the mode to use.
         * @var string
         */
        protected $mode;

        /**
         * Returns the current mode used.
         * @throws \UnexpectedValueException if no mode has been assigned
         * @return string the current mode
         */
        public function getMode()
        {
            if ($this->mode === null) {
                throw new \UnexpectedValueException('The file mode is `null`.');
            }
            return $this->mode;
        }

        /**
         * Sets the mode for the file operation.
         * @param string $mode the mode to use
         * @throws Exceptions\HandleAlreadyExistsException if the handle already exists
         * @return object reference
         */
        public function setMode($mode)
        {
            TypeValidator::MatchesRegex('~^[acwrx]([\+])?$~', $mode);

            if ($this->hasHandle()) {
                throw new Exceptions\HandleAlreadyExistsException();
            }

            $this->mode = $mode;

            return $this;
        }

        /**
         * Holds the opened handle handler.
         * @var handle
         */
        protected $handle;

        /**
         * Lazy handle handle creation.
         * Returns the current used handle.
         * @return handle the resoruce handle
         */
        public function getHandle()
        {
            if (! $this->hasHandle()) {
                $this->open($this->getLocation(), $this->getMode());
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
            if ($this->hasHandle()) {
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
            $this->location    = null;
            $this->filename    = null;
            $this->mode        = null;
            $this->handle      = null;
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
         * Opens the file to store the handle handle.
         * @throws Exceptions\HandleAlreadyExistsException if the handle already exists
         * @throws Exceptions\UnableToCreateHandleException if the handle can not be opened
         * @return reource the resource handle
         */
        public function open()
        {
            if ($this->hasHandle()) {
                throw new Exceptions\HandleAlreadyExistsException();
            }

            if (! $this->handle = @fopen($this->getLocation(), $this->getMode())) {
                throw new Exceptions\UnableToCreateHandleException($this->getLocation());
            }

            return $this->handle;
        }

        /**
         * Writes the data into the file location.
         * Makes sure the mode supports write operations.
         * @param integer|string $data the data to write
         * @return object reference;
         */
        public function write($data)
        {
            if (($mode = $this->getMode()) == 'r') {
                throw new Exceptions\InvalidModeOperationException($mode);
            }

            fwrite($this->getHandle(), $data);

            return $this;
        }

        /**
         * Reads the passed bytes of data from the file location.
         * Makes sure the mode supports read operations.
         * The default the chuck size is 1024 bytes.
         * @param integer the amount of bytes to read from
         * @return string the readed content bytes
         */
        public function read($bytes = 1024)
        {
            TypeValidator::IsInteger($bytes, TypeValidator::FLAG_INTEGER_CAN_NOT_BE_ZERO);

            if (preg_match('~^[waxc]$~', ($mode = $this->getMode()))) {
                throw new Exceptions\InvalidModeOperationException($mode);
            }

            return fread($this->getHandle(), $bytes);
        }

        /**
         * Closes the data handle and frees the holded ressource.
         * @throws Exceptions\HandleNotAvailableException if the handle is not initialized
         * @return object reference
         */
        public function close()
        {
            if (! $this->hasHandle()) {
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
         * @return mixed the called function return value
         */
        public function __call($function, array $arguments)
        {
            if (($function == 'fopen') || ($function == 'fclose')) {
                throw new \BadMethodCallException(
                    sprintf('The method `%s` is not allowed to be called.', $function)
                );
            }

            $arguments = array_merge(array($this->getHandle()), $arguments);

            return call_user_func_array($function, $arguments);
        }
    }