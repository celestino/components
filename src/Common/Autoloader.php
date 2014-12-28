<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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
        $translatedClassName = str_replace("_", DIRECTORY_SEPARATOR, $className);
        return DIRECTORY_SEPARATOR.str_replace("\\", DIRECTORY_SEPARATOR, $translatedClassName).".php";
    }

}
