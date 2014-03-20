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

namespace Brickoo\Component\Filesystem;

use Brickoo\Component\Filesystem\Exception\HandleAlreadyExistsException,
    Brickoo\Component\Filesystem\Exception\HandleNotAvailableException,
    Brickoo\Component\Filesystem\Exception\InvalidModeOperationException,
    Brickoo\Component\Filesystem\Exception\UnableToCreateHandleException,
    Brickoo\Component\Validation\Argument;

/**
 * FileObject
 *
 * Implements an OOP wrapper for handling file operations.
 * The SplFileObject has not an implementation for changing or just close
 * and open the location anytime within an instance, that`s the reason why this version has been created.
 * The resource handle is created and closed by the instance,
 * that`s the reason why fopen() and fclose() are not supported as magic method.
 * This class does not implement all functions available for file handling,
 * BUT(!) you can use any file function which expects the resource handle as first argument.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class File {

    /** @var string */
    private $mode;

    /** @var resource */
    private $handle;

    /**
     * Opens the file to create the handle of a resource.
     * @see http://www.php.net/manual/en/function.fopen.php
     * @param string $filename the file name of the resource to open
     * @param string $mode the mode to use for opening the resource
     * @param boolean $useIncludePath flag to also use the include path as search location
     * @param resource|null $context the context created to use
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Filesystem\Exception\UnableToCreateHandleException
     * @throws \Brickoo\Component\Filesystem\Exception\HandleAlreadyExistsException
     * @return \Brickoo\Component\Filesystem\File
     */
    public function open($filename, $mode, $useIncludePath = false, $context = null) {
        if ($this->hasHandle()) {
            throw new HandleAlreadyExistsException();
        }

        Argument::IsString($filename);
        Argument::IsString($mode);
        Argument::IsBoolean($useIncludePath);
        $handle = null;

        if ($context !== null && (! $handle = @fopen($filename, $mode, $useIncludePath, $context))) {
            throw new UnableToCreateHandleException($filename, $mode);
        }

        if ($context == null && (! $handle = @fopen($filename, $mode, $useIncludePath))) {
            throw new UnableToCreateHandleException($filename, $mode);
        }

        $this->handle = $handle;
        $this->mode = $mode;
        return $this;
    }

    /**
     * Writes the data to the file.
     * @param mixed $data the data to write
     * @throws \Brickoo\Component\Filesystem\Exception\HandleNotAvailableException
     * @throws \Brickoo\Component\Filesystem\Exception\InvalidModeOperationException
     * @return integer the bytes written
     */
    public function write($data) {
        if ($this->mode == "r") {
            throw new InvalidModeOperationException($this->mode, "write");
        }

        return fwrite($this->getHandle(), $data);
    }

    /**
     * Reads the passed bytes of data from the file location.
     * @param integer $bytes the amount of bytes to read from
     * @throws \InvalidArgumentException if the argument is not valid
     * @throws \Brickoo\Component\Filesystem\Exception\HandleNotAvailableException
     * @throws \Brickoo\Component\Filesystem\Exception\InvalidModeOperationException
     * @return string the read content
     */
    public function read($bytes) {
        Argument::IsInteger($bytes);

        if (! $this->isReadMode()) {
            throw new InvalidModeOperationException($this->mode, "read");
        }

        return fread($this->getHandle(), $bytes);
    }

    /**
     * Read a line of the file.
     * @throws InvalidModeOperationException
     * @return string the read line
     */
    public function readLine() {
        if (! $this->isReadMode()) {
            throw new InvalidModeOperationException($this->mode, "read");
        }

        return fgets($this->getHandle());
    }

    /**
     * Check if the end of file has been reached.
     * @return boolean check result
     */
    public function isEndOfFile() {
        return feof($this->getHandle());
    }

    /**
     * Closes the the data handler and frees the resource.
     * @throws \Brickoo\Component\Filesystem\Exception\HandleNotAvailableException
     * @return \Brickoo\Component\Filesystem\File
     */
    public function close() {
        fclose($this->getHandle());
        $this->handle = null;
        return $this;
    }

    /**
     * Removes the resource handle if available.
     * @return void
     */
    public function __destruct() {
        if ($this->hasHandle()) {
            $this->close();
        }
    }

    /**
     * Checks if a handle has been created.
     * @return boolean check result
     */
    private function hasHandle() {
        return is_resource($this->handle);
    }

    /**
     * Returns the resource file handle.
     * @throws \Brickoo\Component\Filesystem\Exception\HandleNotAvailableException
     * @return resource file handle
     */
    private function getHandle() {
        if (! $this->hasHandle()) {
            throw new HandleNotAvailableException();
        }

        return $this->handle;
    }

    /**
     * Checks if the file mode is readable.
     * @return boolean check result
     */
    private function isReadMode() {
        return preg_match("~^[waxc]$~", $this->mode) == 0;
    }

    /**
     * Provides the possibility to call not implemented file functions.
     * @param string $function the function name to call
     * @param array $arguments the arguments to pass
     * @throws \BadMethodCallException if the trying to call fopen() or fclose()
     * @return mixed the called function return value
     */
    public function __call($function, array $arguments) {
        if (($function == "fopen") || ($function == "fclose")) {
            throw new \BadMethodCallException(
                sprintf("The method `%s` is not allowed to be called.", $function)
            );
        }
        return call_user_func_array($function, array_merge(array($this->handle), $arguments));
    }

}