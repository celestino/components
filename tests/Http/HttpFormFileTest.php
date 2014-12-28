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
