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

    namespace Tests\Brickoo\Http\Request\Factory;

    use Brickoo\Http\Request\Factory\Url;

    /**
     * UrlTest
     *
     * Test suite for the Factory\Url class.
     * @see Brickoo\Http\Request\Factory\Url
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class UrlTest extends \PHPUnit_Framework_TestCase {

        /**
         * Returns a url resolver mock object.
         * @return \Brickoo\Http\Request\Factory\Resolver\Url
         */
        private function getUrlResolver() {
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Resolver = $this->getMock(
                'Brickoo\Http\Request\Factory\Resolver\Url',
                array("getScheme", "getHostname", "getPort", "getQueryString", "getPath"),
                array($Header)
            );
            $Resolver->expects($this->any())
                     ->method("getScheme")
                     ->will($this->returnValue("http"));
            $Resolver->expects($this->any())
                     ->method("getHostname")
                     ->will($this->returnValue("resolver.localhost"));
            $Resolver->expects($this->any())
                     ->method("getPort")
                     ->will($this->returnValue(8080));
            $Resolver->expects($this->any())
                     ->method("getPath")
                     ->will($this->returnValue("/path/to/script"));

            return $Resolver;
        }

        /**
         * @covers Brickoo\Http\Request\Factory\Url::Create
         */
        public function testCreateWithResolver() {
            $Resolver = $this->getUrlResolver();
            $Query = $this->getMock('Brickoo\Http\Request\Interfaces\Query');

            $Url = Url::Create($Resolver, $Query);
            $this->assertInstanceOf('Brickoo\Http\Request\Interfaces\Url', $Url);
            $this->assertSame($Query, $Url->getQuery());
        }

        /**
         * @covers Brickoo\Http\Request\Factory\Url::CreateFromString
         */
        public function testImportFromString() {
            $expectedQueryString = "param1=value1&param2=value2";

            $urlString = "https://testcase.locahost/path/to/script.php?param1=value1&param2=value2";
            $Url = Url::CreateFromString($urlString);
            $this->assertInstanceOf('Brickoo\Http\Request\Interfaces\Query', ($Query = $Url->getQuery()));
            $this->assertEquals($expectedQueryString, $Query->toString());
        }

        /**
         * @covers Brickoo\Http\Request\Factory\Url::CreateFromString
         * @expectedException InvalidArgumentException
         */
        public function testCreateFromStringThrowsInvalidArgumentException() {
            Url::CreateFromString(array("wrongType"));
        }

        /**
         * @covers Brickoo\Http\Request\Factory\Url::CreateFromString
         * @expectedException InvalidArgumentException
         */
        public function testCreateFromStringThrowsNotAcceptableUrlException() {
            Url::CreateFromString("http-not-valid-url");
        }

    }