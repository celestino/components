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

    use Brickoo\System\SocketObject;


    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * SocketObjectTest
     *
     * Test suite for the SocketObject class.
     * @see Brickoo\System\SocketObject
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class SocketObjectTest extends PHPUnit_Framework_TestCase {

        /**
         * Holds the SocketObject instance.
         * @var SocketObject
         */
        protected $SocketObject;

        /**
         * Holds the fixture for the SocketObject.
         * @var SocketObjectFixture
         */
        protected $SocketObjectFixture;

        /**
         * Set up the SocketObject used.
         * @return void
         */
        public function setUp() {
            $this->SocketObject = new SocketObject();
            $this->SocketObjectFixture = new SocketObjectFixture();
        }

        /**
         * Test if the constructor implements the SocketObject.
         * @covers Brickoo\System\SocketObject::__construct
         */
        public function testConstruct() {
            $this->assertInstanceOf
            (
                'Brickoo\System\Interfaces\SocketObject',
                new SocketObject()
            );
        }

        /**
         * Test if the protocol can be retrieved and the default value is an empty string.
         * @covers Brickoo\System\SocketObject::getProtocol
         */
        public function testGetProtocol() {
            $this->assertEquals('' , $this->SocketObject->getProtocol());
            $this->SocketObject->setProtocol('upd');
            $this->assertEquals('upd://', $this->SocketObject->getProtocol());
        }

        /**
         * Test if the protocol can be set and the object reference is returned.
         * @covers Brickoo\System\SocketObject::setProtocol
         */
        public function testSetProtocol() {
            $this->assertSame($this->SocketObject, $this->SocketObject->setProtocol('http'));
            $this->assertEquals('http://', $this->SocketObject->getProtocol());
        }

        /**
         * Test is passing a wrong argument type throws an exception.
         * @covers Brickoo\System\SocketObject::setProtocol
         * @expectedException InvalidArgumentException
         */
        public function testSetProtocolArgumentException() {
            $this->SocketObject->setProtocol(array('wrongType'));
        }

        /**
         * Test if a handle is available trying to change the protocol throws an exception.
         * @covers Brickoo\System\SocketObject::setProtocol
         * @covers Brickoo\System\Exceptions\HandleAlreadyExistsException
         * @expectedException Brickoo\System\Exceptions\HandleAlreadyExistsException
         */
        public function testSetProtocolHandleException() {
            $this->SocketObjectFixture->setProtocol('udp');
        }

        /**
         * Test if the server adress can be retrieved.
         * @covers Brickoo\System\SocketObject::getServerAdress
         */
        public function testGetServerAdress() {
            $this->SocketObject->setServerAdress('someadress.com');
            $this->assertEquals('someadress.com', $this->SocketObject->getServerAdress());
        }

        /**
         * Test if trying to retrive the server adress not assigned throws an exception.
         * @covers Brickoo\System\SocketObject::getServerAdress
         * @expectedException UnexpectedValueException
         */
        public function testGetServerAdressValueException() {
            $this->SocketObject->getServerAdress();
        }

        /**
         * Test if the server adress can be set and the object reference is returned.
         * @covers Brickoo\System\SocketObject::setServerAdress
         */
        public function testSetServerAdress() {
            $this->assertSame($this->SocketObject, $this->SocketObject->setServerAdress('someadress.com'));
            $this->assertEquals('someadress.com', $this->SocketObject->getServerAdress());
        }

        /**
         * Test if trying to set an wrong argument type throws an exception.
         * @covers Brickoo\System\SocketObject::setServerAdress
         * @expectedException InvalidArgumentException
         */
        public function testSetServerAdressArgumentException() {
            $this->SocketObject->setServerAdress(array('wrongType'));
        }

        /**
         * Test if a handle is available trying to change the server adress throws an exception.
         * @covers Brickoo\System\SocketObject::setServerAdress
         * @covers Brickoo\System\Exceptions\HandleAlreadyExistsException
         * @expectedException Brickoo\System\Exceptions\HandleAlreadyExistsException
         */
        public function testSetServerAdressHandleException() {
            $this->SocketObjectFixture->setServerAdress('someadress.com');
        }

        /**
         * Test if the server port can be retrieved.
         * @covers Brickoo\System\SocketObject::getServerPort
         */
        public function testGetServerPort() {
            $this->SocketObject->setServerPort(1024);
            $this->assertEquals(1024, $this->SocketObject->getServerPort());
        }

        /**
         * Test if trying to retrive the server port not assigned throws an exception.
         * @covers Brickoo\System\SocketObject::getServerPort
         * @expectedException UnexpectedValueException
         */
        public function testGetServerPortValueException() {
            $this->SocketObject->getServerPort();
        }

        /**
         * Test if the server port can be set and the object reference is returned.
         * @covers Brickoo\System\SocketObject::setServerPort
         */
        public function testSetServerPort() {
            $this->assertSame($this->SocketObject, $this->SocketObject->setServerPort(1024));
            $this->assertEquals(1024, $this->SocketObject->getServerPort());
        }

        /**
         * Test if trying to set an wrong argument type throws an exception.
         * @covers Brickoo\System\SocketObject::setServerPort
         * @expectedException InvalidArgumentException
         */
        public function testSetServerPortArgumentException() {
            $this->SocketObject->setServerPort('wrongType');
        }

        /**
         * Test if a handle is available trying to change the server port throws an exception.
         * @covers Brickoo\System\SocketObject::setServerPort
         * @covers Brickoo\System\Exceptions\HandleAlreadyExistsException
         * @expectedException Brickoo\System\Exceptions\HandleAlreadyExistsException
         */
        public function testSetServerPortHandleException() {
            $this->SocketObjectFixture->setServerPort(1024);
        }

        /**
         * Test if the timeout can be retrieved.
         * @covers Brickoo\System\SocketObject::getTimeout
         */
        public function testGetTimeout() {
            $this->SocketObject->setTimeout(35);
            $this->assertEquals(35, $this->SocketObject->getTimeout());
        }

        /**
         * Test if trying to retrive the timeout not assigned throws an exception.
         * @covers Brickoo\System\SocketObject::getTimeout
         * @expectedException UnexpectedValueException
         */
        public function testGetTimeoutValueException() {
            $this->SocketObject->getTimeout();
        }

        /**
         * Test if the timeout can be set and the object reference is returned.
         * @covers Brickoo\System\SocketObject::setTimeout
         */
        public function testSetTimeout() {
            $this->assertSame($this->SocketObject, $this->SocketObject->setTimeout(60));
            $this->assertEquals(60, $this->SocketObject->getTimeout());
        }

        /**
         * Test if trying to set an wrong argument type throws an exception.
         * @covers Brickoo\System\SocketObject::setTimeout
         * @expectedException InvalidArgumentException
         */
        public function testSetTimeoutArgumentException() {
            $this->SocketObject->setTimeout('wrongType');
        }

        /**
         * Test if a handle is available trying to change the timeout throws an exception.
         * @covers Brickoo\System\SocketObject::setTimeout
         * @covers Brickoo\System\Exceptions\HandleAlreadyExistsException
         * @expectedException Brickoo\System\Exceptions\HandleAlreadyExistsException
         */
        public function testSetTimeoutHandleException() {
            $this->SocketObjectFixture->setTimeout(1024);
        }

        /**
         * Test if the socket can be opened with the configuration done.
         * @covers Brickoo\System\SocketObject::open
         */
        public function testOpen() {
            $this->SocketObject->setProtocol('tcp')
                               ->setServerAdress('google.com')
                               ->setServerPort(80)
                               ->setTimeout(10);

            try {
                $this->assertInternalType('resource', $this->SocketObject->open());
            } catch(\Brickoo\System\Exceptions\UnableToCreateHandleException $Exception) {
                $this->markTestSkipped($Exception->getMessage());
            }
        }

        /**
         * Test if trying to reopen a socket connection throws an exception.
         * @covers Brickoo\System\SocketObject::open
         * @covers Brickoo\System\Exceptions\HandleAlreadyExistsException
         * @expectedException Brickoo\System\Exceptions\HandleAlreadyExistsException
         */
        public function testOpenHandleExistsException() {
            $this->SocketObject->setProtocol('tcp')
                               ->setServerAdress('google.com')
                               ->setServerPort(80)
                               ->setTimeout(10);


            try {
                $this->assertInternalType('resource', $this->SocketObject->open());
            } catch(Brickoo\System\Exceptions\UnableToCreateHandleException $Exception) {
                return $this->markTestSkipped($Exception->getMessage());
            }
            $this->SocketObject->open();
        }

        /**
         * Test if a failure of creating the handle throws an exception.
         * @covers Brickoo\System\SocketObject::open
         * @covers Brickoo\System\Exceptions\UnableToCreateHandleException
         * @expectedException Brickoo\System\Exceptions\UnableToCreateHandleException
         */
        public function testOpenHandleException() {
            $this->SocketObject->setProtocol('whatever')
                               ->setServerAdress('otherplace')
                               ->setServerPort(80)
                               ->setTimeout(1);

            $this->SocketObject->open();
        }

        /**
         * Test if the handle can be retrieved.
         * @covers Brickoo\System\SocketObject::getHandle
         */
        public function testGetHandle() {
            $this->assertInternalType('resource', $this->SocketObjectFixture->getHandle());
        }

        /**
         * Test if the handle opened can be retrived.
         * @covers Brickoo\System\SocketObject::getHandle
         */
        public function testGetHandleWithOpen() {
            $this->SocketObject->setProtocol('tcp')
                               ->setServerAdress('google.com')
                               ->setServerPort(80)
                               ->setTimeout(10);
            try {
                $this->assertInternalType('resource', $this->SocketObject->getHandle());
            } catch(\Brickoo\System\Exceptions\UnableToCreateHandleException $Exception) {
                $this->markTestSkipped($Exception->getMessage());
            }
        }

        /**
         * Test if the handle is recognized.
         * @covers Brickoo\System\SocketObject::hasHandle
         */
        public function testHasHandle() {
            $this->assertFalse($this->SocketObject->hasHandle());
            $this->assertTrue($this->SocketObjectFixture->hasHandle());
        }

        /**
         * Test if the handle can be removed.
         * @covers Brickoo\System\SocketObject::removeHandle
         */
        public function testRemoveHandle() {
            $this->assertTrue($this->SocketObjectFixture->hasHandle());
            $this->assertSame($this->SocketObjectFixture, $this->SocketObjectFixture->removeHandle());
            $this->assertFalse($this->SocketObjectFixture->hasHandle());
        }

        /**
         * Test if the handle is removed if the object is destroyed.
         * @covers Brickoo\System\SocketObject::__destruct
         */
        public function test__destruct() {
            $SocketObjectStub = $this->getMock('Brickoo\System\SocketObject', array('removeHandle'));
            $SocketObjectStub->expects($this->once())
                            ->method('removeHandle')
                            ->will($this->returnSelf());

            $this->assertNull($SocketObjectStub->__destruct());
        }

        /**
         * Test if the handle can be close ant he object reference is returned.
         * @covers Brickoo\System\SocketObject::close
         */
        public function testClose() {
            $this->assertSame($this->SocketObjectFixture, $this->SocketObjectFixture->close());
        }

        /**
         * Test if trying to close an handle not available throws an exception.
         * @covers Brickoo\System\SocketObject::close
         * @covers Brickoo\System\Exceptions\HandleNotAvailableException
         * @expectedException Brickoo\System\Exceptions\HandleNotAvailableException
         */
        public function testCloseHandleException() {
            $this->SocketObject->close();
        }

        /**
         * Test if an magic method fwrite() can be called.
         * @covers Brickoo\System\SocketObject::__call
         */
        public function test__call() {
            $data = 'some test data';
            $this->assertEquals(strlen($data), $this->SocketObjectFixture->fwrite($data));
        }

        /**
         * Test if trying calling fsockopen() throws an exception,
         * @covers Brickoo\System\SocketObject::__call
         * @expectedException BadMethodCallException
         */
        public function test__callFsockopenException() {
            $this->SocketObject->fsockopen();
        }

        /**
         * Test if trying calling fclose() throws an exception,
         * @covers Brickoo\System\SocketObject::__call
         * @expectedException BadMethodCallException
         */
        public function test__callFcloseException() {
            $this->SocketObject->fclose();
        }

    }

    /**
     * SocketObjectFixture with the handle assigned.
     */
    class SocketObjectFixture extends SocketObject {
        public function __construct() {
            $this->handle = fopen('php://memory', 'w+');
        }
    }