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

namespace Brickoo\Tests\Component\Http;

use Brickoo\Component\Http\HttpFormFile;
use PHPUnit_Framework_TestCase;

/**
 * HttpFormFileTest
 *
 * Test suite for the HttpFormFile class.
 * @see Brickoo\Component\Http\HttpFormFile
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpFormFileTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\HttpFormFile::__construct
     * @covers Brickoo\Component\Http\HttpFormFile::getName
     * @covers Brickoo\Component\Http\HttpFormFile::getSize
     * @covers Brickoo\Component\Http\HttpFormFile::getErrorCode
     * @covers Brickoo\Component\Http\HttpFormFile::hasError
     */
    public function testConstructor() {
        $fileName = "document.pdf";
        $filePath = "/tmp/acb123";
        $fileSize = 123;
        $uploadError = UPLOAD_ERR_OK;

        $httpFormFile = new HttpFormFile($fileName, $filePath, $fileSize, $uploadError);
        $this->assertEquals($fileName, $httpFormFile->getName());
        $this->assertEquals($fileSize, $httpFormFile->getSize());
        $this->assertFalse($httpFormFile->hasError());
        $this->assertEquals(UPLOAD_ERR_OK, $httpFormFile->getErrorCode());
    }

    /**
     * @runInSeparateProcess
     * @covers Brickoo\Component\Http\HttpFormFile::moveTo
     * @covers Brickoo\Component\Http\HttpFormFile::generateTargetFilePath
     */
    public function testMoveUploadedFile() {
        include_once __DIR__.DIRECTORY_SEPARATOR."Asset".DIRECTORY_SEPARATOR."moveUploadedFileFunction.php";

        $fileName = "test_case_".time();
        $tempSourceFilePath = sys_get_temp_dir().DIRECTORY_SEPARATOR.$fileName;
        $tempTargetPath = sys_get_temp_dir().DIRECTORY_SEPARATOR;
        file_put_contents($tempSourceFilePath, "");

        $httpFormFile = new HttpFormFile($fileName, $tempSourceFilePath, 0, UPLOAD_ERR_OK);
        $this->assertEquals(
            $tempTargetPath.$fileName.".copy",
            $httpFormFile->moveTo($tempTargetPath, $fileName.".copy")
        );
        unlink($tempTargetPath.$fileName);
        unlink($tempTargetPath.$fileName.".copy");
    }

    /**
     * @covers Brickoo\Component\Http\HttpFormFile::moveTo
     * @covers Brickoo\Component\Http\Exception\HttpFormFileNotFoundException
     * @expectedException \Brickoo\Component\Http\Exception\HttpFormFileNotFoundException
     */
    public function testMovingNotExistingFileThrowsException() {
        $tempTargetPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.time();
        $httpFormFile = new HttpFormFile("", $tempTargetPath, 0, UPLOAD_ERR_OK);
        $httpFormFile->moveTo("somewhere", "someFile");
    }

    /**
     * @covers Brickoo\Component\Http\HttpFormFile::moveTo
     * @covers Brickoo\Component\Http\Exception\UnableToMoveFormFileException
     * @expectedException \Brickoo\Component\Http\Exception\UnableToMoveFormFileException
     */
    public function testMovingNotUploadedFileThrowsException() {
        $tempSourceFilePath = sys_get_temp_dir().DIRECTORY_SEPARATOR.time();
        file_put_contents($tempSourceFilePath, "");
        $httpFormFile = new HttpFormFile("", $tempSourceFilePath, 0, UPLOAD_ERR_OK);
        $httpFormFile->moveTo("somewhere", "someFile");
    }

}
