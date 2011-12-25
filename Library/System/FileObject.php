<?php

    /*
     * Copyright (c) 2008-2011, Celestino Diaz Teran <celestino@users.sourceforge.net>.
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

    use Brickoo\Library\System\Exceptions\ResourceAlreadyAvailableExpetion;

    use Brickoo\Library\System\Interfaces;
    use Brickoo\Library\System\Exceptions;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * FileObject
     *
     * Implements an OOP wrapper for handling file operations.
     * The \SplFileObject has not an implementation for changing or just close
     * and open the location anytime, that´s the reason why i did created this version.
     * The resource handle is created and closed by the FileObject,
     * that´s the reason why fopen() and fclose() are not supported as magic method.
     * The managing of the resource makes it possible to configure the FileObject at any time
     * and the resource handle will be just created if file accessing is realy done later on.
     * This class does not implement all functions available for file handling,
     * BUT(!) you can use any file function which expects the resource handle as first argument.
     * Examples:
     * <code>
     *     // Not implemented fseek() and fread() but supported, notify the resource handle is not passed !
     *     $FileObject =  new Brickoo\Library\Filesystem\FileObject();
     *     $FileObject->setLocation('/path/to/file.txt')->setMode('r');
     *     $FileObject->fseek(100);
     *     $content  = $FileObject->read(1024); // implemented with mode check
     *     $content .= $FileObject->fread(1024); // default php function
     *     $FileObject->close();
     *     var_dump($content);
     *
     *     // Not implemented feof() but supported, reading a file until end of file
     *     $content = ''
     *     $FileObject =  new Brickoo\Library\Filesystem\FileObject();
     *     $FileObject->setLocation('/path/to/file.txt')->setMode('r');
     *     while(! $FileObject->feof())
     *     {
     *         $content = $FileObject->read(1024);
     *     }
     *     $FileObject->close();
     *     var_dump($content);
     * </code>
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id$
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
            if ($this->location === null)
            {
                throw new \UnexpectedValueException('The location is ´null´.');
            }

            return $this->location;
        }

        /**
         * Sets the location to use for file operations.
         * @param string $location the location to use
         * @throws Exceptions\ResourceAlreadyExistsException if the resource already exists
         * @return object reference
         */
        public function setLocation($location)
        {
            TypeValidator::Validate('isString', array($location));

            if ($this->hasResource())
            {
                throw new Exceptions\ResourceAlreadyExistsException();
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
            if ($this->mode === null)
            {
                throw new \UnexpectedValueException('The mode is ´null´.');
            }
            return $this->mode;
        }

        /**
         * Sets the mode for the file operation.
         * @param string $mode the mode to use
         * @throws Exceptions\ResourceAlreadyExistsException if the resource already exists
         * @return object reference
         */
        public function setMode($mode)
        {
            TypeValidator::Validate('useRegex', array(array('~^[acwrx]([\+])?$~', $mode)));

            if ($this->hasResource())
            {
                throw new Exceptions\ResourceAlreadyExistsException();
            }

            $this->mode = $mode;

            return $this;
        }

        /**
         * Holds the opened resource handler.
         * @var resource
         */
        protected $resource;

        /**
         * Opens the file to store the resource handle.
         * @param string $location the location of the file to open
         * @param string $mode the mode to use
         * @throws Exceptions\ResourceAlreadyExistsException if the resource already exists
         * @throws Exceptions\UnableToOpenResourceException if the resource can not be opened
         * @return reource the file handle resource
         */
        public function open($location, $mode)
        {
            TypeValidator::Validate('isString', array($location, $mode));

            if ($this->hasResource())
            {
                throw new Exceptions\ResourceAlreadyExistsException();
            }

            if (! $this->resource = @fopen($location, $mode))
            {
                throw new Exceptions\UnableToOpenResourceException($location);
            }

            return $this->resource;
        }

        /**
         * Lazy resource handle creation.
         * Returns the current used resource.
         * @return resource the resoruce handle
         */
        public function getResource()
        {
            if (! $this->hasResource())
            {
                $this->open($this->getLocation(), $this->getMode());
            }

            return $this->resource;
        }

        /**
         * Checks if a resource has been created.
         * @return boolean check result
         */
        public function hasResource()
        {
            return is_resource($this->resource);
        }

        /**
         * Removes the holded resource by closing the data handle.
         * This method does not throw an exception like the explicit FileObject::close does.
         * @return object reference
         */
        public function removeResource()
        {
            if ($this->hasResource())
            {
                $this->close();
            }

            $this->resource = null;

            return $this;
        }

        /**
        * Class constructor.
        * Initializes the class properties.
        * @return void
        */
        public function __construct()
        {
            $this->clear();
        }

        /**
        * Clears the class properties.
        * @return object reference
        */
        public function clear()
        {
            $this->location    = null;
            $this->filename    = null;
            $this->mode        = null;
            $this->resource    = null;

            return $this;
        }

        /**
         * Removes the resource handle if available.
         * @return void
         */
        public function __destruct()
        {
            $this->removeResource();
        }

        /**
         * Writes the data into the file location.
         * Makes sure the mode supports write operations.
         * @param integer|string $data the data to write
         * @return object reference;
         */
        public function write($data)
        {
            TypeValidator::Validate('isStringOrInteger', array($data));

            if (($mode = $this->getMode()) == 'r')
            {
                throw new Exceptions\InvalidModeOperationException($mode);
            }

            fwrite($this->getResource(), $data);

            return $this;
        }

        /**
         * Reads the passed bytes of data from the file location.
         * Makes sure the mode supports read operations.
         * @param integer the amount of bytes to read from
         * @return string the readed content bytes
         */
        public function read($bytes)
        {
            TypeValidator::Validate('isInteger', array($bytes), TypeValidator::FLAG_INTEGER_CAN_NOT_BE_ZERO);

            if (preg_match('~^[waxc]$~', ($mode = $this->getMode())))
            {
                throw new Exceptions\InvalidModeOperationException($mode);
            }

            return fread($this->getResource(), $bytes);
        }

        /**
         * Closes the data handle and frees the holded ressource.
         * @throws Exceptions\ResourceNotAvailableException if the resource is not initialized
         * @return object reference
         */
        public function close()
        {
            if (! $this->hasResource())
            {
                throw new Exceptions\ResourceNotAvailableException();
            }

            fclose($this->getResource());

            $this->resource = null;

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
                ($function == 'fopen') ||
                ($function == 'fclose')
            )
            {
                throw new \BadMethodCallException
                (
                    sprintf('The method ´%s`is not allowed to be called.', $function)
                );
            }

            $arguments = array_merge(array($this->getResource()), $arguments);

            return call_user_func_array($function, $arguments);
        }
    }

?>