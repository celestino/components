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

    namespace Brickoo\Library\Core;

    use Brickoo\Library\Core\Exceptions;

    require_once 'Interfaces\AutoloaderInterface.php';

    /**
     * Autoloader
     *
     * Autoloader to register different namespaces which need autoloading.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Autoloader implements Interfaces\AutoloaderInterface
    {

        /**
         * Holds the status of registration to php autoloader.
         * @var boolean
         */
        protected $isRegistered;

        /**
         * Holds the current assigned namespaces.
         * @var array
         */
        protected $namespaces;

        /**
         * Register the namespace to the available namespaces.
         * @param string $namespace the namespace to register
         * @param string $namespacePath the absolute path to the namespace
         * @throws InvalidArgumentException if passed arguments are not valid
         * @throws DuplicateNamespaceRegistrationException if the namespace is already registered
         * @return object reference
         */
        public function registerNamespace($namespace, $namespacePath)
        {
            if
            (
                (! is_string($namespace)) ||
                (! $namespace = trim($namespace)) ||
                (! is_string($namespacePath)) ||
                (! is_dir($namespacePath))
            ) {
                throw new \InvalidArgumentException('Invalid arguments used.');
            }

            if ($this->isNamespaceRegistered($namespace)) {
                require_once 'Exceptions/DuplicateNamespaceRegistrationException.php';
                throw new Exceptions\DuplicateNamespaceRegistrationException($namespace);
            }

            $this->namespaces[strtoupper($namespace)] = $namespacePath;

            return $this;
        }

        /**
         * Unregister the namespace available by the given name.
         * @param string $namespace the name of the namespace to remove
         * @throws NamspaceNotRegisteredException if the namespace is not registered
         * @return object reference
         */
        public function unregisterNamespace($namespace)
        {
            if (! $this->isNamespaceRegistered($namespace)) {
                require_once 'Exceptions/NamespaceNotRegisteredException.php';
                throw new Exceptions\NamespaceNotRegisteredException($namespace);
            }

            unset($this->namespaces[strtoupper($namespace)]);

            return $this;
        }

        /**
         * Checks if the given namespace name has been registered.
         * @param string $namespace the namespace to check
         * @throws InvalidArgumentException if passed argument is not valid
         * @return boolean check result
         */
        public function isNamespaceRegistered($namespace)
        {
            if ((! is_string($namespace)) || (! $namespace = trim($namespace))) {
                throw new \InvalidArgumentException('Invalid arguments used.');
            }

            return array_key_exists(strtoupper($namespace), $this->namespaces);
        }

        /**
         * Returns the available namespaces.
         * @return array the available namespaces
         */
        public function getAvailableNamespaces()
        {
            return array_keys($this->namespaces);
        }

        /**
         * Returns the namespace path of the assigned namespace name.
         * @param string $namespace the namespace name to return the path from
         * @throws InvalidArgumentException if the passe namespace is not a string
         * @return string the namespace path or false if the namespace is not registered
         */
        public function getNamespacePath($namespace)
        {
            if (! is_string($namespace)) {
                throw new \InvalidArgumentException('Invalid arguments used');
            }

            if (! $this->isNamespaceRegistered($namespace)) {
                return false;
            }

            return $this->namespaces[strtoupper($namespace)];
        }

        /**
         * Class constructor.
         * Intializes the class properties.
         * @return void
         */
        public function __construct()
        {
            $this->isRegistered    = false;
            $this->namespaces      = array();
        }

        /**
         * Returns the absolute path for the requested class.
         * It requires an registered namespace.
         * @param string $className the class to retrieve the path for
         * @throws InvalidArgumentException if the class name is not a string
         * @return string the absolute file path or false if the namespace is not registered
         */
        public function getAbsolutePath($className)
        {
            if ((! is_string($className)) || (! $className = trim($className))) {
                throw new \InvalidArgumentException('Invalid arguments used');
            }

            if
            (
                (! preg_match('~^(?<ns>[\w]+)(?:/|\\\\)(?<classPath>[\w/\\\\]+)$~i', $className, $matches)) ||
                (! $namespacePath = $this->getNamespacePath($matches['ns']))
            ) {
                return false;
            }

            $namespacePath = rtrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $namespacePath), DIRECTORY_SEPARATOR);

            return $namespacePath . DIRECTORY_SEPARATOR . $matches['classPath'] .'.php';
        }

        /**
         * Requires the given class by using the assigned namespaces.
         * @param string $className the class name to require containing the namespace
         * @throws AutoloadFileDoesNotExistException throws an exception if the file does not exists
         * @return boolean success
         */
        public function loadClass($className)
        {
            if(! $absolutePath = $this->getAbsolutePath($className)) {
                return false;
            }

            if (! file_exists($absolutePath)) {
                require_once 'Exceptions/AutoloadFileDoesNotExistException.php';
                throw new Exceptions\AutoloadFileDoesNotExistException($absolutePath);
            }

            require ($absolutePath);

            return true;
        }

        /**
         * Registers the instance with method Autoloader:loadClass for autoloading.
         * @throws DuplicateAutoloaderRegistrationException if the autoloader is already registered
         * @return object reference
         */
        public function register()
        {
            if ($this->isRegistered) {
                require_once 'Exceptions/DuplicateAutoloaderRegistrationException.php';
                throw new Exceptions\DuplicateAutoloaderRegistrationException();
            }

            spl_autoload_register(array($this, 'loadClass'));
            $this->isRegistered = true;

            return $this;
        }

        /**
         * Unregisters the instance from the autoloading.
         * @throws AutoloaderNotRegisteredExeption if the autoloader did not be registered
         * @return object reference
         */
        public function unregister()
        {
            if (! $this->isRegistered) {
                require_once 'Exceptions/AutoloaderNotRegisteredExeption.php';
                throw new Exceptions\AutoloaderNotRegisteredExeption();
            }

            spl_autoload_unregister(array($this, 'load'));
            $this->isRegistered = false;

            return $this;
        }

    }