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

    namespace Tests\Brickoo\Http\Request;

    use Brickoo\Http\Request\Utils;

    /**
     * UtilsTest
     *
     * Test suite for the Utils class.
     * @see Brickoo\Http\Request\Utils
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class UtilsTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Http\Request\Utils::__construct
         */
        public function testConstructor() {
            $Request = $this->getMock("Brickoo\Http\Interfaces\Request");
            $Utils = new Utils($Request);
            $this->assertAttributeSame($Request, "Request", $Utils);
        }

        /**
         * @covers Brickoo\Http\Request\Utils::getClientIp
         */
        public function testGetClientIpByRemoteAddress() {
            $expectedServerAddress = "127.0.0.1";

            $Header = $this->getMock("Brickoo\Http\Message\Interfaces\Header");
            $Header->expects($this->any())
                   ->method("get")
                   ->will($this->returnValue(null));

            $Request = $this->getMock("Brickoo\Http\Interfaces\Request");
            $Request->expects($this->any())
                    ->method("getHeader")
                    ->will($this->returnValue($Header));
            $Request->expects($this->any())
                    ->method("getServerVar")
                    ->with("REMOTE_ADDR")
                    ->will($this->returnValue($expectedServerAddress));

            $Utils = new Utils($Request);
            $this->assertEquals($expectedServerAddress, $Utils->getClientIp(array($expectedServerAddress)));
        }

        /**
         * @covers Brickoo\Http\Request\Utils::getClientIp
         * @covers Brickoo\Http\Request\Utils::getForwardedClientIp
         */
        public function testGetClientIpByForwardedIps() {
            $reverseProxyIP = "127.0.0.1";
            $expectedServerAddress = "88.77.66.55";

            $Header = $this->getMock("Brickoo\Http\Message\Interfaces\Header");
            $Header->expects($this->once())
                   ->method("get")
                   ->with("X-Forwarded-For")
                   ->will($this->returnValue("88.77.66.55"));

            $Request = $this->getMock("Brickoo\Http\Interfaces\Request");
            $Request->expects($this->any())
                    ->method("getHeader")
                    ->will($this->returnValue($Header));
            $Request->expects($this->any())
                    ->method("getServerVar")
                    ->will($this->returnValue($reverseProxyIP));

            $Utils = new Utils($Request);
            $this->assertEquals($expectedServerAddress, $Utils->getClientIp(array($reverseProxyIP)));
        }

        /**
         * @covers Brickoo\Http\Request\Utils::getClientIp
         */
        public function testGetClientIpByHeaderValue() {
            $reverseProxyIP = "127.0.0.1";
            $expectedServerAddress = "88.77.66.55";

            $valueMap = array(
                array("X-Forwarded-For", null, null),
                array("Client-Ip", null, $expectedServerAddress)
            );

            $Header = $this->getMock("Brickoo\Http\Message\Interfaces\Header");
            $Header->expects($this->any())
                   ->method("get")
                   ->will($this->returnValueMap($valueMap));

            $Request = $this->getMock("Brickoo\Http\Interfaces\Request");
            $Request->expects($this->any())
                    ->method("getHeader")
                    ->will($this->returnValue($Header));
            $Request->expects($this->any())
                    ->method("getServerVar")
                    ->will($this->returnValue($reverseProxyIP));

            $Utils = new Utils($Request);
            $this->assertEquals($expectedServerAddress, $Utils->getClientIp(array($reverseProxyIP)));
        }

        /**
         * @covers Brickoo\Http\Request\Utils::isSecureConnection
         */
        public function testIsSecureConnectionForwarded() {
            $Header = $this->getMock("Brickoo\Http\Message\Interfaces\Header");
            $Header->expects($this->any())
                   ->method("get")
                   ->with("X-Forwarded-Proto")
                   ->will($this->returnValue("HTTPS"));

            $Request = $this->getMock("Brickoo\Http\Interfaces\Request");
            $Request->expects($this->any())
                    ->method("getHeader")
                    ->will($this->returnValue($Header));

            $Utils = new Utils($Request);
            $this->assertTrue($Utils->isSecureConnection());
        }

        /**
         * @covers Brickoo\Http\Request\Utils::isSecureConnection
         */
        public function testIsSecureConnection() {
            $Header = $this->getMock("Brickoo\Http\Message\Interfaces\Header");
            $Header->expects($this->any())
                   ->method("get")
                   ->with("X-Forwarded-Proto")
                   ->will($this->returnValue(null));

            $Request = $this->getMock("Brickoo\Http\Interfaces\Request");
            $Request->expects($this->any())
                    ->method("getHeader")
                    ->will($this->returnValue($Header));
            $Request->expects($this->any())
                    ->method("getServerVar")
                    ->with("HTTPS")
                    ->will($this->returnValue("off"));

            $Utils = new Utils($Request);
            $this->assertFalse($Utils->isSecureConnection());
        }

    }