<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    use Brickoo\Filesystem\Client;

    /**
     * ClientTest
     *
     * Test suite for the Client class.
     * @see Brickoo\Filesystem\Client
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */
    class ClientTest extends \PHPUnit_Framework_TestCase {

        public function testImplementingInterface() {
            $Client = new Client();
            $this->assertInstanceOf('Brickoo\Filesystem\Interfaces\Client', $Client);
        }

        /**
         * @covers Brickoo\Filesystem\Client::open
         * @covers Brickoo\Filesystem\Client::__destruct
         */
        public function testOpenFile() {
            $Client = new Client();
            $Client->open("php://memory", "r");
            $this->assertAttributeEquals("r", "mode", $Client);
            $this->assertAttributeInternalType("resource", "handle", $Client);
        }

        /**
         * @covers Brickoo\Filesystem\Client::open
         * @covers Brickoo\Filesystem\Exceptions\HandleAlreadyExists
         * @expectedException Brickoo\Filesystem\Exceptions\HandleAlreadyExists
         */
        public function testOpenTwiceThrowsHandleAlreadyExistsException() {
            $Client = new Client();
            $Client->open("php://memory", "r");
            $Client->open("php://memory", "r");
        }

        /**
         * @covers Brickoo\Filesystem\Client::open
         * @covers Brickoo\Filesystem\Exceptions\UnableToCreateHandle
         * @expectedException Brickoo\Filesystem\Exceptions\UnableToCreateHandle
         */
        public function testOpenFailureThrowsUnableToCreateHandleException() {
            $Client = new Client();
            $Client->open("php://path/does/not/exist", "r");
        }

        /**
         * @covers Brickoo\Filesystem\Client::open
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

            $Client = new Client();
            $Client->open("http://localhost:12345", "w", false, $context);
        }

        /**
         * @covers Brickoo\Filesystem\Client::write
         * @covers Brickoo\Filesystem\Client::read
         * @covers Brickoo\Filesystem\Client::hasHandle
         * @covers Brickoo\Filesystem\Client::getHandle
         */
        public function testWriteAndReadOperations() {
            $expectedData = "The written data.";
            $Client = new Client();
            $Client->open("php://memory", "r+");
            $this->assertEquals(strlen($expectedData), $Client->write($expectedData));
            $Client->fseek(0);
            $this->assertEquals($expectedData, $Client->read(strlen($expectedData)));
        }

        /**
         * @covers Brickoo\Filesystem\Client::read
         * @covers Brickoo\Filesystem\Client::getHandle
         * @covers Brickoo\Filesystem\Exceptions\HandleNotAvailable
         * @expectedException Brickoo\Filesystem\Exceptions\HandleNotAvailable
         */
        public function testReadThrowsHandleNotAvailableException() {
            $Client = new Client();
            $Client->read(1);
        }

        /**
         * @covers Brickoo\Filesystem\Client::read
         * @covers Brickoo\Filesystem\Exceptions\InvalidModeOperation
         * @expectedException Brickoo\Filesystem\Exceptions\InvalidModeOperation
         */
        public function testReadThrowsInvalidModeOperationException() {
            $Client = new Client();
            $Client->open("php://memory", "w")
                       ->read(1);
        }

        /**
         * @covers Brickoo\Filesystem\Client::read
         * @expectedException InvalidArgumentException
         */
        public function testReadThrowsArgumentException() {
            $Client = new Client();
            $Client->open("php://memory", "r")
                       ->read('wrongType');
        }

        /**
         * @covers Brickoo\Filesystem\Client::write
         * @covers Brickoo\Filesystem\Client::getHandle
         * @covers Brickoo\Filesystem\Exceptions\HandleNotAvailable
         * @expectedException Brickoo\Filesystem\Exceptions\HandleNotAvailable
         */
        public function testWriteThrowsHandleNotAvailableException() {
            $Client = new Client();
            $Client->write("throws exception");
        }

        /**
         * @covers Brickoo\Filesystem\Client::write
         * @covers Brickoo\Filesystem\Exceptions\InvalidModeOperation
         * @expectedException Brickoo\Filesystem\Exceptions\InvalidModeOperation
         */
        public function testWriteThrowsInvalidModeOperationException() {
            $Client = new Client();
            $Client->open("php://memory", "r")
                       ->write("throws exception");
        }

        /**
         * @covers Brickoo\Filesystem\Client::close
         */
        public function testClose() {
            $Client = new Client();
            $Client->open("php://memory", "r");
            $this->assertAttributeInternalType("resource","handle", $Client);
            $Client->close();
            $this->assertAttributeEquals(null,"handle", $Client);
        }

        /**
         * Test if the trying to close the handle without being opened throws an exception.
         * @covers Brickoo\Filesystem\Client::close
         * @covers Brickoo\Filesystem\Exceptions\HandleNotAvailable
         * @expectedException Brickoo\Filesystem\Exceptions\HandleNotAvailable
         */
        public function testCloseHandleException() {
            $Client = new Client();
            $Client->close();
        }

        /**
         * Test if magic functions can be called an returns the function return value.
         * @covers Brickoo\Filesystem\Client::__call
         */
        public function test__call() {
            $expectedData = 'Some data to test with magic functions.';

            $Client = new Client();
            $Client->open("php://memory", "w+");

            $this->assertEquals(strlen($expectedData), $Client->fwrite($expectedData)); // magic
            $this->assertEquals(0, $Client->fseek(0)); // magic

            $loadedData = '';
            while(! $Client->feof()) {
                $loadedData .= $Client->fread(5); // magic
            }
            $this->assertEquals($expectedData, $loadedData);
        }

        /**
         * @covers Brickoo\Filesystem\Client::__call
         * @expectedException BadMethodCallException
         */
        public function testFOPENThrowsBadMethodCallException() {
            $Client = new Client();
            $Client->fopen();
        }

        /**
         * @covers Brickoo\Filesystem\Client::__call
         * @expectedException BadMethodCallException
         */
        public function testFCLOSEThrowsBadMethodCallException() {
            $Client = new Client();
            $Client->fclose();
        }

    }
