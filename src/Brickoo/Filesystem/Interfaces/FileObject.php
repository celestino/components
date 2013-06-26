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

    namespace Brickoo\Filesystem\Interfaces;

    /**
     * FileObject
     *
     * Describes a file object to read from and write to files.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface FileObject {

        /**
        * Opens the file to store the handle of a resource.
        * @see http://www.php.net/manual/en/function.fopen.php
        * @param string $filename the file name of the resource to open
        * @param string $mode the mode to use for opening the resource
        * @param boolean $useIncludePath flag to also use the include path as search location
        * @param resource|null $context the context created to use
        * @throws \InvalidArgumentException if an argument is not valid
        * @throws \Brickoo\Filesystem\Exceptions\UnableToCreateHandle if the handle can not be created
        * @throws \Brickoo\Filesystem\Exceptions\HandleAlreadyExists if the handle has been created and not closed
        * @return \Brickoo\Filesystem\Interfaces\Client
        */
        public function open($filename, $mode, $useIncludePath = false, $context = null);

        /**
         * Writes the data to the file.
         * @param mixed $data the data to write
         * @throws \Brickoo\Filesystem\Exceptions\HandleNotAvailable if the handle is not initialized
         * @throws \Brickoo\Filesystem\Exceptions\InvalidModeOperation if the current mode does not support write operations
         * @return integer the bytes written
         */
        public function write($data);

        /**
         * Reads the passed bytes of data from the file location.
         * @param integer the amount of bytes to read from
         * @throws \InvalidArgumentException if the argument is not valid
         * @throws \Brickoo\Filesystem\Exceptions\HandleNotAvailable if the handle is not initialized
         * @throws \Brickoo\Filesystem\Exceptions\InvalidModeOperation if the current mode does not support read operations
         * @return string the readed content
         */
        public function read($bytes);

        /**
         * Closes the the data handler and frees the ressource.
         * @throws \Brickoo\Filesystem\Exceptions\HandleNotAvailable if the handle is not initialized
         * @return \Brickoo\Filesystem\Interfaces\Client
         */
        public function close();

    }