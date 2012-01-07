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
        * @return object CacheProviderInterface stub
        */
        public function getCacheProviderStub()
        {
            return $this->getMock
            (
                'Brickoo\Library\Cache\Interfaces\CacheProviderInterface',
                array('get', 'set', 'delete', 'flush')
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
            $this->CacheManager = new CacheManager($this->getCacheProviderStub());
        }

        /**
         * Test if the CacheHander can be injected as dependency.
         * @covers Brickoo\Library\Cache\CacheManager::__construct
         */
        public function testConstruct()
        {
            $CacheProviderStub = $this->getCacheProviderStub();
            $this->assertInstanceOf
            (
                'Brickoo\Library\Cache\Interfaces\CacheManagerInterface',
                ($CacheManager = new CacheManager($CacheProviderStub))
            );
            $this->assertAttributeSame($CacheProviderStub, 'CacheProvider', $CacheManager);
        }

        /**
         * Test if the CacheHander dependency can be retrieved.
         * @covers Brickoo\Library\Cache\CacheManager::getCacheProvider
         */
        public function testGetCacheProvider()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Library\Cache\Interfaces\CacheProviderInterface',
                $this->CacheManager->getCacheProvider()
            );
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
         * Test if the CacheProvider is used to return the cached content.
         * @covers Brickoo\Library\Cache\CacheManager::get
         */
        public function testGetWithCacheProvider()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('has'));
            $LocalCacheStub->expects($this->once())
                           ->method('has')
                           ->will($this->returnValue(false));

            $CacheProviderStub = $this->CacheManager->getCacheProvider();
            $CacheProviderStub->expects($this->once())
                              ->method('get')
                              ->will($this->returnValue('cache provider content'));

            $this->CacheManager->injectLocalCache($LocalCacheStub);;

            $this->assertEquals('cache provider content', $this->CacheManager->get('some_identifier'));
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
         * Test if adding a content to cache the LocalCache and CacheProvider are called and the
         * CacheManager refrence is returned.
         * @covers Brickoo\Library\Cache\CacheManager::set
         */
        public function testSet()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('set'));
            $LocalCacheStub->expects($this->once())
                           ->method('set')
                           ->will($this->returnSelf());

            $CacheProviderStub = $this->CacheManager->getCacheProvider();
            $CacheProviderStub->expects($this->once())
                              ->method('set')
                              ->will($this->returnSelf());

            $this->CacheManager->injectLocalCache($LocalCacheStub);

            $this->assertSame
            (
                $this->CacheManager,
                $this->CacheManager->set('some_identifier', array('content'), 60)
            );
        }

        /**
         * Test is trying to use a wrong identifier type throws an exception.
         * @covers Brickoo\Library\Cache\CacheManager::set
         * @expectedException InvalidArgumentException
         */
        public function testSetIdentifierArgumentException()
        {
            $this->CacheManager->set(array('wrongType'), '', 60);
        }

        /**
         * Test is trying to use a wrong lifetime type throws an exception.
         * @covers Brickoo\Library\Cache\CacheManager::set
         * @expectedException InvalidArgumentException
         */
        public function testSetLifetimeArgumentException()
        {
            $this->CacheManager->set('some_identifier', '', 'wrongType');
        }

        /**
         * Test if trying to delete some content the LocalCache and CacheProvider are called
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

            $CacheProviderStub = $this->CacheManager->getCacheProvider();
            $CacheProviderStub->expects($this->once())
                             ->method('delete')
                             ->will($this->returnSelf());

            $this->CacheManager->injectLocalCache($LocalCacheStub);

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
         * Test if trying to flush the cache the LocalCache and CacheProvider are called
         * and the CacheManager reference is returned.
         * @covers Brickoo\Library\Cache\CacheManager::flush
         */
        public function testFlush()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('flush'));
            $LocalCacheStub->expects($this->once())
                           ->method('flush')
                           ->will($this->returnSelf());

            $CacheProviderStub = $this->CacheManager->getCacheProvider();
            $CacheProviderStub->expects($this->once())
                             ->method('flush')
                             ->will($this->returnSelf());

            $this->CacheManager->injectLocalCache($LocalCacheStub);

            $this->assertSame($this->CacheManager, $this->CacheManager->flush());
        }

        /**
         * Test if the cache callback returns the value which is stored back to the LocalCache and CacheProvider.
         * @covers Brickoo\Library\Cache\CacheManager::getCacheCallback
         */
        public function testGetCacheCallback()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('has', 'set'));
            $LocalCacheStub->expects($this->once())
                           ->method('has')
                           ->will($this->returnValue(false));
            $LocalCacheStub->expects($this->once())
                           ->method('set')
                           ->will($this->returnSelf());

            $CacheProviderStub = $this->CacheManager->getCacheProvider();
            $CacheProviderStub->expects($this->once())
                              ->method('get')
                              ->will($this->returnValue(false));
            $CacheProviderStub->expects($this->once())
                             ->method('set')
                             ->will($this->returnSelf());

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
            $this->CacheManager->getCacheCallback(array('wrongType'), 'someFunction', array(), 60);
        }

        /**
         * Test is trying to use a wrong lifetime type throws an exception.
         * @covers Brickoo\Library\Cache\CacheManager::getCacheCallback
         * @expectedException InvalidArgumentException
         */
        public function testGetCacheCallbackLifetimeArgumentException()
        {
            $this->CacheManager->getCacheCallback('some_identifier', 'someFunction', array(), 'wrongType');
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
