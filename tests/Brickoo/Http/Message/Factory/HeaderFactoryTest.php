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

    namespace Tests\Brickoo\Http\Message\Factory;

    use Brickoo\Http\Message\Factory\HeaderFactory;

    require_once "Fixture/Functions.php";

    /**
     * HeaderFactoryTest
     *
     * Test suite for the HeaderFactory class.
     * @see Brickoo\Http\Message\Factory\HeaderFactory
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class HeaderFactoryTest extends \PHPUnit_Framework_TestCase {

        /** @var array */
        private $cachedServerVars;

        public function setUp() {
            $this->cachedServerVars = $_SERVER;
        }

        public function tearDown() {
            $_SERVER = $this->cachedServerVars;
        }

        /**
         * @covers Brickoo\Http\Message\Factory\HeaderFactory::Create
         * @covers Brickoo\Http\Message\Factory\HeaderFactory::NormalizeHeaders
         */
        public function testCreateHeader() {
            $expectedHeader = array (
                'Unittest'        => "OK",
                'Apache-Header'   => "APACHE_SERVER",
                'Content-Type'    => "application/xml",
                'Content-Length'  => 123
            );

            $_SERVER["HTTP_UNITTEST"] = "OK";
            $_SERVER["CONTENT_TYPE"] = "application/xml";
            $_SERVER["CONTENT_LENGTH"] = 123;

            $Header = HeaderFactory::Create();
            $this->assertInstanceOf('Brickoo\Http\Message\Interfaces\Header', $Header);
            $this->assertEquals($expectedHeader, $Header->toArray());
        }

        /**
         * @covers Brickoo\Http\Message\Factory\HeaderFactory::CreateFromString
         * @covers Brickoo\Http\Message\Factory\HeaderFactory::NormalizeHeaders
         */
        public function testCreateHeaderFromString() {
            $expectedHeader = array(
                "Accept"            => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Accept-Language"   => "en-us,en;q=0.5",
                "Accept-Encoding"   => "gzip,deflate",
                "Accept-Charset"    => "ISO-8859-1,utf-8;q=0.7,*;q=0.7"
            );

            $headers = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n".
                       "Accept-Language: en-us,en;q=0.5\r\n".
                       "Accept-Encoding: gzip,deflate\r\n".
                       "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";

            $Header = HeaderFactory::CreateFromString($headers);
            $this->assertInstanceOf('Brickoo\Http\Message\Interfaces\Header', $Header);
            $this->assertEquals($expectedHeader, $Header->toArray());
        }

        /**
         * @covers Brickoo\Http\Message\Factory\HeaderFactory::CreateFromString
         * @expectedException InvalidArgumentException
         */
        public function testCreateFromStringThrowsInvalidArgumentException() {
            $Header = HeaderFactory::CreateFromString(array("wrongType"));
        }

    }