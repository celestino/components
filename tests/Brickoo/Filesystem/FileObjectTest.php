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

    namespace Tests\Brickoo\Filesystem;

    use Brickoo\Filesystem\File;

    /**
     * FileObjectTest
     *
     * Test suite for the FileObject class.
     * @see Brickoo\Filesystem\FileObject
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */
    class FileObjectTest extends \PHPUnit_Framework_TestCase {

        public function testImplementingInterface() {
            $FileObject = new File();
            $this->assertInstanceOf('Brickoo\Filesystem\Interfaces\FileObject', $FileObject);
        }

        /**
         * @covers Brickoo\Filesystem\FileObject::open
         * @covers Brickoo\Filesystem\FileObject::__destruct
         */
        public function testOpenFile() {
            $FileObject = new File();
            $FileObject->open("php://memory", "r");
            $this->assertAttributeEquals("r", "mode", $FileObject);
            $this->assertAttributeInternalType("resource", "handle", $FileObject);
        }

        /**
         * @covers Brickoo\Filesystem\FileObject::open
         * @covers Brickoo\Filesystem\Exceptions\HandleAlreadyExists
         * @expectedException Brickoo\Filesystem\Exceptions\HandleAlreadyExists
         */
        public function testOpenTwiceThrowsHandleAlreadyExistsException() {
            $FileObject = new File();
            $FileObject->open("php://memory", "r");
            $FileObject->open("php://memory", "r");
        }

        /**
         * @covers Brickoo\Filesystem\FileObject::open
         * @covers Brickoo\Filesystem\Exceptions\UnableToCreateHandle
         * @expectedException Brickoo\Filesystem\Exceptions\UnableToCreateHandle
         */
        public function testOpenFailureThrowsUnableToCreateHandleException() {
            $FileObject = new File();
            $FileObject->open("php://path/does/not/exist", "r");
        }

        /**
         * @covers Brickoo\Filesystem\FileObject::open
         * @covers Brickoo\Filesystem\Exceptions\UnableToCreateHandle
         * @expectedException Brickoo\Filesystem\Exceptions\UnableToCreateHandle
         */
        public function testOpenFileWithContextThrowsUnableToCreateHandleException() {
            $context = stream_context_create(array(
              'http'=>array(
                  'method'=>"GET",
                  'header'=>"Accept-language: en\r\n"
              )
            ));

            $FileObject = new File();
            $FileObject->open("http://localhost:12345", "w", false, $context);
        }

        /**
         * @covers Brickoo\Filesystem\FileObject::write
         * @covers Brickoo\Filesystem\FileObject::read
         * @covers Brickoo\Filesystem\FileObject::hasHandle
         * @covers Brickoo\Filesystem\FileObject::getHandle
         */
        public function testWriteAndReadOperations() {
            $expectedData = "The written data.";
            $FileObject = new File();
            $FileObject->open("php://memory", "r+");
            $this->assertEquals(strlen($expectedData), $FileObject->write($expectedData));
            $FileObject->fseek(0);
            $this->assertEquals($expectedData, $FileObject->read(strlen($expectedData)));
        }

        /**
         * @covers Brickoo\Filesystem\FileObject::read
         * @covers Brickoo\Filesystem\FileObject::getHandle
         * @covers Brickoo\Filesystem\Exceptions\HandleNotAvailable
         * @expectedException Brickoo\Filesystem\Exceptions\HandleNotAvailable
         */
        public function testReadThrowsHandleNotAvailableException() {
            $FileObject = new File();
            $FileObject->read(1);
        }

        /**
         * @covers Brickoo\Filesystem\FileObject::read
         * @covers Brickoo\Filesystem\Exceptions\InvalidModeOperation
         * @expectedException Brickoo\Filesystem\Exceptions\InvalidModeOperation
         */
        public function testReadThrowsInvalidModeOperationException() {
            $FileObject = new File();
            $FileObject->open("php://memory", "w")
                       ->read(1);
        }

        /**
         * @covers Brickoo\Filesystem\FileObject::read
         * @expectedException InvalidArgumentException
         */
        public function testReadThrowsArgumentException() {
            $FileObject = new File();
            $FileObject->open("php://memory", "r")
                       ->read('wrongType');
        }

        /**
         * @covers Brickoo\Filesystem\FileObject::write
         * @covers Brickoo\Filesystem\FileObject::getHandle
         * @covers Brickoo\Filesystem\Exceptions\HandleNotAvailable
         * @expectedException Brickoo\Filesystem\Exceptions\HandleNotAvailable
         */
        public function testWriteThrowsHandleNotAvailableException() {
            $FileObject = new File();
            $FileObject->write("throws exception");
        }

        /**
         * @covers Brickoo\Filesystem\FileObject::write
         * @covers Brickoo\Filesystem\Exceptions\InvalidModeOperation
         * @expectedException Brickoo\Filesystem\Exceptions\InvalidModeOperation
         */
        public function testWriteThrowsInvalidModeOperationException() {
            $FileObject = new File();
            $FileObject->open("php://memory", "r")
                       ->write("throws exception");
        }

        /**
         * @covers Brickoo\Filesystem\FileObject::close
         */
        public function testClose() {
            $FileObject = new File();
            $FileObject->open("php://memory", "r");
            $this->assertAttributeInternalType("resource","handle", $FileObject);
            $FileObject->close();
            $this->assertAttributeEquals(null,"handle", $FileObject);
        }

        /**
         * Test if the trying to close the handle without being opened throws an exception.
         * @covers Brickoo\Filesystem\FileObject::close
         * @covers Brickoo\Filesystem\Exceptions\HandleNotAvailable
         * @expectedException Brickoo\Filesystem\Exceptions\HandleNotAvailable
         */
        public function testCloseHandleException() {
            $FileObject = new File();
            $FileObject->close();
        }

        /**
         * Test if magic functions can be called an returns the function return value.
         * @covers Brickoo\Filesystem\FileObject::__call
         */
        public function test__call() {
            $expectedData = 'Some data to test with magic functions.';

            $FileObject = new File();
            $FileObject->open("php://memory", "w+");

            $this->assertEquals(strlen($expectedData), $FileObject->fwrite($expectedData)); // magic
            $this->assertEquals(0, $FileObject->fseek(0)); // magic

            $loadedData = '';
            while(! $FileObject->feof()) {
                $loadedData .= $FileObject->fread(5); // magic
            }
            $this->assertEquals($expectedData, $loadedData);
        }

        /**
         * @covers Brickoo\Filesystem\FileObject::__call
         * @expectedException BadMethodCallException
         */
        public function testFOPENThrowsBadMethodCallException() {
            $FileObject = new File();
            $FileObject->fopen();
        }

        /**
         * @covers Brickoo\Filesystem\FileObject::__call
         * @expectedException BadMethodCallException
         */
        public function testFCLOSEThrowsBadMethodCallException() {
            $FileObject = new File();
            $FileObject->fclose();
        }

    }
