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

namespace Brickoo\Tests\Component\Filesystem;

use Brickoo\Component\Filesystem\File,
    PHPUnit_Framework_TestCase;

/**
 * FileTest
 *
 * Test suite for the File class.
 * @see Brickoo\Component\Filesystem\File
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class FileTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Filesystem\File::open
     * @covers Brickoo\Component\Filesystem\File::__destruct
     */
    public function testOpenFile() {
        $File = new File();
        $File->open("php://memory", "r");
        $this->assertAttributeEquals("r", "mode", $File);
        $this->assertAttributeInternalType("resource", "handle", $File);
    }

    /**
     * @covers Brickoo\Component\Filesystem\File::open
     * @covers Brickoo\Component\Filesystem\Exception\HandleAlreadyExistsException
     * @expectedException Brickoo\Component\Filesystem\Exception\HandleAlreadyExistsException
     */
    public function testOpenTwiceThrowsHandleAlreadyExistsException() {
        $File = new File();
        $File->open("php://memory", "r");
        $File->open("php://memory", "r");
    }

    /**
     * @covers Brickoo\Component\Filesystem\File::open
     * @covers Brickoo\Component\Filesystem\Exception\UnableToCreateHandleException
     * @expectedException Brickoo\Component\Filesystem\Exception\UnableToCreateHandleException
     */
    public function testOpenFailureThrowsUnableToCreateHandleException() {
        $File = new File();
        $File->open("php://path/does/not/exist", "r");
    }

    /**
     * @covers Brickoo\Component\Filesystem\File::open
     * @covers Brickoo\Component\Filesystem\Exception\UnableToCreateHandleException
     * @expectedException Brickoo\Component\Filesystem\Exception\UnableToCreateHandleException
     */
    public function testOpenFileWithContextThrowsUnableToCreateHandleException() {
        $context = stream_context_create(["http" => [
              "method"=>"GET",
              "header"=>"Accept-language: en\r\n"
        ]]);
        $File = new File();
        $File->open("http://localhost:12345", "w", false, $context);
    }

    /**
     * @covers Brickoo\Component\Filesystem\File::write
     * @covers Brickoo\Component\Filesystem\File::read
     * @covers Brickoo\Component\Filesystem\File::hasHandle
     * @covers Brickoo\Component\Filesystem\File::getHandle
     * @covers Brickoo\Component\Filesystem\File::isReadMode
     */
    public function testReadAndWriteOperations() {
        $expectedData = "The written data.";
        $File = new File();
        $File->open("php://memory", "r+");
        $this->assertEquals(strlen($expectedData), $File->write($expectedData));
        $File->fseek(0);
        $this->assertEquals($expectedData, $File->read(strlen($expectedData)));
    }

    /**
     * @covers Brickoo\Component\Filesystem\File::read
     * @covers Brickoo\Component\Filesystem\File::getHandle
     * @covers Brickoo\Component\Filesystem\Exception\HandleNotAvailableException
     * @expectedException Brickoo\Component\Filesystem\Exception\HandleNotAvailableException
     */
    public function testReadThrowsHandleNotAvailableException() {
        $File = new File();
        $File->read(1);
    }

    /**
     * @covers Brickoo\Component\Filesystem\File::read
     * @covers Brickoo\Component\Filesystem\Exception\InvalidModeOperationException
     * @expectedException Brickoo\Component\Filesystem\Exception\InvalidModeOperationException
     */
    public function testReadThrowsInvalidModeOperationException() {
        $File = new File();
        $File->open("php://memory", "w")->read(1);
    }

    /**
     * @covers Brickoo\Component\Filesystem\File::read
     * @expectedException InvalidArgumentException
     */
    public function testReadThrowsArgumentException() {
        $File = new File();
        $File->open("php://memory", "r")->read("wrongType");
    }

    /**
     * @covers Brickoo\Component\Filesystem\File::write
     * @covers Brickoo\Component\Filesystem\File::getHandle
     * @covers Brickoo\Component\Filesystem\Exception\HandleNotAvailableException
     * @expectedException Brickoo\Component\Filesystem\Exception\HandleNotAvailableException
     */
    public function testWriteThrowsHandleNotAvailableException() {
        $File = new File();
        $File->write("throws exception");
    }

    /**
     * @covers Brickoo\Component\Filesystem\File::write
     * @covers Brickoo\Component\Filesystem\Exception\InvalidModeOperationException
     * @expectedException Brickoo\Component\Filesystem\Exception\InvalidModeOperationException
     */
    public function testWriteThrowsInvalidModeOperationException() {
        $File = new File();
        $File->open("php://memory", "r")->write("throws exception");
    }

    /** @covers Brickoo\Component\Filesystem\File::readLine s*/
    public function testReadLine() {
        $fileData = "First line.\r\nSecond line.\nThird line.";
        $File = new File();
        $File->open("php://memory", "r+");
        $this->assertEquals(strlen($fileData), $File->write($fileData));
        $File->fseek(0);
        $this->assertEquals("First line.\r\n", $File->readLine());
        $this->assertEquals("Second line.\n", $File->readLine());
        $this->assertEquals("Third line.", $File->readLine());
    }

    /**
     * @covers Brickoo\Component\Filesystem\File::readLine
     * @covers Brickoo\Component\Filesystem\Exception\InvalidModeOperationException
     * @expectedException Brickoo\Component\Filesystem\Exception\InvalidModeOperationException
     */
    public function testReadLineThrowsInvalidModeOperationException() {
        $File = new File();
        $File->open("php://memory", "w")->readLine();
    }

    /** @covers Brickoo\Component\Filesystem\File::close */
    public function testClose() {
        $File = new File();
        $File->open("php://memory", "r");
        $this->assertAttributeInternalType("resource","handle", $File);
        $File->close();
        $this->assertAttributeEquals(null,"handle", $File);
    }

    /**
     * @covers Brickoo\Component\Filesystem\File::close
     * @covers Brickoo\Component\Filesystem\Exception\HandleNotAvailableException
     * @expectedException Brickoo\Component\Filesystem\Exception\HandleNotAvailableException
     */
    public function testCloseHandleException() {
        $File = new File();
        $File->close();
    }

    /**
     * @covers Brickoo\Component\Filesystem\File::__call
     * @covers Brickoo\Component\Filesystem\File::isEndOfFile
     */
    public function test__call() {
        $expectedData = "Some data to test with magic functions.";

        $File = new File();
        $File->open("php://memory", "w+");

        $this->assertEquals(strlen($expectedData), $File->fwrite($expectedData)); // magic
        $this->assertEquals(0, $File->fseek(0)); // magic

        $loadedData = "";
        while(! $File->isEndOfFile()) {
            $loadedData .= $File->fread(5); // magic
        }
        $this->assertEquals($expectedData, $loadedData);
    }

    /**
     * @covers Brickoo\Component\Filesystem\File::__call
     * @expectedException BadMethodCallException
     */
    public function testFOPENThrowsBadMethodCallException() {
        $File = new File();
        $File->fopen();
    }

    /**
     * @covers Brickoo\Component\Filesystem\File::__call
     * @expectedException BadMethodCallException
     */
    public function testFCLOSEThrowsBadMethodCallException() {
        $File = new File();
        $File->fclose();
    }

}