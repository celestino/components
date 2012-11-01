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

    namespace Tests\Brickoo\Http;

    use Brickoo\Http\Request;

    /**
     * RequestTest
     *
     * Test suite for the Request class.
     * @see Brickoo\Http\Request
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RequestTest extends \PHPUnit_Framework_TestCase {

        /** @var array */
        private $backupServerValues;

        /**
         * {@inheritDoc}
         * Backups the global server values.
         * PHPUnit provides this by annotation (@backupGlobals),
         * to do not depend on PHPUnit implementation this is done manually.
         * @see PHPUnit_Framework_TestCase::setUp()
         * @return void
         */
        public function setUp() {
            $this->backupServerValues = $_SERVER;
        }

        /**
         * {@inheritDoc}
         * Restores the global server values.
         * @see PHPUnit_Framework_TestCase::tearDown()
         * @return void
         */
        public function tearDown() {
            $_SERVER = $this->backupServerValues;
        }

        /**
         * @covers Brickoo\Http\Request::__construct
         */
        public function testConstructor() {
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');
            $Uri = $this->getMock('\Brickoo\Http\Request\Interfaces\Uri');
            $method = "GET";
            $version = Request::HTTP_VERSION_1_1;

            $Request = new Request($Header, $Body, $Uri, $method, $version);

            $this->assertInstanceOf('Brickoo\Http\Interfaces\Request', $Request);
            $this->assertAttributeSame($Header, "Header", $Request);
            $this->assertAttributeSame($Body, "Body", $Request);
            $this->assertAttributeSame($Uri, "Uri", $Request);
            $this->assertAttributeEquals($method, "method", $Request);
            $this->assertAttributeEquals($version, "version", $Request);
        }

        /**
         * @covers Brickoo\Http\Request::getHeader
         */
        public function testGetHeaderDependency() {
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');
            $Uri = $this->getMock('\Brickoo\Http\Request\Interfaces\Uri');

            $Request = new Request($Header, $Body, $Uri);
            $this->assertSame($Header, $Request->getHeader());
        }

        /**
         * @covers Brickoo\Http\Request::getBody
         */
        public function testGetBodyDependency() {
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');
            $Uri = $this->getMock('\Brickoo\Http\Request\Interfaces\Uri');

            $Request = new Request($Header, $Body, $Uri);
            $this->assertSame($Body, $Request->getBody());
        }

        /**
         * @covers Brickoo\Http\Request::getUri
         */
        public function testGetUriDependency() {
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');
            $Uri = $this->getMock('\Brickoo\Http\Request\Interfaces\Uri');

            $Request = new Request($Header, $Body, $Uri);
            $this->assertSame($Uri, $Request->getUri());
        }

        /**
         * @covers Brickoo\Http\Request::getQuery
         */
        public function testGetQuery() {
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');

            $Query = $this->getMock('Brickoo\Http\Request\Interfaces\Query');
            $Uri = $this->getMock('\Brickoo\Http\Request\Interfaces\Uri');
            $Uri->expects($this->once())
                ->method("getQuery")
                ->will($this->returnValue($Query));

            $Request = new Request($Header, $Body, $Uri);
            $this->assertSame($Query, $Request->getQuery());
        }

        /**
         * @covers Brickoo\Http\Request::getMethod
         */
        public function testGetMethod() {
            $Request = $this->getRequestFixture();
            $this->assertEquals("GET", $Request->getMethod());
        }

        /**
         * @covers Brickoo\Http\Request::getMethod
         */
        public function testGetMethodFromGlobalServerValue() {
            $_SERVER["REQUEST_METHOD"] = "POST";

            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');
            $Uri = $this->getMock('\Brickoo\Http\Request\Interfaces\Uri');

            $Request = new Request($Header, $Body, $Uri);
            $this->assertEquals("POST", $Request->getMethod());
        }

        /**
         * @covers Brickoo\Http\Request::getVersion
         */
        public function testGetVersion() {
            $Request = $this->getRequestFixture();
            $this->assertEquals(Request::HTTP_VERSION_1_1, $Request->getVersion());
        }

        /**
         * @covers Brickoo\Http\Request::getVersion
         */
        public function testGetVersionFromGlobalServerValue() {
            $_SERVER["SERVER_PROTOCOL"] = Request::HTTP_VERSION_1;

            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');
            $Uri = $this->getMock('\Brickoo\Http\Request\Interfaces\Uri');

            $Request = new Request($Header, $Body, $Uri);
            $this->assertEquals(Request::HTTP_VERSION_1, $Request->getVersion());
        }

        /**
         * @covers Brickoo\Http\Request::getServerVar
         */
        public function testGetServerVariable() {
            $_SERVER["UNIT"] = "TEST";
            $Request = $this->getRequestFixture();
            $this->assertEquals("TEST", $Request->getServerVar("UNIT"));
        }

        /**
         * @covers Brickoo\Http\Request::getServerVar
         */
        public function testGetServerVariableDefaultValue() {
            $Request = $this->getRequestFixture();
            $this->assertEquals("TEST", $Request->getServerVar("UNIT", "TEST"));
        }

        /**
         * @covers Brickoo\Http\Request::toString
         */
        public function testRequestToString() {
            $method = "GET";
            $version = Request::HTTP_VERSION_1_1;
            $bodyString = "test content";
            $headerString = "UNIT: TEST";
            $queryString = "key=value1";
            $urlPath = "/path/to/script";

            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Header->expects($this->any())
                   ->method("toString")
                   ->will($this->returnValue($headerString));

            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');
            $Body->expects($this->any())
                 ->method("getContent")
                 ->will($this->returnValue($bodyString));

            $Query = $this->getMock('Brickoo\Http\Request\Interfaces\Query');
            $Query->expects($this->any())
                  ->method("toString")
                  ->will($this->returnValue($queryString));

            $Uri = $this->getMock('\Brickoo\Http\Request\Interfaces\Uri');
            $Uri->expects($this->any())
                ->method("getQuery")
                ->will($this->returnValue($Query));
            $Uri->expects($this->any())
                ->method("getPath")
                ->will($this->returnValue($urlPath));

            $expectedValue = sprintf(
                "%s %s %s\r\n", $method, $urlPath ."?". $queryString, $version
            );
            $expectedValue .= $headerString ."\r\n\r\n". $bodyString;

            $Request = new Request($Header, $Body, $Uri, $method, $version);
            $this->assertEquals($expectedValue, $Request->toString());
        }

        /**
         * Returns a http request fixture.
         * @return \Brickoo\Http\Request
         */
        private function getRequestFixture() {
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Body = $this->getMock('Brickoo\Http\Message\Interfaces\Body');
            $Uri = $this->getMock('\Brickoo\Http\Request\Interfaces\Uri');
            $method = "GET";
            $version = Request::HTTP_VERSION_1_1;

            return new Request($Header, $Body, $Uri, $method, $version);

        }

    }