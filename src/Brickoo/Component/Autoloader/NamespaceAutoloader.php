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

namespace Brickoo\Component\Autoloader;

use Brickoo\Component\Autoloader\Exception\DirectoryDoesNotExistException,
    Brickoo\Component\Autoloader\Exception\DuplicateNamespaceRegistrationException,
    Brickoo\Component\Autoloader\Exception\FileDoesNotExistException,
    Brickoo\Component\Autoloader\Exception\NamespaceNotRegisteredException;

/**
 * NamespaceAutoloader
 *
 * Implementation of an autoloader to register namespaces based class loading.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class NamespaceAutoloader extends Autoloader {

    /** @var array */
    private $namespaces;

    /**
     * Class constructor.
     * @param array $namespaces the namespaces to register as namespace => path structure.
     * @param boolean $prepend flag to prepend or append to the PHP autoloader list
     * @throws \InvalidArgumentException if an argument is not valid
     * @throws \Brickoo\Component\Autoloader\Exception\DirectoryDoesNotExistException
     * @throws \Brickoo\Component\Autoloader\Exception\DuplicateNamespaceRegistrationException
     * @return void
     */
    public function __construct(array $namespaces = [], $prepend = true) {
        parent::__construct($prepend);
        $this->namespaces = array();

        foreach ($namespaces as $namespace => $includePath) {
            $this->registerNamespace($namespace, $includePath);
        }
        include_once "Exception.php";
    }

    /**
     * Register the namespace to the available namespaces.
     * @param string $namespace the namespace to register
     * @param string $namespacePath the absolute path to the namespace
     * @throws \InvalidArgumentException if an argument is not valid
     * @throws \Brickoo\Component\Autoloader\Exception\DirectoryDoesNotExistException
     * @throws \Brickoo\Component\Autoloader\Exception\DuplicateNamespaceRegistrationException
     * @return \Brickoo\Component\Autoloader\NamespaceAutoloader
     */
    public function registerNamespace($namespace, $includePath) {
        if ((! is_string($namespace)) || (! $namespace = trim($namespace, "\\")) || (! is_string($includePath))) {
            throw new \InvalidArgumentException("Invalid arguments used.");
        }

        if (! is_dir($includePath)) {
            require_once "Exception".DIRECTORY_SEPARATOR."DirectoryDoesNotExistException.php";
            throw new DirectoryDoesNotExistException($includePath);
        }

        if ($this->isNamespaceRegistered($namespace)) {
            require_once "Exception".DIRECTORY_SEPARATOR."DuplicateNamespaceRegistrationException.php";
            throw new DuplicateNamespaceRegistrationException($namespace);
        }

        $this->namespaces[$namespace] = rtrim($includePath, "/\\");
        return $this;
    }

    /**
     * Unregister the namespace available by the given name.
     * @param string $namespace the name of the namespace to remove
     * @throws \InvalidArgumentException if an argument is not valid
     * @throws \Brickoo\Component\Autoloader\Exception\NamespaceNotRegisteredException
     * @return \Brickoo\Component\Autoloader\NamespaceAutoloader
     */
    public function unregisterNamespace($namespace) {
        if (! $this->isNamespaceRegistered($namespace)) {
            require_once "Exception".DIRECTORY_SEPARATOR."NamespaceNotRegisteredException.php";
            throw new NamespaceNotRegisteredException($namespace);
        }

        unset($this->namespaces[$namespace]);
        return $this;
    }

    /**
     * Checks if the given namespace has been registered.
     * @param string $namespace the namespace to check
     * @throws \InvalidArgumentException if an argument is not valid
     * @return boolean check result
     */
    public function isNamespaceRegistered($namespace) {
        if ((! is_string($namespace)) || (! $namespace = trim($namespace))) {
            throw new \InvalidArgumentException("Invalid namespace argument used.");
        }

        return array_key_exists($namespace, $this->namespaces);
    }

    /**
     * Returns the registered namespaces.
     * @return array the registered namespaces
     */
    public function getRegisteredNamespaces() {
        return $this->namespaces;
    }

    /** {@inheritDoc} */
    public function load($className) {
        if ((! is_string($className)) || (! $className = trim($className, "\\"))) {
            throw new \InvalidArgumentException("Invalid class argument used.");
        }

        if (($absolutePath = $this->getAbsolutePath($className)) === null) {
            return false;
        }

        if ((! file_exists($absolutePath))) {
            return false;
        }

        include ($absolutePath);
        return true;
    }

    /**
     * Returns the absolute path for the requested class.
     * @param string $className the class to retrieve the path from
     * @return string the absolute file path or null if the namespace is not registered
     */
    private function getAbsolutePath($className) {
        $namespacePath = $this->getNamespaceClassPath($className);

        if ($namespacePath !== null) {
            return $namespacePath . $this->getTranslatedClassPath($className);
        }

        return null;
    }

    /**
     * Returns the path for a namespaced class.
     * @param string $className
     * @return string the namespace based path otherwise null
     */
    private function getNamespaceClassPath($className) {
        $namespacePath = null;
        $choosedNamespace = null;

        foreach($this->namespaces as $namespace => $path) {
            if ((strpos($className, $namespace) === 0)
                && (($choosedNamespace === null)
                    || (strlen($choosedNamespace) < strlen($namespace)))
            ){
                $choosedNamespace = $namespace;
                $namespacePath = substr($path, 0, strlen($namespace)*-1);
            }
        }
        return $namespacePath;
    }

    /**
     * Returns a translated namespaced class to fileystem path.
     * @param string $className class including namespace
     * @return string the translated class path
     */
    private function getTranslatedClassPath($className) {
        return DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, $className) .".php";
    }

}