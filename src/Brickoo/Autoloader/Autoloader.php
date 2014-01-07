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

namespace Brickoo\Autoloader;

use Brickoo\Autoloader\Exception\DuplicateAutoloaderRegistrationException,
    Brickoo\Autoloader\Exception\AutoloaderNotRegisteredException;

/**
 * Autoloader
 *
 * Abstract implementation of an autoloader.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

abstract class Autoloader {


    /** @var boolean */
    private $isRegistered;

    /** @var boolean*/
    private $prependAutoloader;

    /**
     * Class constructor.
     * @param boolean $prepend flag to prepend or append to the PHP autoloader list
     * @return void
     */
    public function __construct($prepend = true) {
        $this->isRegistered = false;
        $this->prependAutoloader = (boolean)$prepend;
    }

    /**
     * Register the autoloader.
     * @throws \Brickoo\Autoloader\Exception\DuplicateAutoloaderRegistrationException
     * @return \Brickoo\Autoloader\Autoloader
     */
    public function register() {
        if ($this->isRegistered) {
            require_once "Exception".DIRECTORY_SEPARATOR."DuplicateAutoloaderRegistrationException.php";
            throw new DuplicateAutoloaderRegistrationException();
        }

        spl_autoload_register(array($this, 'load'), true, $this->prependAutoloader);
        $this->isRegistered = true;

        return $this;
    }

    /**
     * Unregister the autoloader.
     * @throws \Brickoo\Autoloader\Exception\AutoloaderNotRegisteredException
     * @return \Brickoo\Autoloader\Autoloader
     */
    public function unregister() {
        if (! $this->isRegistered) {
            require_once "Exception".DIRECTORY_SEPARATOR."AutoloaderNotRegisteredException.php";
            throw new AutoloaderNotRegisteredException();
        }

        spl_autoload_unregister(array($this, "load"));
        $this->isRegistered = false;

        return $this;
    }

    /**
     * Loads the requested class.
     * Commomly this is the autoload callback function registered.
     * @param string $className the class to load
     * @throws \InvalidArgumentException if an argument is not valid
     * @throws \Brickoo\Autoloader\Exception\FileDoesNotExist throws an exception if the file does not exists
     * @return boolean true on success false on failure
     */
    abstract public function load($className);

}