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

    namespace Tests\Brickoo\Http\Component;

    use Brickoo\Http\Request\Url;

    /**
     * UrlTest
     *
     * Test suite for the Url class.
     * @see Brickoo\Http\Request\Url
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class UrlTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Http\Request\Url::__construct
         */
        public function testConstructor() {
            $Query = $this->getMock('Brickoo\Http\Request\Interfaces\Query');
            $scheme = "http";
            $hostname = "testcase.localhost";
            $port = 8080;
            $path = "/path/to/script";

            $Url = new Url($scheme, $hostname, $port, $path, $Query);
            $this->assertInstanceOf('Brickoo\Http\Request\Interfaces\Url', $Url);
            $this->assertAttributeEquals($scheme, "scheme", $Url);
            $this->assertAttributeEquals($hostname, "hostname", $Url);
            $this->assertAttributeEquals($port, "port", $Url);
            $this->assertAttributeEquals($path, "path", $Url);
        }

        /**
         * @covers Brickoo\Http\Request\Url::getScheme
         */
        public function testGetScheme() {
            $Url = $this->getUrlFixture();
            $this->assertEquals("https", $Url->getScheme());
        }

        /**
         * @covers Brickoo\Http\Request\Url::getHostname
         */
        public function testGetHostname() {
            $Url = $this->getUrlFixture();
            $this->assertEquals("fixture.localhost", $Url->getHostname());
        }

        /**
         * @covers Brickoo\Http\Request\Url::getPort
         */
        public function testGetPort() {
            $Url = $this->getUrlFixture();
            $this->assertEquals(8080, $Url->getPort());
        }

        /**
         * @covers Brickoo\Http\Request\Url::getPath
         */
        public function testGetPath() {
            $Url = $this->getUrlFixture();
            $this->assertEquals("/path/to/script", $Url->getPath());
        }

        /**
         * @covers Brickoo\Http\Request\Url::getQuery
         */
        public function testGetQuery() {
            $Query = $this->getMock('Brickoo\Http\Request\Interfaces\Query');
            $scheme = "https";
            $hostname = "testquery.localhost";
            $port = 8080;
            $path = "/path/to/script";

            $Url = new Url($scheme, $hostname, $port, $path, $Query);
            $this->assertSame($Query, $Url->getQuery());
        }

        /**
         * @covers Brickoo\Http\Request\Url::toString
         */
        public function testToString() {
            $expectedUrl = "https://fixture.localhost:8080/path/to/script?key=value1";

            $Url = $this->getUrlFixture();

            $Query = $Url->getQuery();
            $Query->expects($this->any())
                  ->method("toString")
                  ->will($this->returnValue("key=value1"));

            $this->assertEquals($expectedUrl, $Url->toString());
        }

        /**
         * Returns a Url fixture object.
         * @return \Brickoo\Http\Request\Url
         */
        private function getUrlFixture() {
            $Query = $this->getMock('Brickoo\Http\Request\Interfaces\Query');

            $scheme = "https";
            $hostname = "fixture.localhost";
            $port = 8080;
            $path = "/path/to/script";

            return new Url($scheme, $hostname, $port, $path, $Query);
        }

    }