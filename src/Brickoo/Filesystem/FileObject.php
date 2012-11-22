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

    namespace Brickoo\Filesystem;

    use Brickoo\Validator\Argument;

    /**
     * FileObject
     *
     * Implements an OOP wrapper for handling file operations.
     * The SplFileObject has not an implementation for changing or just close
     * and open the location anytime within an instance, that`s the reason why did this version has been created.
     * The resource handle is created and closed by the FileObject,
     * that`s the reason why fopen() and fclose() are not supported as magic method.
     * This class does not implement all functions available for file handling,
     * BUT(!) you can use any file function which expects the resource handle as first argument.
     * Examples:
     * <code>
     *     // Not implemented fseek() and fread() but supported, notify the handle handle is not passed !
     *     $content = "";
     *     $FileObject =  new Brickoo\Filesystem\FileObject();
     *     $FileObject->open("/path/to/file.txt", "r");
     *     $FileObject->fseek(100);
     *     $content .= $FileObject->fread(1024);
     *     $FileObject->close();
     *     var_dump($content);
     *
     *     // Not implemented feof() but supported, reading a file until end of file
     *     $content = "";
     *     $FileObject =  new Brickoo\Filesystem\FileObject();
     *     $FileObject->open("/path/to/file.txt", "r");
     *     while(! $FileObject->feof()) {
     *         $content .= $FileObject->read(1024);
     *     }
     *     $FileObject->close();
     *     var_dump($content);
     * </code>
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class FileObject implements Interfaces\FileObject {

        /** @var string */
        private $mode;

        /** @var resource */
        private $handle;

        /** {@inheritDoc} */
        public function open($filename, $mode, $useIncludePath = false, $context = null) {
            if ($this->hasHandle()) {
                throw new Exceptions\HandleAlreadyExists();
            }

            Argument::IsString($filename);
            Argument::IsString($mode);
            Argument::IsBoolean($useIncludePath);

            if ($context !== null && (! $handle = @fopen($filename, $mode, $useIncludePath, $context))) {
                throw new Exceptions\UnableToCreateHandle($filename, $mode);
            }

            if ($context == null && (! $handle = @fopen($filename, $mode, $useIncludePath))) {
                throw new Exceptions\UnableToCreateHandle($filename, $mode);
            }

            $this->handle = $handle;
            $this->mode = $mode;
            return $this;
        }

        /** {@inheritDoc} */
        public function write($data) {
            if ($this->mode == "r") {
                throw new Exceptions\InvalidModeOperation($this->mode, "write");
            }

            return fwrite($this->getHandle(), $data);
        }

        /** {@inheritDoc} */
        public function read($bytes) {
            Argument::IsInteger($bytes);

            if (preg_match("~^[waxc]$~", $this->mode)) {
                throw new Exceptions\InvalidModeOperation($this->mode, "read");
            }

            return fread($this->getHandle(), $bytes);
        }

        /** {@inheritDoc} */
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
         * Returns the resoruce file handle.
         * @throws \Brickoo\Filesystem\Exceptions\HandleNotAvailable
         * @return resource file handle
         */
        private function getHandle() {
            if (! $this->hasHandle()) {
                throw new Exceptions\HandleNotAvailable();
            }

            return $this->handle;
        }

        /**
         * Provides the posibility to call not implemented file functions.
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