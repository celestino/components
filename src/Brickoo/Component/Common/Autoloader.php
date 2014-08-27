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

use Brickoo\Component\Common\Exception\DuplicateAutoloaderRegistrationException;
use Brickoo\Component\Common\Exception\AutoloaderNotRegisteredException;
use Brickoo\Component\Common\Exception\DirectoryDoesNotExistException;
use Brickoo\Component\Common\Exception\DuplicateNamespaceRegistrationException;
use Brickoo\Component\Common\Exception\NamespaceNotRegisteredException;

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
     * @throws \Brickoo\Component\Common\Exception\DuplicateAutoloaderRegistrationException
     * @return \Brickoo\Component\Common\Autoloader
     */
    public function register() {
        if ($this->isRegistered) {
            require_once "Exception".DIRECTORY_SEPARATOR."DuplicateAutoloaderRegistrationException.php";
            throw new DuplicateAutoloaderRegistrationException();
        }

        spl_autoload_register([$this, "load"], true, $this->prependAutoloader);
        $this->isRegistered = true;

        return $this;
    }

    /**
     * Unregister the autoloader.
     * @throws \Brickoo\Component\Common\Exception\AutoloaderNotRegisteredException
     * @return \Brickoo\Component\Common\Autoloader
     */
    public function unregister() {
        if (! $this->isRegistered) {
            require_once "Exception".DIRECTORY_SEPARATOR."AutoloaderNotRegisteredException.php";
            throw new AutoloaderNotRegisteredException();
        }

        spl_autoload_unregister([$this, "load"]);
        $this->isRegistered = false;

        return $this;
    }

    /**
     * Register the namespace to the available namespaces.
     * @param string $namespace the namespace to register
     * @param string $includePath the absolute path to the namespace
     * @throws Exception\DirectoryDoesNotExistException
     * @throws Exception\DuplicateNamespaceRegistrationException
     * @throws \InvalidArgumentException if an argument is not valid
     * @return \Brickoo\Component\Common\Autoloader
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
     * @throws \Brickoo\Component\Common\Exception\NamespaceNotRegisteredException
     * @return \Brickoo\Component\Common\Autoloader
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

    /**
     * Loads the requested class.
     * Commonly this is the auto loader callback function registered.
     * @param string $className the class to load
     * @throws \InvalidArgumentException if an argument is not valid
     * @return boolean true on success false on failure
     */
    public function load($className) {
        if ((! is_string($className)) || (! $className = trim($className, "\\"))) {
            throw new \InvalidArgumentException("Invalid class argument used.");
        }

        if (($namespaceClassPath = $this->getNamespaceClassPath($className)) === null) {
            return false;
        }

        if ((! file_exists($namespaceClassPath))) {
            return false;
        }

        include ($namespaceClassPath);
        return true;
    }

    /**
     * Returns the path for a namespace class.
     * @param string $className
     * @return string|null the namespace based path otherwise null
     */
    private function getNamespaceClassPath($className) {
        $namespaceClassPath = null;
        $chosenNamespace = null;

        foreach($this->namespaces as $namespace => $path) {
            if ((strpos($className, $namespace) === 0)
                && (($chosenNamespace === null)
                    || (strlen($chosenNamespace) < strlen($namespace)))
            ){
                $chosenNamespace = $namespace;
                $namespaceClassPath = $path.$this->getTranslatedClassPath(substr($className, strlen($namespace)));
            }
        }
        return $namespaceClassPath;
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
