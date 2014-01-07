<?php

    /*
     * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
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

    namespace Tests\Brickoo\Http\Message;

    use Brickoo\Http\Message\Header;

    /**
     * HeaderTest
     *
     * Test suite for the Header class.
     * @see Brickoo\Http\Message\Header
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class HeaderTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Http\Message\Header::__construct
         */
        public function testConstructor() {
            $Header = new Header();
            $this->assertInstanceOf("Brickoo\Http\Message\Interfaces\Header", $Header);
        }

        /**
         * @covers Brickoo\Http\Message\Header::send
         * @covers Brickoo\Http\Message\Header::normalizeHeaders
         */
        public function testSendHeaderMustBeSorted() {
            $expectedOutput = "First: HEADER\r\nUnit: TEST\r\n";
            $this->expectOutputString($expectedOutput);

            $output = "";
            $Header = new Header();
            $Header->fromArray(array("Unit" => "TEST", "FIRST" => "HEADER"));
            $Header->send(function ($header) use (&$output) {$output .= $header ."\r\n";});

            echo ($output);
        }

        /**
         * @covers Brickoo\Http\Message\Header::toString
         * @covers Brickoo\Http\Message\Header::normalizeHeaders
         */
        public function testToStringMustBeSorted() {
            $headers = array(
                "Accept-Charset"    => "ISO-8859-1,utf-8;q=0.7,*;q=0.7",
                "Accept-Encoding"   => "gzip,deflate",
                "Accept"            => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Accept-Language"   => "en-us,en;q=0.5"
            );

            $expectedHeader = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n".
               "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n".
               "Accept-Encoding: gzip,deflate\r\n".
               "Accept-Language: en-us,en;q=0.5\r\n";

            $Header = new Header();
            $this->assertSame($Header, $Header->fromArray($headers));
            $this->assertEquals($expectedHeader, $Header->toString());
        }

    }