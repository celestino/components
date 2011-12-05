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

    namespace Brickoo\Library\Core;

    use Brickoo\Library\Core\Exception\AutoloaderException;

    // Autoloader Exception
    require_once ('Exception/AutoloaderException.php');

    /**
     * Autoloader
     *
     * Autoloader with registration for different namespaces.
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id: $
     */

    class Autoloader
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
         * @throws AutoloaderException if the namespace is already registered
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
            )
            {
                throw new \InvalidArgumentException('Invalid arguments used.', E_ERROR);
            }

            if ($this->isNamespaceRegistered($namespace))
            {
                throw new AutoloaderException('Namespace is already registered.', E_ERROR);
            }

            $this->namespaces[strtoupper($namespace)] = $namespacePath;

            return $this;
        }

        /**
         * Unregister the namespace available by the given name.
         * @param string $namespace the name of the namespace to remove
         * @throws AutoloaderException if the namespace is not registered
         * @return object reference
         */
        public function unregisterNamespace($namespace)
        {
            if (! $this->isNamespaceRegistered($namespace))
            {
                throw new AutoloaderException('Namespace is not registered.', E_ERROR);
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
            if
            (
                (! is_string($namespace)) ||
                (! $namespace = trim($namespace))
            )
            {
                throw new \InvalidArgumentException('Invalid arguments used.', E_ERROR);
            }

            return in_array(strtoupper($namespace), $this->getAvailableNamespaces());
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
         * @throws AutoloaderException if the $namespace is not registered.
         * @return string the namespace path or false if the namespace is not registered
         */
        public function getNamespacePath($namespace)
        {
            if (! is_string($namespace))
            {
                throw new \InvalidArgumentException('Invalid arguments used', E_ERROR);
            }

            if (! $this->isNamespaceRegistered($namespace))
            {
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
            $this->isRegistered = false;
            $this->clearNamespaces();
        }

        /**
         * Clears the current assigned namespaces.
         * @return object reference
         */
        public function clearNamespaces()
        {
            $this->namespaces = array();

            return $this;
        }

        /**
         * Returns the absolute path for the requested class.
         * It requires an registered namespace.
         * @param string $className the class to retrieve the path for
         * @throws InvalidArgumentException if the class name is not a string
         * @throws AutoloaderException if the class path is too short
         * @return string the absolute file path or false if the namespace is not registered
         */
        public function getAbsolutePath($className)
        {
            if
            (
                (! is_string($className)) ||
                (! $className = trim($className))
            )
            {
                throw new \InvalidArgumentException('Invalid arguments used', E_ERROR);
            }

            if
            (
                (! preg_match('~^(?<ns>[\w]+)(?:/|\\\\)(?<classPath>[\w/\\\\]+)$~i', $className, $matches)) ||
                (! $namespacePath = $this->getNamespacePath($matches['ns']))
            )
            {
                return false;
            }

            $namespacePath = rtrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $namespacePath), DIRECTORY_SEPARATOR);

            return $namespacePath . DIRECTORY_SEPARATOR . $matches['classPath'] .'.php';
        }

        /**
         * Requires the given class by using the assigned namespaces.
         * @param string $className the class name to require containing the namespace
         * @throws AutoloaderException if the class name is not registered
         * @throws RuntimeException throws an exception if the file does not exists
         * @return boolean success
         */
        public function load($className)
        {
            if(! $absolutePath = $this->getAbsolutePath($className))
            {
                return false;
            }

            if (! file_exists($absolutePath))
            {
                throw new AutoloaderException('File does not exists : '. $absolutePath, E_ERROR);
            }

            require_once ($absolutePath);

            return true;
        }

        /**
         * Registers the object method Autoloader:load for autoloading.
         * @throws AutoloaderException if the autoloader is already registered
         * @return object reference
         */
        public function registerAutoloader()
        {
            if ($this->isRegistered)
            {
                throw new AutoloaderException('This is already registered as autoloader', E_ERROR);
            }

            spl_autoload_register(array($this, 'load'));
            $this->isRegistered = true;

            return $this;
        }

        /**
         * Unregisters the object from the autoloading.
         * @throws AutoloaderException if the autoloader did not be registered
         * @return object reference
         */
        public function unregisterAutoloader()
        {
            if (! $this->isRegistered)
            {
                throw new AutoloaderException('This is not registered as autoloader', E_ERROR);
            }

            spl_autoload_unregister(array($this, 'load'));
            $this->isRegistered = false;

            return $this;
        }

    }

?>