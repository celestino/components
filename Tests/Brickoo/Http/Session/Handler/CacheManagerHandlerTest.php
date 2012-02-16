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

    use Brickoo\Http\Session\Handler\CacheManagerHandler;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * CacheManagerHandlerTest
     *
     * Test suite for the CacheManagerHandler class.
     * @see Brickoo\Http\Session\Handler\CacheManagerHandler
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class CacheManagerHandlerTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Returns a CacheManager stub implementig the Cache\Interfaces\CacheManagerInterface.
         * @param array $methods the methods to mock
         * @return object
         */
        public function getCacheManagerStub(array $methods = null)
        {
            return $this->getMock
            (
                'Brickoo\Cache\CacheManager',
                (empty($methods) ? null : array_values($methods)),
                array($this->getMock('Brickoo\Cache\Provider\FileProvider'))
            );
        }

        /**
         * Holds an instance of the CacheManagerHandler class.
         * @var Brickoo\Http\Session\Handler\CacheManagerHandler
         */
        protected $CacheManagerHandler;

        /**
         * Sets the CacheManagerHandler instance used.
         * @return void
         */
        protected function setUp()
        {
            $this->CacheManagerHandler = new CacheManagerHandler();
        }

        /**
         * Test if the lazy initialization returns an instance of the CacheManager class.
         * @covers Brickoo\Http\Session\Handler\CacheManagerHandler::CacheManager
         * @covers Brickoo\Http\Session\Handler\CacheManagerHandler::getDependency
         */
        public function testCacheManagerLazyInitialization()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Cache\CacheManager',
                ($CacheManager = $this->CacheManagerHandler->CacheManager())
            );
            $this->assertAttributeContains($CacheManager, 'dependencies', $this->CacheManagerHandler);
        }

        /**
         * Test if the CacheManager dependency can be injected and the CacheManagerHandler reference is returned.
         * @covers Brickoo\Http\Session\Handler\CacheManagerHandler::CacheManager
         * @covers Brickoo\Http\Session\Handler\CacheManagerHandler::getDependency
         */
        public function testInjectCacheManager()
        {
            $CacheManagerStub = $this->getCacheManagerStub();

            $this->assertSame($this->CacheManagerHandler, $this->CacheManagerHandler->CacheManager($CacheManagerStub));
            $this->assertAttributeContains($CacheManagerStub, 'dependencies', $this->CacheManagerHandler);
        }

        /**
         * Test if the lifetime can be set and the CacheManagerHandler reference is returned.
         * @covers Brickoo\Http\Session\Handler\CacheManagerHandler::setLifetime
         */
        public function testSetLifetime()
        {
            $this->assertSame($this->CacheManagerHandler, $this->CacheManagerHandler->setLifetime(60));
            $this->assertAttributeEquals(60, 'lifetime', $this->CacheManagerHandler);
        }

        /**
         * Test if trying to use an wrong argument type throws an exception.
         * @covers Brickoo\Http\Session\Handler\CacheManagerHandler::setLifetime
         * @expectedException InvalidArgumentException
         */
        public function testSetLifetimeArgumentException()
        {
            $this->CacheManagerHandler->setLifetime('wrongType');
        }

        /**
         * Test if calling the open method the CacheManager disables the local cache.
         * @covers Brickoo\Http\Session\Handler\CacheManagerHandler::open
         */
        public function testOpen()
        {
            $CacheManagerStub = $this->getCacheManagerStub(array('disableLocalCache'));
            $CacheManagerStub->expects($this->once())
                             ->method('disableLocalCache')
                             ->will($this->returnSelf());

            $this->CacheManagerHandler->CacheManager($CacheManagerStub);
            $this->assertTrue($this->CacheManagerHandler->open('/some/path','sessionName'));
        }

        /**
         * Test if boolean true is always returned.
         * @covers Brickoo\Http\Session\Handler\CacheManagerHandler::close
         */
        public function testClose()
        {
            $this->assertTrue($this->CacheManagerHandler->close());
        }

        /**
         * Test if the saved session content can be retrieved through the CacheManager get method.
         * @covers Brickoo\Http\Session\Handler\CacheManagerHandler::read
         */
        public function testRead()
        {
            $valueMap = array
            (
                array(CacheManagerHandler::SESSION_CACHE_PREFIX . 'identifier', 'session saved content')
            );

            $CacheManagerStub = $this->getCacheManagerStub(array('get'));
            $CacheManagerStub->expects($this->once())
                             ->method('get')
                             ->will($this->returnValueMap($valueMap));

            $this->CacheManagerHandler->CacheManager($CacheManagerStub);

            $this->assertEquals('session saved content', $this->CacheManagerHandler->read('identifier'));
        }

        /**
         * Test if the sesssion content is saved through the CacheManager set method.
         * @covers Brickoo\Http\Session\Handler\CacheManagerHandler::write
         */
        public function testWrite()
        {
            $CacheManagerStub = $this->getCacheManagerStub(array('set'));
            $CacheManagerStub->expects($this->once())
                             ->method('set')
                             ->will($this->returnSelf());

            $this->CacheManagerHandler->CacheManager($CacheManagerStub);

            $this->assertTrue($this->CacheManagerHandler->write('identifier', null));
        }

        /**
         * Test if destrying a session the CacheManager delete method is called.
         * @covers Brickoo\Http\Session\Handler\CacheManagerHandler::destroy
         */
        public function testDestroy()
        {
            $CacheManagerStub = $this->getCacheManagerStub(array('delete'));
            $CacheManagerStub->expects($this->once())
                             ->method('delete')
                             ->will($this->returnSelf());

            $this->CacheManagerHandler->CacheManager($CacheManagerStub);

            $this->assertTrue($this->CacheManagerHandler->destroy('identifier'));
        }

        /**
         * test if the garbage collector returns always boolean true.
         * @covers Brickoo\Http\Session\Handler\CacheManagerHandler::gc
         */
        public function testGc()
        {
            $this->assertTrue($this->CacheManagerHandler->gc(60));
        }

    }