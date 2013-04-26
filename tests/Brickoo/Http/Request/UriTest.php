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

    namespace Tests\Brickoo\Http\Component;

    use Brickoo\Http\Request\Uri;

    /**
     * UriTest
     *
     * Test suite for the Uri class.
     * @see Brickoo\Http\Request\Uri
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class UriTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Http\Request\Uri::__construct
         */
        public function testConstructor() {
            $Query = $this->getMock('Brickoo\Http\Request\Interfaces\Query');
            $scheme = "http";
            $hostname = "testcase.localhost";
            $port = 8080;
            $path = "/path/to/script";
            $fragment = "ankerLink1";

            $Uri = new Uri($scheme, $hostname, $port, $path, $Query, $fragment);
            $this->assertInstanceOf('Brickoo\Http\Request\Interfaces\Uri', $Uri);
            $this->assertAttributeEquals($scheme, "scheme", $Uri);
            $this->assertAttributeEquals($hostname, "hostname", $Uri);
            $this->assertAttributeEquals($port, "port", $Uri);
            $this->assertAttributeEquals($path, "path", $Uri);
            $this->assertAttributeEquals($fragment, "fragment", $Uri);
            $this->assertAttributeSame($Query, "Query", $Uri);

        }

        /**
         * @covers Brickoo\Http\Request\Uri::getScheme
         */
        public function testGetScheme() {
            $Uri = $this->getUriFixture();
            $this->assertEquals("https", $Uri->getScheme());
        }

        /**
         * @covers Brickoo\Http\Request\Uri::getHostname
         */
        public function testGetHostname() {
            $Uri = $this->getUriFixture();
            $this->assertEquals("fixture.localhost", $Uri->getHostname());
        }

        /**
         * @covers Brickoo\Http\Request\Uri::getPort
         */
        public function testGetPort() {
            $Uri = $this->getUriFixture();
            $this->assertEquals(8080, $Uri->getPort());
        }

        /**
         * @covers Brickoo\Http\Request\Uri::getPath
         */
        public function testGetPath() {
            $Uri = $this->getUriFixture();
            $this->assertEquals("/arcticles/test-cases", $Uri->getPath());
        }

        /**
         * @covers Brickoo\Http\Request\Uri::getFragment
         */
        public function testGetFragment() {
            $Uri = $this->getUriFixture();
            $this->assertEquals("ankerLink1", $Uri->getFragment());
        }

        /**
         * @covers Brickoo\Http\Request\Uri::getQuery
         */
        public function testGetQuery() {
            $Query = $this->getMock('Brickoo\Http\Request\Interfaces\Query');
            $scheme = "https";
            $hostname = "testquery.localhost";
            $port = 8080;
            $path = "/path/to/script";

            $Uri = new Uri($scheme, $hostname, $port, $path, $Query);
            $this->assertSame($Query, $Uri->getQuery());
        }

        /**
         * @covers Brickoo\Http\Request\Uri::toString
         */
        public function testToString() {
            $expectedUri = "https://fixture.localhost:8080/arcticles/test-cases?key=value1#ankerLink1";

            $Uri = $this->getUriFixture();

            $Query = $Uri->getQuery();
            $Query->expects($this->any())
                  ->method("toString")
                  ->will($this->returnValue("key=value1"));

            $this->assertEquals($expectedUri, $Uri->toString());
        }

        /**
         * Returns a Uri fixture object.
         * @return \Brickoo\Http\Request\Uri
         */
        private function getUriFixture() {
            $Query = $this->getMock('Brickoo\Http\Request\Interfaces\Query');

            $scheme = "https";
            $hostname = "fixture.localhost";
            $port = 8080;
            $path = "/arcticles/test-cases";
            $fragment = "ankerLink1";

            return new Uri($scheme, $hostname, $port, $path, $Query, $fragment);
        }

    }