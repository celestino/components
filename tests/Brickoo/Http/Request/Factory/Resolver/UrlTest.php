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

    namespace Tests\Brickoo\Http\Request\Factory\Resolver;

    use Brickoo\Http\Request\Factory\Resolver\Url;

    class UrlTest extends \PHPUnit_Framework_TestCase {

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
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::__construct
         */
        public function testConstructor() {
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $_SERVER = array("SERVER_NAME" => "localhost");

            $UrlResolver = new Url($Header);
            $this->assertAttributeSame($Header, "Header", $UrlResolver);
            $this->assertAttributeEquals($_SERVER, "serverValues", $UrlResolver);
        }

        /**
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::getScheme
         */
        public function testGetSchemeFromHeadersForwardedProtocol() {
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Header->expects($this->any())
                    ->method("get")
                    ->with("X-Forwarded-Proto")
                    ->will($this->returnValue("https"));

            $UrlResolver = new Url($Header);
            $this->assertEquals("https", $UrlResolver->getScheme());
        }

        /**
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::getScheme
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::getServerVar
         */
        public function testGetSchemeFromGlobalServerValue() {
            $_SERVER = array("HTTPS" => "on");

            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Header->expects($this->any())
                    ->method("get")
                    ->will($this->returnValue(null));

            $UrlResolver = new Url($Header);
            $this->assertEquals("https", $UrlResolver->getScheme());
        }

        /**
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::getHostname
         */
        public function testGetHostnameFromHeaders() {
            $expectedHost = "brickoo.localhost";

            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Header->expects($this->any())
                    ->method("get")
                    ->with("Host")
                    ->will($this->returnValue($expectedHost));

            $UrlResolver = new Url($Header);
            $this->assertEquals($expectedHost, $UrlResolver->getHostname());
        }

        /**
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::getHostname
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::getServerVar
         */
        public function testGetHostnameFromGlobalServerName() {
            $expectedHost = "brickoo.localhost";
            $_SERVER = array("SERVER_NAME" => $expectedHost);

            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Header->expects($this->any())
                    ->method("get")
                    ->will($this->returnValue(null));

            $UrlResolver = new Url($Header);
            $this->assertEquals($expectedHost, $UrlResolver->getHostname());
        }

        /**
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::getHostname
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::getServerVar
         */
        public function testGetHostnameFromGlobalServerAddress() {
            $expectedHost = "brickoo.localhost";
            $_SERVER = array("SERVER_ADDR" => $expectedHost);

            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Header->expects($this->any())
                    ->method("get")
                    ->will($this->returnValue(null));

            $UrlResolver = new Url($Header);
            $this->assertEquals($expectedHost, $UrlResolver->getHostname());
        }

        /**
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::getPort
         */
        public function testGetPortFromHeaders() {

            $expectedPort = "8080";

            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Header->expects($this->any())
                    ->method("get")
                    ->with("X-Forwarded-Port")
                    ->will($this->returnValue($expectedPort));

            $UrlResolver = new Url($Header);
            $this->assertEquals($expectedPort, ($port = $UrlResolver->getPort()));
            $this->assertInternalType("integer", $port);

        }

        /**
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::getPort
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::getServerVar
         */
        public function testGetPortFromGlobalServerPort() {
            $expectedPort = "8080";
            $_SERVER = array("SERVER_PORT" => $expectedPort);

            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Header->expects($this->any())
                    ->method("get")
                    ->will($this->returnValue(null));

            $UrlResolver = new Url($Header);
            $this->assertEquals($expectedPort, ($port = $UrlResolver->getPort()));
            $this->assertInternalType("integer", $port);
        }

        /**
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::getQueryString
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::getServerVar
         */
        public function testGetQueryStringFromGlobalServerQueryString() {
            $expectedQueryString = "key=value1&key2=value2";
            $_SERVER = array("QUERY_STRING" => $expectedQueryString);

            $UrlResolver = new Url($this->getMock('Brickoo\Http\Message\Interfaces\Header'));
            $this->assertEquals($expectedQueryString, $UrlResolver->getQueryString());
        }

        /**
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::getQueryString
         */
        public function testGetQueryStringFromGlobalGETVariables() {
            $backupGlobalGetValues = $_GET;
            $expectedQueryString = "key1=value1&key2=value2";
            $_GET = array("key1" => "value1", "key2" => "value2");

            $UrlResolver = new Url($this->getMock('Brickoo\Http\Message\Interfaces\Header'));
            $this->assertEquals($expectedQueryString, $UrlResolver->getQueryString());

            $_GET = $backupGlobalGetValues;
        }

        /**
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::getPath
         * @covers Brickoo\Http\Request\Factory\Resolver\Url::getIISRequestUrl
         */
        public function testGetPathFromGlobalServerRequestUri() {
            $expectedPath = "/path/to/the/script";
            $_SERVER = array("REQUEST_URI" => "?". $expectedPath);

            $UrlResolver = new Url($this->getMock('Brickoo\Http\Message\Interfaces\Header'));
            $this->assertEquals($expectedPath, $UrlResolver->getPath());
        }

    }