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

use Brickoo\Component\Autoloader\Exception\DirectoryDoesNotExistException;

/**
 * IncludePathAutoloader
 *
 * Implements an autoloader which uses an include path as vendor location.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class IncludePathAutoloader extends Autoloader {

    /** @var string */
    private $includePath;

    /**
     * Class constructor.
     * @param string $includePath
     * @param boolean $prepend
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Autoloader\Exception\DirectoryDoesNotExistException
     */
    public function __construct($includePath, $prepend = true) {
        parent::__construct($prepend);
        $this->setIncludePath($includePath);
        include_once "Exception.php";
    }

    /** {@inheritDoc} */
    public function load($className) {
        if ((! is_string($className)) || (! $className = trim($className, "\\"))) {
            throw new \InvalidArgumentException("Invalid class argument used.");
        }

        $absolutePath = $this->getAbsolutePath($className);

        if ((! file_exists($absolutePath))) {
            return false;
        }

        require ($absolutePath);
        return true;
    }

    /**
     * Sets the include path.
     * @param string $includePath
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Autoloader\Exception\DirectoryDoesNotExistException
     * @return \Brickoo\Component\Autoloader\IncludePathAutoloader
     */
    private function setIncludePath($includePath) {
        if (! is_string($includePath)) {
            throw new \InvalidArgumentException("Invalid include path argument.");
        }

        if (! is_dir($includePath)) {
            include_once "Exception".DIRECTORY_SEPARATOR."DirectoryDoesNotExistException.php";
            throw new DirectoryDoesNotExistException($includePath);
        }

        $this->includePath = rtrim($includePath, "/\\");
        return $this;
    }

    /**
     * Returns the absolute path for the requested class.
     * @param string $className the class to retrieve the path from
     * @return string the absolute file path
     */
    private function getAbsolutePath($className) {
        return $this->includePath . $this->getTranslatedClassPath($className);
    }

    /**
     * Returns a translated namespace class to filesystem path.
     * @param string $className class including namespace
     * @return string the translated class path
     */
    private function getTranslatedClassPath($className) {
        return DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, $className) .".php";
    }

}
