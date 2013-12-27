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

namespace Brickoo\Autoloader;

use Brickoo\Autoloader\Exception\ClassNotRegisteredException,
    Brickoo\Autoloader\Exception\DuplicateClassRegistrationException,
    Brickoo\Autoloader\Exception\FileDoesNotExistException;


/**
 * ListAutoloader
 *
 * Implementation of an autoloader to register mapping classes to a location.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ListAutoloader extends Autoloader {

    /** @var array */
    private $classes;

    /**
     * Class constructor.
     * @param array $classes the class to register as className, location pairs.
     * @param boolean $prepend flag to prepend or append to the PHP autoloader list
     * @return void
     */
    public function __construct(array $classes = array(), $prepend = true) {
        $this->classes = $classes;
        parent::__construct($prepend);
    }

    /**
     * Register the class with the corresponding location.
     * @param string $className the class to register
     * @param string $location the absoulte location path to the class
     * @throws \InvalidArgumentException if an argument is not valid
     * @throws \Brickoo\Autoloader\Exception\FileDoesNotExistException
     * @throws \Brickoo\Autoloader\Exception\DuplicateClassRegistrationException
     * @return \Brickoo\Autoloader\ListAutoloader
     */
    public function registerClass($className, $location) {
        if ((! is_string($className)) || empty($className) || (! is_string($location))) {
            throw new \InvalidArgumentException("The arguments should be non empty strings.");
        }

        if (! file_exists($location)) {
            require_once "Exception".DIRECTORY_SEPARATOR."FileDoesNotExistException.php";
            throw new FileDoesNotExistException($location);
        }

        if ($this->isClassRegistered($className)) {
            require_once "Exception".DIRECTORY_SEPARATOR."DuplicateClassRegistrationException.php";
            throw new DuplicateClassRegistrationException($className);
        }

        $this->classes[$className] = $location;
        return $this;
    }

    /**
     * Unregister the class available by the given name.
     * @param string $class the class to unregister from autoloader
     * @throws \InvalidArgumentException if an argument is not valid
     * @throws \Brickoo\Autoloader\Exception\ClassNotRegisteredException
     * @return \Brickoo\Autoloader\ListAutoloader
     */
    public function unregisterClass($className) {
        if (! is_string($className)) {
            throw new \InvalidArgumentException(sprintf("The class name `%s` is not valid", $className));
        }

        if (! $this->isClassRegistered($className)) {
            require_once "Exception".DIRECTORY_SEPARATOR."ClassNotRegisteredException.php";
            throw new ClassNotRegisteredException($className);
        }

        unset($this->classes[$className]);
        return $this;
    }

    /**
     * Checks if the given class has been registered.
     * @param string $className the class to check
     * @throws \InvalidArgumentException if an argument is not valid
     * @return boolean check result
     */
    public function isClassRegistered($className) {
        if (! is_string($className)) {
            throw new \InvalidArgumentException(sprintf("The class name `%s` is not valid", $className));
        }

        return isset($this->classes[$className]);
    }

    /**
     * Returns the registered classes.
     * @return array the registered classes
     */
    public function getRegisteredClasses() {
        return $this->classes;
    }

    /** {@inheritDoc} */
    public function load($className) {
        if (! is_string($className)) {
            throw new \InvalidArgumentException(sprintf("The class name `%s` is not valid", $className));
        }

        if (! $this->isClassRegistered($className)) {
            return false;
        }

        $classFilePath = $this->classes[$className];

        if ((! file_exists($classFilePath))) {
            require_once "Exception".DIRECTORY_SEPARATOR."FileDoesNotExistException.php";
            throw new FileDoesNotExistException($classFilePath);
        }

        require $classFilePath;
        return true;
    }

}