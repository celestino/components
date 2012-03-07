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

    use Brickoo\Http\Session\Handler\CacheHandler;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * CacheHandlerTest
     *
     * Test suite for the CacheHandler class.
     * @see Brickoo\Http\Session\Handler\CacheHandler
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class CacheHandlerTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Returns a Manager stub implementig the Cache\Interfaces\ManagerInterface.
         * @param array $methods the methods to mock
         * @return object
         */
        public function getManagerStub(array $methods = null)
        {
            return $this->getMock
            (
                'Brickoo\Cache\Manager',
                (empty($methods) ? null : array_values($methods)),
                array($this->getMock('Brickoo\Cache\Provider\FileProvider'))
            );
        }

        /**
         * Holds an instance of the CacheHandler class.
         * @var Brickoo\Http\Session\Handler\CacheHandler
         */
        protected $CacheHandler;

        /**
         * Sets the CacheHandler instance used.
         * @return void
         */
        protected function setUp()
        {
            $this->CacheHandler = new CacheHandler();
        }

        /**
         * Test if the lazy initialization returns an instance of the Manager class.
         * @covers Brickoo\Http\Session\Handler\CacheHandler::Manager
         * @covers Brickoo\Http\Session\Handler\CacheHandler::getDependency
         */
        public function testManagerLazyInitialization()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Cache\Manager',
                ($Manager = $this->CacheHandler->Manager())
            );
            $this->assertAttributeContains($Manager, 'dependencies', $this->CacheHandler);
        }

        /**
         * Test if the Manager dependency can be injected and the CacheHandler reference is returned.
         * @covers Brickoo\Http\Session\Handler\CacheHandler::Manager
         * @covers Brickoo\Http\Session\Handler\CacheHandler::getDependency
         */
        public function testInjectManager()
        {
            $ManagerStub = $this->getManagerStub();

            $this->assertSame($this->CacheHandler, $this->CacheHandler->Manager($ManagerStub));
            $this->assertAttributeContains($ManagerStub, 'dependencies', $this->CacheHandler);
        }

        /**
         * Test if the lifetime can be set and the CacheHandler reference is returned.
         * @covers Brickoo\Http\Session\Handler\CacheHandler::setLifetime
         */
        public function testSetLifetime()
        {
            $this->assertSame($this->CacheHandler, $this->CacheHandler->setLifetime(60));
            $this->assertAttributeEquals(60, 'lifetime', $this->CacheHandler);
        }

        /**
         * Test if trying to use an wrong argument type throws an exception.
         * @covers Brickoo\Http\Session\Handler\CacheHandler::setLifetime
         * @expectedException InvalidArgumentException
         */
        public function testSetLifetimeArgumentException()
        {
            $this->CacheHandler->setLifetime('wrongType');
        }

        /**
         * Test if calling the open method the Manager disables the local cache.
         * @covers Brickoo\Http\Session\Handler\CacheHandler::open
         */
        public function testOpen()
        {
            $ManagerStub = $this->getManagerStub(array('disableLocalCache'));
            $ManagerStub->expects($this->once())
                             ->method('disableLocalCache')
                             ->will($this->returnSelf());

            $this->CacheHandler->Manager($ManagerStub);
            $this->assertTrue($this->CacheHandler->open('/some/path','sessionName'));
        }

        /**
         * Test if boolean true is always returned.
         * @covers Brickoo\Http\Session\Handler\CacheHandler::close
         */
        public function testClose()
        {
            $this->assertTrue($this->CacheHandler->close());
        }

        /**
         * Test if the saved session content can be retrieved through the Manager get method.
         * @covers Brickoo\Http\Session\Handler\CacheHandler::read
         */
        public function testRead()
        {
            $valueMap = array
            (
                array(CacheHandler::SESSION_CACHE_PREFIX . 'identifier', 'session saved content')
            );

            $ManagerStub = $this->getManagerStub(array('get'));
            $ManagerStub->expects($this->once())
                             ->method('get')
                             ->will($this->returnValueMap($valueMap));

            $this->CacheHandler->Manager($ManagerStub);

            $this->assertEquals('session saved content', $this->CacheHandler->read('identifier'));
        }

        /**
         * Test if the sesssion content is saved through the Manager set method.
         * @covers Brickoo\Http\Session\Handler\CacheHandler::write
         */
        public function testWrite()
        {
            $ManagerStub = $this->getManagerStub(array('set'));
            $ManagerStub->expects($this->once())
                             ->method('set')
                             ->will($this->returnSelf());

            $this->CacheHandler->Manager($ManagerStub);

            $this->assertTrue($this->CacheHandler->write('identifier', null));
        }

        /**
         * Test if destrying a session the Manager delete method is called.
         * @covers Brickoo\Http\Session\Handler\CacheHandler::destroy
         */
        public function testDestroy()
        {
            $ManagerStub = $this->getManagerStub(array('delete'));
            $ManagerStub->expects($this->once())
                             ->method('delete')
                             ->will($this->returnSelf());

            $this->CacheHandler->Manager($ManagerStub);

            $this->assertTrue($this->CacheHandler->destroy('identifier'));
        }

        /**
         * test if the garbage collector returns always boolean true.
         * @covers Brickoo\Http\Session\Handler\CacheHandler::gc
         */
        public function testGc()
        {
            $this->assertTrue($this->CacheHandler->gc(60));
        }

    }