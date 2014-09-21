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

namespace Brickoo\Component\Http;

use Brickoo\Component\Validation\Argument;
use Brickoo\Component\Http\Exception\HttpFormFileNotFoundException;
use Brickoo\Component\Http\Exception\UnableToMoveFormFileException;

/**
 * HttpFormFile
 *
 * Implements a http form file.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpFormFile {

    /** @var string */
    private $name;

    /** @var string */
    private $filePath;

    /** @var integer */
    private $fileSize;

    /** @var integer */
    private $errorCode;

    /**
     * Class constructor.
     * @param string $name
     * @param string $filePath
     * @param integer $fileSize
     * @param integer $errorCode
     * @throws \InvalidArgumentException
     */
    public function __construct($name, $filePath, $fileSize, $errorCode) {
        Argument::isString($name);
        Argument::isString($filePath);
        Argument::isInteger($fileSize);
        Argument::isInteger($errorCode);
        $this->name = $name;
        $this->filePath = $filePath;
        $this->fileSize = $fileSize;
        $this->errorCode = $errorCode;
    }

    /**
     * Check if the file did produce an error.
     * @return boolean check result
     */
    public function hasError() {
        return $this->errorCode !== UPLOAD_ERR_OK;
    }

    /**
     * Return the error code.
     * @return integer
     */
    public function getErrorCode() {
        return $this->errorCode;
    }

    /**
     * Return the original file name.
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Return the file sie in bytes.
     * @return integer
     */
    public function getSize() {
        return $this->fileSize;
    }

    /**
     * Move the form file to a target location.
     * @param string $targetPath
     * @param string $targetFileName
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Http\Exception\HttpFormFileNotFoundException
     * @throws \Brickoo\Component\Http\Exception\UnableToMoveFormFileException
     * @return string target file path
     */
    public function moveTo($targetPath, $targetFileName) {
        Argument::isString($targetPath);
        Argument::isString($targetFileName);

        if (! file_exists($this->filePath)) {
            throw new HttpFormFileNotFoundException($this->filePath);
        }

        $targetFilePath = $this->generateTargetFilePath($targetPath, $targetFileName);

        if (! move_uploaded_file($this->filePath, $targetFilePath)) {
            throw new UnableToMoveFormFileException($this->filePath, $targetFilePath);
        }

        return $targetFilePath;
    }

    /**
     * Generate an upload target file path.
     * @param string $targetPath
     * @param string $targetFileName
     * @return string the generated target file path
     */
    private function generateTargetFilePath($targetPath, $targetFileName) {
        return rtrim($targetPath, "\\/").DIRECTORY_SEPARATOR.substr(utf8_encode($targetFileName), 0, 255);
    }

}
