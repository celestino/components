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

    namespace Tests\Brickoo\Network;

    use Brickoo\Network\Client;

    /**
     * ClientTest
     *
     * Test suite for the Client class.
     * @see Brickoo\Network\Client
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ClientTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Network\Client::open
         * @covers Brickoo\Network\Client::__destruct
         */
        public function testOpenNetworkConnection() {
            $Client = new Client();

            try {
                $Client->open("google.com", 80, 10);
            } catch(\Brickoo\Network\Exceptions\UnableToCreateHandle $Exception) {
                $this->markTestSkipped($Exception->getMessage());
            }

            $this->assertAttributeInternalType("resource", "handle", $Client);
        }

        /**
         * @covers Brickoo\Network\Client::open
         * @covers Brickoo\Network\Exceptions\HandleAlreadyExists
         * @expectedException Brickoo\Network\Exceptions\HandleAlreadyExists
         */
        public function testOpenSocketTwiceThrowsHandleExistsException() {
            $Client = new Client();

            try {
                $Client->open("google.com", 80, 10);
            } catch(Brickoo\Network\Exceptions\UnableToCreateHandleException $Exception) {
                return $this->markTestSkipped($Exception->getMessage());
            }

            $Client->open("google.com", 80, 10);
        }

        /**
         * @covers Brickoo\Network\Client::open
         * @covers Brickoo\Network\Exceptions\UnableToCreateHandle
         * @expectedException Brickoo\Network\Exceptions\UnableToCreateHandle
         */
        public function testOpenHandleException() {
            $Client = new Client();
            $Client->open("brickoo://failure", 80, 1);
        }

        /**
         * @covers Brickoo\Network\Client::open
         * @covers Brickoo\Network\Exceptions\UnableToCreateHandle
         * @expectedException Brickoo\Network\Exceptions\UnableToCreateHandle
         */
        public function testOpenFileWithContextThrowsUnableToCreateHandleException() {
            $context = stream_context_create(array(
              'http'=>array(
                  'method'=>"GET",
                  'header'=>"Accept-language: en\r\n"
              )
            ));

            $Client = new Client();
            $Client->open("brickoo://failure", 80, 1, STREAM_CLIENT_CONNECT, $context);
        }

        /**
         * @covers Brickoo\Network\Client::write
         * @covers Brickoo\Network\Client::getHandle
         * @covers Brickoo\Network\Client::hasHandle
         */
        public function testWriteToStream() {
            $data = "GET / HTTP/1.0\r\n".
                    "Host: google.com\r\n\r\n\r\n";

            $Client = new Client();
            $Client->open("google.com", 80, 30);
            $this->assertEquals(strlen($data), $Client->write($data));
        }

        /**
         * @covers Brickoo\Network\Client::write
         * @covers Brickoo\Network\Client::getHandle
         * @covers Brickoo\Network\Exceptions\HandleNotAvailable
         * @expectedException Brickoo\Network\Exceptions\HandleNotAvailable
         */
        public function testWriteThrowsHandleNotAvailableException() {
            $Client = new Client();
            $Client->write("failure");
        }

        /**
         * @covers Brickoo\Network\Client::read
         * @covers Brickoo\Network\Client::getHandle
         * @covers Brickoo\Network\Client::hasHandle
         */
        public function testREadFromStream() {
            $expectedData = "HTTP/1.0 302 Found";
            $data = "GET / HTTP/1.0\r\n".
                    "Host: www.google.com\r\n\r\n\r\n";

            $Client = new Client();
            $Client->open("www.google.com", 80, 30);
            $Client->write($data);
            $this->assertEquals($expectedData, $Client->read(strlen($expectedData)));
        }

        /**
         * @covers Brickoo\Network\Client::read
         * @covers Brickoo\Network\Client::getHandle
         * @covers Brickoo\Network\Exceptions\HandleNotAvailable
         * @expectedException Brickoo\Network\Exceptions\HandleNotAvailable
         */
        public function testReradThrowsHandleNotAvailableException() {
            $Client = new Client();
            $Client->read("failure");
        }

        /**
         * @covers Brickoo\Network\Client::close
         */
        public function testCloseConnection() {
            $Client = new Client();
            $Client->open("google.com", 80, 30);
            $this->assertAttributeInternalType("resource", "handle", $Client);
            $Client->close();
            $this->assertAttributeEquals(null, "handle", $Client);
        }

        /**
         * @covers Brickoo\Network\Client::close
         * @covers Brickoo\Network\Exceptions\HandleNotAvailable
         * @expectedException Brickoo\Network\Exceptions\HandleNotAvailable
         */
        public function testCloseThrowsHandleNotAvailableException() {
            $Client = new Client();
            $Client->close();
        }

        /**
         * @covers Brickoo\Network\Client::__call
         */
        public function testMagicMethodCall() {
            $data = "GET / HTTP/1.0\r\n".
                    "Host: google.com\r\n\r\n\r\n";

            $Client = new Client();
            $Client->open("google.com", 80, 30);
            $this->assertEquals(strlen($data), $Client->fwrite($data));
        }

        /**
         * @covers Brickoo\Network\Client::__call
         * @expectedException BadMethodCallException
         */
        public function testCallCloseThrowsException() {
            $Client = new Client();
            $Client->fclose();
        }

    }