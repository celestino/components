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

    namespace Tests\Brickoo\Http\Message;

    use Brickoo\Http\Message\Body;

    /**
     * BodyTest
     *
     * Test suite for the Body class.
     * @see Brickoo\Http\Message\Body
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class BodyTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Http\Message\Body::__construct
         */
        public function testConstructor() {
            $expectedBody = "test message body content";
            $Body = new Body($expectedBody);
            $this->assertInstanceOf("Brickoo\Http\Message\Interfaces\Body", $Body);
            $this->assertAttributeEquals($expectedBody, "content", $Body);
        }

        /**
         * @covers Brickoo\Http\Message\Body::getContent
         */
        public function testToString() {
            $expectedBody = "test message body content";
            $Body = new Body($expectedBody);
            $this->assertEquals($expectedBody, $Body->getContent());
        }

        /**
         * @covers Brickoo\Http\Message\Body::send
         */
        public function testSendBody() {
            $expectedBody = "test message body content";
            $this->expectOutputString($expectedBody);
            $Body = new Body($expectedBody);
            $Body->send();
        }

    }