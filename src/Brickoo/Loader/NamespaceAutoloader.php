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

    namespace Brickoo\Loader;

    require_once 'Interfaces/NamespaceAutoloader.php';

    /**
     * NamespaceAutoloader
     *
     * Implementation of an autoloader to register namespaces based classes.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class NamespaceAutoloader extends Autoloader implements Interfaces\NamespaceAutoloader {

        /** @var array */
        private $namespaces;

        /**
         * Class constructor.
         * @param array $namespaces the namespaces to register as namespace => path structure.
         * @param boolean $prepend flag to prepend or append to the PHP autoloader list
         * @return void
         */
        public function __construct(array $namespaces = array(), $prepend = true) {
            $this->namespaces = $namespaces;
            parent::__construct($prepend);
        }

        /** {@inheritDoc} */
        public function registerNamespace($namespace, $includePath) {
            if ((! is_string($namespace)) || (! $namespace = trim($namespace)) || (! is_string($includePath))) {
                throw new \InvalidArgumentException('Invalid arguments used.');
            }

            if (! is_dir($includePath)) {
                require_once 'Exceptions/DirectoryDoesNotExist.php';
                throw new Exceptions\DirectoryDoesNotExist($includePath);
            }

            if ($this->isNamespaceRegistered($namespace)) {
                require_once 'Exceptions/DuplicateNamespaceRegistration.php';
                throw new Exceptions\DuplicateNamespaceRegistration($namespace);
            }

            $this->namespaces[$namespace] = rtrim($includePath, '/\\');

            return $this;
        }

        /** {@inheritDoc} */
        public function unregisterNamespace($namespace) {
            if (! $this->isNamespaceRegistered($namespace)) {
                require_once 'Exceptions/NamespaceNotRegistered.php';
                throw new Exceptions\NamespaceNotRegistered($namespace);
            }

            unset($this->namespaces[$namespace]);

            return $this;
        }

        /** {@inheritDoc} */
        public function isNamespaceRegistered($namespace) {
            if ((! is_string($namespace)) || (! $namespace = trim($namespace))) {
                throw new \InvalidArgumentException('Invalid namespace argument used.');
            }

            return array_key_exists($namespace, $this->namespaces);
        }

        /** {@inheritDoc} */
        public function getRegisteredNamespaces() {
            return $this->namespaces;
        }

        /** {@inheritDoc} */
        public function load($className) {
            if ((! is_string($className)) || (! $className = trim($className, '\\'))) {
                throw new \InvalidArgumentException('Invalid class argument used.');
            }

            if (($absolutePath = $this->getAbsolutePath($className)) === null) {
                return false;
            }

            if ((! file_exists($absolutePath))) {
                require_once 'Exceptions/FileDoesNotExist.php';
                throw new Exceptions\FileDoesNotExist($absolutePath);
            }

            require ($absolutePath);
            return true;
        }

        /**
         * Returns the absolute path for the requested class.
         * @param string $className the class to retrieve the path for
         * @return string the absolute file path or null if the namespace is not registered
         */
        private function getAbsolutePath($className) {
            $namespaceDirectory = null;

            foreach($this->namespaces as $namespace => $directory) {
                if (strpos($className, $namespace) === 0) {
                    $namespaceDirectory = $directory;
                    break;
                }
            }

            if ($namespaceDirectory === null) {
                return null;
            }

            return $namespaceDirectory . DIRECTORY_SEPARATOR . str_replace(array('\\'), DIRECTORY_SEPARATOR, $className) .'.php';
        }

    }