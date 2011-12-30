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

    use Brickoo\Library\Log\Handler\SyslogNGHandler;


    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * SyslogNGHandlerTest
     *
     * Test case for the SyslogNGHandler class.
     * @see Brickoo\Library\Log\Handler\SyslogNGHandler
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     * @version $Id$
     */

    class SyslogNGHandlerTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the SyslogNGHandler.
         * @var SyslogNGHandler
         */
        protected $SyslogNGHandler;

        /**
         * Set up the SyslogNGHandler object used.
         */
        public function setUp()
        {
            $this->SyslogNGHandler = new SyslogNGHandler;
        }

        /**
         * Returns an Stub of the SocketObject class.
         * @param array $methods the methods to mock
         * @return object SocketObject Stub
         */
        public function getSocketObjectStub(array $methods = null)
        {
            return $this->getMock
            (
                'Brickoo\Library\System\SocketObject',
                ($methods === null ? null : array_values($methods))
            );
        }

        /**
         * Test if the SyslogNGHandler can be created.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::__construct
         */
        public function testConstruct()
        {
            $this->assertInstanceOf('Brickoo\Library\Log\Handler\SyslogNGHandler', $this->SyslogNGHandler);
        }

        /**
         * Test is the injected SocketObject can be retrieved.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::getSocketObject
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::injectSocketObject
         */
        public function testGetSocketObject()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Library\System\Interfaces\SocketObjectInterface',
                $this->SyslogNGHandler->getSocketObject()
            );
        }

        /**
         * Test if the SocketObject dependency can be injected.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::injectSocketObject
         */
        public function testInjectSocketObject()
        {
            $SocketObject = $this->getSocketObjectStub();
            $this->assertSame($this->SyslogNGHandler, $this->SyslogNGHandler->injectSocketObject($SocketObject));
        }

        /**
         * Test if trying to override dependency throws an exception.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::injectSocketObject
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         */
        public function testInjectSocketObjectOverrideException()
        {
            $SocketObject = $this->getSocketObjectStub();
            $this->SyslogNGHandler->injectSocketObject($SocketObject);
            $this->SyslogNGHandler->injectSocketObject($SocketObject);
        }

        /**
         * Test if the hostname can be retrieved.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::getHostname
         */
        public function testGetHostname()
        {
            $this->SyslogNGHandler->setHostname('testdomain.com');
            $this->assertEquals('testdomain.com', $this->SyslogNGHandler->getHostname());
        }

        /**
         * Test if the trying to retrieve not available hostname throws an exception.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::getHostname
         * @expectedException UnexpectedValueException
         */
        public function testGetHostnameValueException()
        {
            $this->SyslogNGHandler->getHostname();
        }

        /**
         * Test if the hostname can be set and the object refenrece is returned.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::setHostname
         */
        public function testSetHostname()
        {
            $this->assertSame($this->SyslogNGHandler, $this->SyslogNGHandler->setHostname('testdomain.com'));
            $this->assertEquals('testdomain.com', $this->SyslogNGHandler->getHostname());
        }

        /**
         * Test if the trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::setHostname
         * @expectedException InvalidArgumentException
         */
        public function testSetHostnameArgumentException()
        {
            $this->SyslogNGHandler->setHostname(array('wrongType'));
        }

        /**
         * Test if the server adress can be retrieved.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::getServerAdress
         */
        public function testGetServerAdress()
        {
            $this->SyslogNGHandler->setServerAdress('www.someadress.com');
            $this->assertEquals('www.someadress.com', $this->SyslogNGHandler->getServerAdress());
        }

        /**
         * Test if the trying to retrieve not available server adress throws an exception.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::getServerAdress
         * @expectedException UnexpectedValueException
         */
        public function testGetServerAdressValueException()
        {
            $this->SyslogNGHandler->getServerAdress();
        }

        /**
         * Test if the server adress can be set and the object refenrece is returned.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::setServerAdress
         */
        public function testSetServerAdress()
        {
            $this->assertSame($this->SyslogNGHandler, $this->SyslogNGHandler->setServerAdress('www.adress.com'));
            $this->assertEquals('www.adress.com', $this->SyslogNGHandler->getServerAdress());
        }

        /**
         * Test if the trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::setServerAdress
         * @expectedException InvalidArgumentException
         */
        public function testSetAdressArgumentException()
        {
            $this->SyslogNGHandler->setServerAdress(array('wrongType'));
        }

        /**
         * Test if the server port can be retrieved.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::getServerPort
         */
        public function testGetServerPort()
        {
            $this->SyslogNGHandler->setServerPort(12345);
            $this->assertEquals(12345, $this->SyslogNGHandler->getServerPort());
        }

        /**
         * Test if the trying to retrieve not available server port throws an exception.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::getServerPort
         * @expectedException UnexpectedValueException
         */
        public function testGetServerPortValueException()
        {
            $this->SyslogNGHandler->getServerPort();
        }

        /**
         * Test if the server port can be set and the object refenrece is returned.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::setServerPort
         */
        public function testSetServerPort()
        {
            $this->assertSame($this->SyslogNGHandler, $this->SyslogNGHandler->setServerPort(123));
            $this->assertEquals(123, $this->SyslogNGHandler->getServerPort());
        }

        /**
         * Test if the trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::setServerPort
         * @expectedException InvalidArgumentException
         */
        public function testSetPortArgumentException()
        {
            $this->SyslogNGHandler->setServerPort(array('wrongType'));
        }

        /**
         * Test if the timeout can be retrieved.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::getTimeout
         */
        public function testGetTimeout()
        {
            $this->SyslogNGHandler->setTimeout(60);
            $this->assertEquals(60, $this->SyslogNGHandler->getTimeout());
        }

        /**
         * Test if the trying to retrieve not available timeout throws an exception.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::getTimeout
         * @expectedException UnexpectedValueException
         */
        public function testGetTimeoutValueException()
        {
            $this->SyslogNGHandler->getTimeout();
        }

        /**
         * Test if the timeout can be set and the object refenrece is returned.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::setTimeout
         */
        public function testSetTimeout()
        {
            $this->assertSame($this->SyslogNGHandler, $this->SyslogNGHandler->SetTimeout(30));
            $this->assertEquals(30, $this->SyslogNGHandler->getTimeout());
        }

        /**
         * Test if the trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::setTimeout
         * @expectedException InvalidArgumentException
         */
        public function testSetTimeoutArgumentException()
        {
            $this->SyslogNGHandler->setTimeout('wrongType');
        }

        /**
         * Test if the facility can be retrieved.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::getFacility
         */
        public function testGetFacility()
        {
            $this->SyslogNGHandler->setFacility(10);
            $this->assertEquals(10, $this->SyslogNGHandler->getFacility());
        }

        /**
         * Test if the trying to retrieve not available timeout throws an exception.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::getFacility
         * @expectedException UnexpectedValueException
         */
        public function testGetFacilityValueException()
        {
            $this->SyslogNGHandler->getFacility();
        }

        /**
         * Test if the facility can be set and the object refenrece is returned.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::setFacility
         */
        public function testSetFacility()
        {
            $this->assertSame($this->SyslogNGHandler, $this->SyslogNGHandler->setFacility(13));
            $this->assertEquals(13, $this->SyslogNGHandler->getFacility());
        }

        /**
         * Test if the trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::setFacility
         * @expectedException InvalidArgumentException
         */
        public function testSetFacilityArgumentException()
        {
            $this->SyslogNGHandler->setFacility('wrongType');
        }

        /**
         * Test if the trying to use a wrong argument range throws an exception.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::setFacility
         * @expectedException OutOfRangeException
         */
        public function testSetFacilityRangeException()
        {
            $this->SyslogNGHandler->setFacility(99);
        }

        /**
         * Test if clearing the object properties returns the object referece.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::clear
         */
        public function testClear()
        {
            $this->assertSame($this->SyslogNGHandler, $this->SyslogNGHandler->clear());
        }

        /**
         * Test if the message header returns the expected value format.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::getMessageHeader
         */
        public function testGetMessageHeader()
        {
            $this->SyslogNGHandler->setHostname('testdomain.com');
            $this->SyslogNGHandler->setFacility(10);
            $this->assertEquals
            (
                '<85>' . date('c') . ' testdomain.com',
                $this->SyslogNGHandler->getMessageHeader(5)
            );
        }

        /**
         * Test if the trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::getMessageHeader
         * @expectedException InvalidArgumentException
         */
        public function testGetMessageHeaderArgumentException()
        {
            $this->SyslogNGHandler->getMessageHeader('wrongType');
        }

        /**
         * Test if messages can be sent and the SocketObject is used.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::log
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::sendMessages
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::getMessageHeader
         */
        public function testLog()
        {
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

            $this->SyslogNGHandler->injectSocketObject($SocketObject)
                                  ->setServerAdress('localhost')
                                  ->setServerPort(514)
                                  ->setTimeout(60)
                                  ->setHostname('home')
                                  ->setFacility(SyslogNGHandler::FACILITY_USER_0);

            $this->assertSame
            (
                $this->SyslogNGHandler,
                $this->SyslogNGHandler->log('message', SyslogNGHandler::SEVERITY_INFO)
            );
        }

        /**
         * Test if the trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Log\Handler\SyslogNGHandler::log
         * @expectedException InvalidArgumentException
         */
        public function testLogSeverityArgumentException()
        {
            $this->SyslogNGHandler->log('messge', 'wrongType');
        }

    }

?>
