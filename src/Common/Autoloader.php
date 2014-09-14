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

namespace Brickoo\Component\Common;

use Brickoo\Component\Common\Exception\DirectoryDoesNotExistException;
use Brickoo\Component\Common\Exception\DuplicateNamespaceRegistrationException;

/**
 * Autoloader
 *
 * Implementation of a namespace based autoloader.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class Autoloader {

    /** @var boolean */
    private $isRegistered;

    /** @var boolean */
    private $prependAutoloader;

    /** @var array */
    private $namespaces;

    /**
     * Class constructor.
     * @param array $namespaces the namespaces to register as namespace => path structure.
     * @param boolean $prepend flag to prepend or append to the PHP autoloader list
     * @throws \InvalidArgumentException if an argument is not valid
     * @throws \Brickoo\Component\Common\Exception\DirectoryDoesNotExistException
     * @throws \Brickoo\Component\Common\Exception\DuplicateNamespaceRegistrationException
     */
    public function __construct(array $namespaces = [], $prepend = true) {
        $this->isRegistered = false;
        $this->prependAutoloader = (boolean)$prepend;
        $this->namespaces = [];

        foreach ($namespaces as $namespace => $includePath) {
            $this->registerNamespace($namespace, $includePath);
        }
        include_once "Exception.php";
    }

    /**
     * Register the autoloader.
     * @return \Brickoo\Component\Common\Autoloader
     */
    public function register() {
        if (! $this->isRegistered) {
            spl_autoload_register([$this, "load"], true, $this->prependAutoloader);
            $this->isRegistered = true;
        }

        return $this;
    }

    /**
     * Unregister the autoloader.
     * @return \Brickoo\Component\Common\Autoloader
     */
    public function unregister() {
        if ($this->isRegistered) {
            spl_autoload_unregister([$this, "load"]);
            $this->isRegistered = false;
        }

        return $this;
    }

    /**
     * Register the namespace to the available namespaces.
     * @param string $namespace the namespace to register
     * @param string $namespacePath the absolute path to the namespace
     * @throws Exception\DirectoryDoesNotExistException
     * @throws Exception\DuplicateNamespaceRegistrationException
     * @throws \InvalidArgumentException if an argument is not valid
     * @return \Brickoo\Component\Common\Autoloader
     */
    public function registerNamespace($namespace, $namespacePath) {
        $this->validateNamespace($namespace);
        $this->validateNamespacePath($namespacePath);

        if ($this->isNamespaceRegistered($namespace)) {
            require_once "Exception".DIRECTORY_SEPARATOR."DuplicateNamespaceRegistrationException.php";
            throw new DuplicateNamespaceRegistrationException($namespace);
        }

        $this->namespaces[$namespace] = rtrim($namespacePath, "/\\");
        return $this;
    }

    /**
     * Check if the given namespace has been registered.
     * @param string $namespace the namespace to check
     * @throws \InvalidArgumentException if an argument is not valid
     * @return boolean check result
     */
    public function isNamespaceRegistered($namespace) {
        $this->validateNamespace($namespace);
        return array_key_exists($namespace, $this->namespaces);
    }

    /**
     * Return the registered namespaces.
     * @return array the registered namespaces
     */
    public function getRegisteredNamespaces() {
        return $this->namespaces;
    }

    /**
     * Load the requested class.
     * Commonly this is the auto loader callback function registered.
     * @param string $className the class to load
     * @return boolean true on success false on failure
     */
    public function load($className) {
        if (($namespaceClassPath = $this->getNamespaceClassPath($className)) === null
            || (! file_exists($namespaceClassPath))) {
                return false;
        }

        include $namespaceClassPath;
        return true;
    }

    /**
     * Validate the namespace.
     * @param string $namespace
     * @throws \InvalidArgumentException
     * @return void
     */
    private function validateNamespace($namespace) {
        if (! is_string($namespace)) {
            throw new \InvalidArgumentException("Invalid namespace argument used.");
        }
    }

    /**
     * Validate the namespace path.
     * @param string $namespacePath
     * @throws \Brickoo\Component\Common\Exception\DirectoryDoesNotExistException
     * @throws \InvalidArgumentException
     * @return void
     */
    private function validateNamespacePath($namespacePath) {
        if (! is_string($namespacePath)) {
            throw new \InvalidArgumentException("Invalid namespace path argument used.");
        }

        if (! is_dir($namespacePath)) {
            require_once "Exception".DIRECTORY_SEPARATOR."DirectoryDoesNotExistException.php";
            throw new DirectoryDoesNotExistException($namespacePath);
        }
    }

    /**
     * Returns the path for a namespace class.
     * @param string $className
     * @return string|null the namespace based path otherwise null
     */
    private function getNamespaceClassPath($className) {
        $chosenPath = "";
        $chosenNamespace = "";

        foreach($this->namespaces as $namespace => $path) {
            if ((strpos($className, $namespace) === 0)
                && strlen($chosenNamespace) < strlen($namespace)) {
                    $chosenNamespace = $namespace;
                    $chosenPath = $path;
            }
        }
        return $this->createClassPath($className, $chosenNamespace, $chosenPath);
    }

    /**
     * Create the class filesystem loader path.
     * @param string $className
     * @param string $chosenNamespace
     * @param string $chosenPath
     * @return null|string the class path otherwise null
     */
    private function createClassPath($className, $chosenNamespace, $chosenPath) {
        if (empty($chosenNamespace) || empty($chosenPath)) {
            return null;
        }
        return $chosenPath.$this->getTranslatedClassPath(substr($className, strlen($chosenNamespace)));
    }

    /**
     * Returns a translated namespace class to filesystem path.
     * @param string $className class including namespace
     * @return string the translated class path
     */
    private function getTranslatedClassPath($className) {
        return DIRECTORY_SEPARATOR.str_replace("\\", DIRECTORY_SEPARATOR, $className).".php";
    }

}
