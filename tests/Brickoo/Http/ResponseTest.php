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

    namespace Tests\Brickoo\Http;

    use Brickoo\Http\Response;

    /**
     * ResponseTest
     *
     * Test suite for the Response class.
     * @see Brickoo\Http\Response
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ResponseTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Http\Response::__construct
         */
        public function testConstructor() {
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');
            $status = 201;
            $version = "HTTP/1.1";

            $Response = new Response($Header, $Body, $status, $version);
            $this->assertInstanceOf('Brickoo\Http\Interfaces\Response', $Response);
            $this->assertAttributeSame($Header, "Header", $Response);
            $this->assertAttributeSame($Body, "Body", $Response);
            $this->assertAttributeEquals($status, "status", $Response);
            $this->assertAttributeEquals($version, "version", $Response);
        }

        /**
         * @covers Brickoo\Http\Response::__construct
         * @covers Brickoo\Http\Exceptions\StatusCodeUnknown
         * @expectedException Brickoo\Http\Exceptions\StatusCodeUnknown
         */
        public function testConstructorThrowsUnknownStatusException() {
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');
            $status = 999;
            $Response = new Response($Header, $Body, $status);
        }

        /**
         * @covers Brickoo\Http\Response::getHeader
         */
        public function testHeaderAreReturned() {
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');

            $Response = new Response($Header, $Body);
            $this->assertSame($Header, $Response->getHeader());
        }

        /**
         * @covers Brickoo\Http\Response::getBody
         */
        public function testBodyIsReturned() {
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');

            $Response = new Response($Header, $Body);
            $this->assertSame($Body, $Response->getBody());
        }

        /**
         * @covers Brickoo\Http\Response::getStatus
         */
        public function testGetStatus() {
            $expectedStatus = 404;
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');

            $Response = new Response($Header, $Body, $expectedStatus);
            $this->assertEquals($expectedStatus, $Response->getStatus());
        }

        /**
         * @covers Brickoo\Http\Response::getVersion
         */
        public function testGetVersion() {
            $expectedVersion = "HTTP/1.1";
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');

            $Response = new Response($Header, $Body, 200, $expectedVersion);
            $this->assertEquals($expectedVersion, $Response->getVersion());
        }

        /**
         * @covers Brickoo\Http\Response::send
         * @covers Brickoo\Http\Response::sendStatus
         * @covers Brickoo\Http\Response::getStatusPhrase
         */
        public function testSend() {
            $expectedOutput = "HTTP/1.1 201 Created\r\n";
            $this->expectOutputString($expectedOutput);

            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Header->expects($this->once())
                   ->method("send");

            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');
            $Body->expects($this->once())
                 ->method("send");

            $Response = new Response($Header, $Body, 201, "HTTP/1.1");
            $Response->send(function ($statusLine) {echo $statusLine. "\r\n";});
        }

        /**
         * @covers Brickoo\Http\Response::toString
         * @covers Brickoo\Http\Response::getStatusPhrase
         */
        public function testToString() {
            $expectedOutput  = "HTTP/1.1 200 OK\r\n";
            $expectedOutput .= "Unit: TEST\r\n";
            $expectedOutput .= "\r\ntest case content";

            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Header->expects($this->once())
                   ->method("toString")
                   ->will($this->returnValue("Unit: TEST\r\n"));

            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');
            $Body->expects($this->once())
                 ->method("getContent")
                 ->will($this->returnValue("test case content"));

            $Response = new Response($Header, $Body, 200, "HTTP/1.1");
            $this->assertEquals($expectedOutput, $Response->toString());
        }

    }