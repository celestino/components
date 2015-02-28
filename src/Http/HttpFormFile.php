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

        if ((! is_writable(dirname($targetFilePath)))
            || (! move_uploaded_file($this->filePath, $targetFilePath))) {
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
