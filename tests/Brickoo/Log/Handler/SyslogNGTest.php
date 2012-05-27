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

    use Brickoo\Log\Handler\SyslogNG;


    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * SyslogNGTest
     *
     * Test suite for the SyslogNG class.
     * @see Brickoo\Log\Handler\SyslogNG
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class SyslogNGTest extends PHPUnit_Framework_TestCase {


        /**
        * Returns an Stub of the SocketObject class.
         * @param array $methods the methods to mock
        * @return object SocketObject Stub
        */
        public function getSocketObjectStub(array $methods = null) {
            return $this->getMock
            (
                'Brickoo\System\SocketObject',
                ($methods === null ? null : array_values($methods))
            );
        }

        /**
         * Holds an instance of the SyslogNG.
         * @var SyslogNG
         */
        protected $SyslogNG;

        /**
         * Set up the SyslogNG object used.
         */
        public function setUp() {
            $this->SyslogNG = new SyslogNG($this->getSocketObjectStub());
        }

        /**
         * Test if the SyslogNG can be created.
         * @covers Brickoo\Log\Handler\SyslogNG::__construct
         */
        public function testConstruct() {
            $SocketObject = $this->getSocketObjectStub();
            $this->assertInstanceOf
            (
                '\Brickoo\Log\Handler\Interfaces\Handler',
                ($SyslogHandler = new SyslogNG($SocketObject))
            );
            $this->assertAttributeSame($SocketObject, '_SocketObject', $SyslogHandler);
        }

        /**
         * Test is the injected SocketObject can be retrieved.
         * @covers Brickoo\Log\Handler\SyslogNG::SocketObject
         */
        public function testGetSocketObject() {
            $this->assertInstanceOf
            (
                'Brickoo\System\Interfaces\SocketObject',
                $this->SyslogNG->SocketObject()
            );
        }

        /**
         * Test if the hostname can be retrieved.
         * @covers Brickoo\Log\Handler\SyslogNG::getHostname
         */
        public function testGetHostname() {
            $this->SyslogNG->setHostname('testdomain.com');
            $this->assertEquals('testdomain.com', $this->SyslogNG->getHostname());
        }

        /**
         * Test if the trying to retrieve not available hostname throws an exception.
         * @covers Brickoo\Log\Handler\SyslogNG::getHostname
         * @expectedException UnexpectedValueException
         */
        public function testGetHostnameValueException() {
            $this->SyslogNG->getHostname();
        }

        /**
         * Test if the hostname can be set and the object refenrece is returned.
         * @covers Brickoo\Log\Handler\SyslogNG::setHostname
         */
        public function testSetHostname() {
            $this->assertSame($this->SyslogNG, $this->SyslogNG->setHostname('testdomain.com'));
            $this->assertEquals('testdomain.com', $this->SyslogNG->getHostname());
        }

        /**
         * Test if the trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Log\Handler\SyslogNG::setHostname
         * @expectedException InvalidArgumentException
         */
        public function testSetHostnameArgumentException() {
            $this->SyslogNG->setHostname(array('wrongType'));
        }

        /**
         * Test if the server adress can be retrieved.
         * @covers Brickoo\Log\Handler\SyslogNG::getServerAdress
         */
        public function testGetServerAdress() {
            $this->SyslogNG->setServerAdress('www.someadress.com');
            $this->assertEquals('www.someadress.com', $this->SyslogNG->getServerAdress());
        }

        /**
         * Test if the trying to retrieve not available server adress throws an exception.
         * @covers Brickoo\Log\Handler\SyslogNG::getServerAdress
         * @expectedException UnexpectedValueException
         */
        public function testGetServerAdressValueException() {
            $this->SyslogNG->getServerAdress();
        }

        /**
         * Test if the server adress can be set and the object refenrece is returned.
         * @covers Brickoo\Log\Handler\SyslogNG::setServerAdress
         */
        public function testSetServerAdress() {
            $this->assertSame($this->SyslogNG, $this->SyslogNG->setServerAdress('www.adress.com'));
            $this->assertEquals('www.adress.com', $this->SyslogNG->getServerAdress());
        }

        /**
         * Test if the trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Log\Handler\SyslogNG::setServerAdress
         * @expectedException InvalidArgumentException
         */
        public function testSetAdressArgumentException() {
            $this->SyslogNG->setServerAdress(array('wrongType'));
        }

        /**
         * Test if the server port can be retrieved.
         * @covers Brickoo\Log\Handler\SyslogNG::getServerPort
         */
        public function testGetServerPort() {
            $this->SyslogNG->setServerPort(12345);
            $this->assertEquals(12345, $this->SyslogNG->getServerPort());
        }

        /**
         * Test if the trying to retrieve not available server port throws an exception.
         * @covers Brickoo\Log\Handler\SyslogNG::getServerPort
         * @expectedException UnexpectedValueException
         */
        public function testGetServerPortValueException() {
            $this->SyslogNG->getServerPort();
        }

        /**
         * Test if the server port can be set and the object refenrece is returned.
         * @covers Brickoo\Log\Handler\SyslogNG::setServerPort
         */
        public function testSetServerPort() {
            $this->assertSame($this->SyslogNG, $this->SyslogNG->setServerPort(123));
            $this->assertEquals(123, $this->SyslogNG->getServerPort());
        }

        /**
         * Test if the trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Log\Handler\SyslogNG::setServerPort
         * @expectedException InvalidArgumentException
         */
        public function testSetPortArgumentException() {
            $this->SyslogNG->setServerPort(array('wrongType'));
        }

        /**
         * Test if the timeout can be retrieved.
         * @covers Brickoo\Log\Handler\SyslogNG::getTimeout
         */
        public function testGetTimeout() {
            $this->SyslogNG->setTimeout(60);
            $this->assertEquals(60, $this->SyslogNG->getTimeout());
        }

        /**
         * Test if the trying to retrieve not available timeout throws an exception.
         * @covers Brickoo\Log\Handler\SyslogNG::getTimeout
         * @expectedException UnexpectedValueException
         */
        public function testGetTimeoutValueException() {
            $this->SyslogNG->getTimeout();
        }

        /**
         * Test if the timeout can be set and the object refenrece is returned.
         * @covers Brickoo\Log\Handler\SyslogNG::setTimeout
         */
        public function testSetTimeout() {
            $this->assertSame($this->SyslogNG, $this->SyslogNG->SetTimeout(30));
            $this->assertEquals(30, $this->SyslogNG->getTimeout());
        }

        /**
         * Test if the trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Log\Handler\SyslogNG::setTimeout
         * @expectedException InvalidArgumentException
         */
        public function testSetTimeoutArgumentException() {
            $this->SyslogNG->setTimeout('wrongType');
        }

        /**
         * Test if the facility can be retrieved.
         * @covers Brickoo\Log\Handler\SyslogNG::getFacility
         */
        public function testGetFacility() {
            $this->SyslogNG->setFacility(10);
            $this->assertEquals(10, $this->SyslogNG->getFacility());
        }

        /**
         * Test if the trying to retrieve not available timeout throws an exception.
         * @covers Brickoo\Log\Handler\SyslogNG::getFacility
         * @expectedException UnexpectedValueException
         */
        public function testGetFacilityValueException() {
            $this->SyslogNG->getFacility();
        }

        /**
         * Test if the facility can be set and the object refenrece is returned.
         * @covers Brickoo\Log\Handler\SyslogNG::setFacility
         */
        public function testSetFacility() {
            $this->assertSame($this->SyslogNG, $this->SyslogNG->setFacility(13));
            $this->assertEquals(13, $this->SyslogNG->getFacility());
        }

        /**
         * Test if the trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Log\Handler\SyslogNG::setFacility
         * @expectedException InvalidArgumentException
         */
        public function testSetFacilityArgumentException() {
            $this->SyslogNG->setFacility('wrongType');
        }

        /**
         * Test if the trying to use a wrong argument range throws an exception.
         * @covers Brickoo\Log\Handler\SyslogNG::setFacility
         * @expectedException OutOfRangeException
         */
        public function testSetFacilityRangeException() {
            $this->SyslogNG->setFacility(99);
        }

        /**
         * Test if the message header returns the expected value format.
         * @covers Brickoo\Log\Handler\SyslogNG::getMessageHeader
         */
        public function testGetMessageHeader() {
            $this->SyslogNG->setHostname('testdomain.com');
            $this->SyslogNG->setFacility(10);
            $this->assertEquals
            (
                '<85>' . date('c') . ' testdomain.com',
                $this->SyslogNG->getMessageHeader(5)
            );
        }

        /**
         * Test if the trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Log\Handler\SyslogNG::getMessageHeader
         * @expectedException InvalidArgumentException
         */
        public function testGetMessageHeaderArgumentException() {
            $this->SyslogNG->getMessageHeader('wrongType');
        }

        /**
         * Test if messages can be sent and the SocketObject is used.
         * @covers Brickoo\Log\Handler\SyslogNG::log
         * @covers Brickoo\Log\Handler\SyslogNG::sendMessages
         * @covers Brickoo\Log\Handler\SyslogNG::getMessageHeader
         */
        public function testLog() {
            $SocketObject = $this->getSocketObjectStub
            (
                array('setProtocol', 'setServerAdress', 'setServerPort', 'setTimeout', 'fwrite', 'close')
            );
            $SocketObject->expects($this->once())
                         ->method('setProtocol')
                         ->will($this->returnSelf());
            $SocketObject->expects($this->once())
                         ->method('setServerAdress')
                         ->will($this->returnSelf());
            $SocketObject->expects($this->once())
                         ->method('setServerPort')
                         ->will($this->returnSelf());
            $SocketObject->expects($this->once())
                         ->method('setTimeout')
                         ->will($this->returnSelf());
            $SocketObject->expects($this->once())
                         ->method('fwrite')
                         ->will($this->returnValue(1024));
            $SocketObject->expects($this->once())
                         ->method('close')
                         ->will($this->returnSelf());

            $SyslogHandler = new SyslogNG($SocketObject);
            $SyslogHandler->setServerAdress('localhost')
                          ->setServerPort(514)
                          ->setTimeout(60)
                          ->setHostname('home')
                          ->setFacility(SyslogNG::FACILITY_USER_0);

            $this->assertSame($SyslogHandler, $SyslogHandler->log('message', SyslogNG::SEVERITY_INFO));
        }

        /**
         * Test if the trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Log\Handler\SyslogNG::log
         * @expectedException InvalidArgumentException
         */
        public function testLogSeverityArgumentException() {
            $this->SyslogNG->log('message', 'wrongType');
        }

    }
