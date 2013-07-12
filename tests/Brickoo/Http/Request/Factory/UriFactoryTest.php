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

    use Brickoo\Http\Request\Factory\UriFactory;

    /**
     * UriFactoryTest
     *
     * Test suite for the UriFactory class.
     * @see Brickoo\Http\Request\Factory\UriFactory
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class UriFactoryTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Http\Request\Factory\UriFactory::Create
         */
        public function testCreateWithResolver() {
            $Resolver = $this->getUriResolver();
            $Query = $this->getMock('Brickoo\Http\Request\Interfaces\Query');

            $Uri = UriFactory::Create($Resolver, $Query);
            $this->assertInstanceOf('Brickoo\Http\Request\Interfaces\Uri', $Uri);
            $this->assertSame($Query, $Uri->getQuery());
        }

        /**
         * @covers Brickoo\Http\Request\Factory\UriFactory::CreateFromString
         */
        public function testImportFromString() {
            $expectedQueryString = "param1=value1&param2=value2";
            $uriString = "https://testcase.locahost/path/to/script.php?param1=value1&param2=value2#fragment1";

            $Uri = UriFactory::CreateFromString($uriString);
            $this->assertInstanceOf('Brickoo\Http\Request\Interfaces\Query', ($Query = $Uri->getQuery()));
            $this->assertEquals($expectedQueryString, $Query->toString());
            $this->assertEquals($uriString, $Uri->toString());
        }

        /**
         * @covers Brickoo\Http\Request\Factory\UriFactory::CreateFromString
         * @expectedException InvalidArgumentException
         */
        public function testCreateFromStringThrowsInvalidArgumentException() {
            UriFactory::CreateFromString(array("wrongType"));
        }

        /**
         * @covers Brickoo\Http\Request\Factory\UriFactory::CreateFromString
         * @expectedException InvalidArgumentException
         */
        public function testCreateFromStringThrowsNotAcceptableUriException() {
            UriFactory::CreateFromString("http-not-valid-url");
        }

        /**
         * Returns a url resolver mock object.
         * @return \Brickoo\Http\Request\Factory\Resolver\UriResolver
         */
        private function getUriResolver() {
            $Header = $this->getMock('Brickoo\Http\Message\Interfaces\Header');
            $Resolver = $this->getMock(
                'Brickoo\Http\Request\Factory\Resolver\UriResolver',
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

    }