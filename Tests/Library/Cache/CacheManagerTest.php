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

    use Brickoo\Library\Cache\CacheManager;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * CacheManager
     *
     * Test suite for the CacheManager class.
     * @see Brickoo\Library\Cache\CacheManager
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class CacheManagerTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Creates and returns a stub of the LocalCache.
         * @param array $methods the methods to mock
         * @return object LocalCache stub
         */
        public function getLocalCacheStub(array $methods = null)
        {
            return $this->getMock
            (
                'Brickoo\Library\Cache\LocalCache',
                (is_null($methods) ? null : array_values($methods))
            );
        }

        /**
        * Creates and returns a stub of the CacheHandleInterface.
        * @return object CacheHandlerInterface stub
        */
        public function getCacheHandlerStub()
        {
            return $this->getMock
            (
                'Brickoo\Library\Cache\Interfaces\CacheHandlerInterface',
                array('get', 'add', 'delete', 'flush')
            );
        }

        /**
         * Holds the CacheManager instance used.
         * @var Brickoo\Library\Cache\CacheManager
         */
        protected $CacheManager;

        /**
         * Set up the CacheManager object used.
         * @return void
         */
        protected function setUp()
        {
            $this->CacheManager = new CacheManager();
        }

        /**
         * Test if the CacheHander can be injected as dependency and the CacheManager reference is returned.
         * @covers Brickoo\Library\Cache\CacheManager::injectCacheHandler
         */
        public function testInjectCacheHandler()
        {
            $CacheHandlerStub = $this->getCacheHandlerStub();
            $this->assertSame($this->CacheManager, $this->CacheManager->injectCacheHandler($CacheHandlerStub));
            $this->assertAttributeSame($CacheHandlerStub, 'CacheHandler', $this->CacheManager);

            return $this->CacheManager;
        }

        /**
         * Test if trying to overwrite the CacheHander dependecy throws an exception.
         * @covers Brickoo\Library\Cache\CacheManager::injectCacheHandler
         * @covers Brickoo\Library\Core\Exceptions\DependencyOverwriteException::__construct
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         */
        public function testInjectCacheHandlerOverwriteException()
        {
            $CacheHandlerStub = $this->getCacheHandlerStub();
            $this->CacheManager->injectCacheHandler($CacheHandlerStub);
            $this->CacheManager->injectCacheHandler($CacheHandlerStub);
        }

        /**
         * Test if the CacheHander dependency can be retrieved.
         * @covers Brickoo\Library\Cache\CacheManager::getCacheHandler
         * @depends testInjectCacheHandler
         */
        public function testGetCacheHandler($CacheManager)
        {
            $this->assertInstanceOf
            (
                'Brickoo\Library\Cache\Interfaces\CacheHandlerInterface',
                $CacheManager->getCacheHandler()
            );
        }

        /**
         * Test if trying to retrieve the not available CacheHandler dependency throws an exception.
         * @covers Brickoo\Library\Cache\CacheManager::getCacheHandler
         * @covers Brickoo\Library\Core\Exceptions\DependencyNotAvailableException::__construct
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyNotAvailableException
         */
        public function testGetCacheHandlerMissingDependencyException()
        {
            $this->CacheManager->getCacheHandler();
        }

        /**
         * Test if the LocalCache can be injected as dependency and the CacheManager reference is returned.
         * @covers Brickoo\Library\Cache\CacheManager::injectLocalCache
         */
        public function testInjectLocalCache()
        {
            $LocalCacheStub = $this->getLocalCacheStub();
            $this->assertSame($this->CacheManager, $this->CacheManager->injectLocalCache($LocalCacheStub));
            $this->assertAttributeSame($LocalCacheStub, 'LocalCache', $this->CacheManager);

            return $this->CacheManager;
        }

        /**
         * Test if trying to overwrite the LocalCache dependecy throws an exception.
         * @covers Brickoo\Library\Cache\CacheManager::injectLocalCache
         * @covers Brickoo\Library\Core\Exceptions\DependencyOverwriteException::__construct
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         */
        public function testInjectLocalCacheOverwriteException()
        {
            $LocalCacheStub = $this->getLocalCacheStub();
            $this->CacheManager->injectLocalCache($LocalCacheStub);
            $this->CacheManager->injectLocalCache($LocalCacheStub);
        }

        /**
         * Test if the LocalCache dependency can be retrieved.
         * @covers Brickoo\Library\Cache\CacheManager::getLocalCache
         * @depends testInjectLocalCache
         */
        public function testGetLocalCache($CacheManager)
        {
            $this->assertInstanceOf
            (
                'Brickoo\Library\Cache\Interfaces\LocalCacheInterface',
                $CacheManager->getLocalCache()
            );
        }

        /**
         * Test if trying to retrieve the not available LocalCache it will be created.
         * @covers Brickoo\Library\Cache\CacheManager::getLocalCache
         */
        public function testGetLocalCacheLazyInitialization()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Library\Cache\Interfaces\LocalCacheInterface',
                $this->CacheManager->getLocalCache()
            );
        }

        /**
         * Test if the LocalCache is used to return the cached content.
         * @covers Brickoo\Library\Cache\CacheManager::get
         */
        public function testGetWithLocalCache()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('has', 'get'));
            $LocalCacheStub->expects($this->once())
                           ->method('has')
                           ->will($this->returnValue(true));
            $LocalCacheStub->expects($this->once())
                           ->method('get')
                           ->will($this->returnValue('local cache content'));

            $this->CacheManager->injectLocalCache($LocalCacheStub);

            $this->assertEquals('local cache content', $this->CacheManager->get('some_identifier'));
        }

        /**
         * Test if the CacheHandler is used to return the cached content.
         * @covers Brickoo\Library\Cache\CacheManager::get
         */
        public function testGetWithCacheHandler()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('has'));
            $LocalCacheStub->expects($this->once())
                           ->method('has')
                           ->will($this->returnValue(false));

            $CacheHandlerStub = $this->getCacheHandlerStub();
            $CacheHandlerStub->expects($this->once())
                             ->method('get')
                             ->will($this->returnValue('cache handler content'));

            $this->CacheManager->injectLocalCache($LocalCacheStub);
            $this->CacheManager->injectCacheHandler($CacheHandlerStub);

            $this->assertEquals('cache handler content', $this->CacheManager->get('some_identifier'));
        }

        /**
         * Test is trying to retrieve a cached value with a wrong identifier type throws an exception.
         * @covers Brickoo\Library\Cache\CacheManager::get
         * @expectedException InvalidArgumentException
         */
        public function testGetArgumentException()
        {
            $this->CacheManager->get(array('wrongType'));
        }

        /**
         * Test if adding a content to cache the LocalCache and CacheHandler are called and the
         * CacheManager refrence is returned.
         * @covers Brickoo\Library\Cache\CacheManager::add
         */
        public function testAdd()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('add'));
            $LocalCacheStub->expects($this->once())
                           ->method('add')
                           ->will($this->returnSelf());

            $CacheHandlerStub = $this->getCacheHandlerStub();
            $CacheHandlerStub->expects($this->once())
                             ->method('add')
                             ->will($this->returnSelf());

            $this->CacheManager->injectLocalCache($LocalCacheStub);
            $this->CacheManager->injectCacheHandler($CacheHandlerStub);

            $this->assertSame
            (
                $this->CacheManager,
                $this->CacheManager->add('some_identifier', array('content'), 60)
            );
        }

        /**
         * Test is trying to use a wrong identifier type throws an exception.
         * @covers Brickoo\Library\Cache\CacheManager::add
         * @expectedException InvalidArgumentException
         */
        public function testAddIdentifierArgumentException()
        {
            $this->CacheManager->add(array('wrongType'), '', 60);
        }

        /**
         * Test is trying to use a wrong lifetime type throws an exception.
         * @covers Brickoo\Library\Cache\CacheManager::add
         * @expectedException InvalidArgumentException
         */
        public function testAddLifetimeArgumentException()
        {
            $this->CacheManager->add('some_identifier', '', 'wrongType');
        }

        /**
         * Test if trying to delete some content the LocalCache and CacheHandler are called
         * and the CacheManager reference is returned.
         * @covers Brickoo\Library\Cache\CacheManager::delete
         */
        public function testDelete()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('has', 'remove'));
            $LocalCacheStub->expects($this->once())
                           ->method('has')
                           ->will($this->returnValue(true));
            $LocalCacheStub->expects($this->once())
                           ->method('remove')
                           ->will($this->returnSelf());

            $CacheHandlerStub = $this->getCacheHandlerStub();
            $CacheHandlerStub->expects($this->once())
                             ->method('delete')
                             ->will($this->returnSelf());

            $this->CacheManager->injectLocalCache($LocalCacheStub);
            $this->CacheManager->injectCacheHandler($CacheHandlerStub);

            $this->assertSame($this->CacheManager, $this->CacheManager->delete('some_identifier'));
        }

        /**
         * Test is trying to use a wrong identifier type throws an exception.
         * @covers Brickoo\Library\Cache\CacheManager::delete
         * @expectedException InvalidArgumentException
         */
        public function testDeleteArgumentException()
        {
            $this->CacheManager->delete(array('wrongType'));
        }

        /**
         * Test if trying to flush the cache the LocalCache and CacheHandler are called
         * and the CacheManager reference is returned.
         * @covers Brickoo\Library\Cache\CacheManager::flush
         */
        public function testFlush()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('flush'));
            $LocalCacheStub->expects($this->once())
                           ->method('flush')
                           ->will($this->returnSelf());

            $CacheHandlerStub = $this->getCacheHandlerStub();
            $CacheHandlerStub->expects($this->once())
                             ->method('flush')
                             ->will($this->returnSelf());

            $this->CacheManager->injectLocalCache($LocalCacheStub);
            $this->CacheManager->injectCacheHandler($CacheHandlerStub);

            $this->assertSame($this->CacheManager, $this->CacheManager->flush());
        }

        /**
         * Test if the cache callback returns the value which is stored back to the LocalCache and CacheHandler.
         * @covers Brickoo\Library\Cache\CacheManager::getCacheCallback
         */
        public function testGetCacheCallback()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('has', 'add'));
            $LocalCacheStub->expects($this->once())
                           ->method('has')
                           ->will($this->returnValue(false));
            $LocalCacheStub->expects($this->once())
                           ->method('add')
                           ->will($this->returnSelf());

            $CacheHandlerStub = $this->getCacheHandlerStub();
            $CacheHandlerStub->expects($this->once())
                             ->method('get')
                             ->will($this->returnValue(false));
            $CacheHandlerStub->expects($this->once())
                             ->method('add')
                             ->will($this->returnSelf());

            $this->CacheManager->injectCacheHandler($CacheHandlerStub);
            $this->CacheManager->injectLocalCache($LocalCacheStub);

            $this->assertEquals
            (
                'callback content',
                $this->CacheManager->getCacheCallback('unique_identifier', array($this, 'callback'), array(), 60)
            );
        }

        /**
         * Test is trying to use a wrong identifier type throws an exception.
         * @covers Brickoo\Library\Cache\CacheManager::getCacheCallback
         * @expectedException InvalidArgumentException
         */
        public function testGetCacheCallbackIdentifierArgumentException()
        {
            $this->CacheManager->add(array('wrongType'), 'someFunction', array(), 60);
        }

        /**
         * Test is trying to use a wrong lifetime type throws an exception.
         * @covers Brickoo\Library\Cache\CacheManager::getCacheCallback
         * @expectedException InvalidArgumentException
         */
        public function testGetCacheCallbackLifetimeArgumentException()
        {
            $this->CacheManager->add('some_identifier', 'someFunction', array(), 'wrongType');
        }

        /**
         * Helper method for the testGetCacheCallback.
         * @returns string the callback response
         */
        public function callback()
        {
            return 'callback content';
        }

    }

?>
