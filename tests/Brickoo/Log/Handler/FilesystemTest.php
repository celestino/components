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

    namespace Tests\Brickoo\Log\Handler;

    use Brickoo\Log\Handler\Filesystem;

    /**
     * FilesystemTest
     *
     * Test suite for the Filesystem class.
     * @see Brickoo\Log\Handler\Filesystem
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class FilesystemTest extends \PHPUnit_Framework_TestCase {

        /**
        * @covers Brickoo\Log\handler\Filesystem::__construct
        */
        public function testConstructor() {
            $Client = $this->getMock('Brickoo\Filesystem\Interfaces\Client');
            $Filesystem = new Filesystem($Client, "/tmp/test");
            $this->assertInstanceOf('\Brickoo\Log\Handler\Interfaces\Handler', $Filesystem);
            $this->assertAttributeSame($Client, "Client", $Filesystem);
            $this->assertAttributeSame("/tmp/test".DIRECTORY_SEPARATOR, "logsDirectory", $Filesystem);
        }

        /**
         * @covers \Brickoo\Log\Handler\Filesystem::log
         * @covers \Brickoo\Log\Handler\Filesystem::convertToLogMessage
         */
        public function testlog() {
            date_default_timezone_set('UTC');

            $logMessage = "Message to log.";
            $expectedFilename = "/var/log". DIRECTORY_SEPARATOR . date("Y-m-d") .".log";
            $expectedRegexMessage = "~^\[[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2}\]\[[a-zA-Z]+\] ". $logMessage ."$~";

            $Client = $this->getMock('Brickoo\Filesystem\Interfaces\Client');
            $Client->expects($this->once())
                       ->method("open")
                       ->with($expectedFilename, "a", false, null)
                       ->will($this->returnSelf());
            $Client->expects($this->once())
                       ->method("write")
                       ->with($this->matchesRegularExpression($expectedRegexMessage));
            $Client->expects($this->once())
                       ->method("close")
                       ->will($this->returnSelf());

            $Filesystem = new Filesystem($Client, "/var/log");
            $this->assertNull($Filesystem->log($logMessage, 99999));
        }

        /**
        * @covers \Brickoo\Log\Handler\Filesystem::log
        * @expectedException InvalidArgumentException
        */
        public function testlogArgumentException() {
            $Client = $this->getMock('Brickoo\Filesystem\Interfaces\Client');
            $Filesystem = new Filesystem($Client, "/var/log/");
            $Filesystem->log("message", "wrongType");
        }

    }