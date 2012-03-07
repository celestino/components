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

    use Brickoo\Cache\Manager;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Manager
     *
     * Test suite for the Manager class.
     * @see Brickoo\Cache\Manager
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ManagerTest extends \PHPUnit_Framework_TestCase
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
                'Brickoo\Cache\LocalCache',
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
                'Brickoo\Cache\Provider\Interfaces\CacheProviderInterface',
                array('get', 'set', 'delete', 'flush')
            );
        }

        /**
         * Holds the Manager instance used.
         * @var Brickoo\Cache\Manager
         */
        protected $Manager;

        /**
         * Set up the Manager object used.
         * @return void
         */
        protected function setUp()
        {
            $this->Manager = new Manager($this->getCacheProviderStub());
        }

        /**
         * Test if the CacheHander can be injected as dependency.
         * @covers Brickoo\Cache\Manager::__construct
         */
        public function testConstruct()
        {
            $CacheProviderStub = $this->getCacheProviderStub();
            $this->assertInstanceOf
            (
                'Brickoo\Cache\Interfaces\ManagerInterface',
                ($Manager = new Manager($CacheProviderStub))
            );
            $this->assertAttributeSame($CacheProviderStub, '_CacheProvider', $Manager);
        }

        /**
         * Test if the CacheHander dependency can be retrieved.
         * @covers Brickoo\Cache\Manager::CacheProvider
         */
        public function testGetCacheProvider()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Cache\Provider\Interfaces\CacheProviderInterface',
                $this->Manager->CacheProvider()
            );
        }

        /**
         * Test if the LocalCache can be injected as dependency and the Manager reference is returned.
         * @covers Brickoo\Cache\Manager::LocalCache
         * @covers Brickoo\Cache\Manager::getDependency
         */
        public function testInjectLocalCache()
        {
            $LocalCacheStub = $this->getLocalCacheStub();
            $this->assertSame($this->Manager, $this->Manager->LocalCache($LocalCacheStub));
            $this->assertAttributeContains($LocalCacheStub, 'dependencies', $this->Manager);
        }

        /**
         * Test if trying to retrieve the not available LocalCache it will be created.
         * @covers Brickoo\Cache\Manager::LocalCache
         * @covers Brickoo\Cache\Manager::getDependency
         */
        public function testGetLocalCacheLazyInitialization()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Cache\Interfaces\LocalCacheInterface',
                ($LocalCache = $this->Manager->LocalCache())
            );
            $this->assertAttributeContains($LocalCache, 'dependencies', $this->Manager);
        }

        /**
         * Test if the LocalCache is used to return the cached content.
         * @covers Brickoo\Cache\Manager::get
         */
        public function testGetWithLocalCache()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('has', 'get'));
            $LocalCacheStub->expects($this->once())
                           ->method('has')
                           ->with('some_identifier')
                           ->will($this->returnValue(true));
            $LocalCacheStub->expects($this->once())
                           ->method('get')
                           ->with('some_identifier')
                           ->will($this->returnValue('local cache content'));

            $this->Manager->LocalCache($LocalCacheStub);

            $this->assertEquals('local cache content', $this->Manager->get('some_identifier'));
        }

        /**
         * Test if the local cache be called to flush the cache.
         * @covers Brickoo\Cache\Manager::flushLocalCache
         */
        public function testFlushLocalCache()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('flush'));
            $LocalCacheStub->expects($this->once())
                           ->method('flush')
                           ->will($this->returnSelf());

            $this->Manager->LocalCache($LocalCacheStub);

            $this->assertNull($this->Manager->flushLocalCache());
        }

        /**
         * Test if the local cache can be enabled and the Manager reference is returned.
         * @covers Brickoo\Cache\Manager::enableLocalCache
         */
        public function testEnableLocalCache()
        {
            $this->assertSame($this->Manager, $this->Manager->enableLocalCache());
            $this->assertAttributeEquals(true, 'enableLocalCache', $this->Manager);
        }


        /**
         * Test if the local cache can be disabled and the Manager reference is returned.
         * @covers Brickoo\Cache\Manager::disableLocalCache
         */
        public function testDisableLocalCache()
        {
            $this->assertSame($this->Manager, $this->Manager->disableLocalCache());
            $this->assertAttributeEquals(false, 'enableLocalCache', $this->Manager);
        }

        /**
         * Test if the local cache is enabled by default.
         * @covers Brickoo\Cache\Manager::isLocalCacheEnabled
         */
        public function testIsLocalCacheEnabled()
        {
            $this->assertTrue($this->Manager->isLocalCacheEnabled());
        }

        /**
         * Test if the CacheProvider is used to return the cached content.
         * @covers Brickoo\Cache\Manager::get
         */
        public function testGetWithCacheProvider()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('has'));
            $LocalCacheStub->expects($this->once())
                           ->method('has')
                           ->will($this->returnValue(false));

            $CacheProviderStub = $this->Manager->CacheProvider();
            $CacheProviderStub->expects($this->once())
                              ->method('get')
                              ->will($this->returnValue('cache provider content'));

            $this->Manager->LocalCache($LocalCacheStub);;

            $this->assertEquals('cache provider content', $this->Manager->get('some_identifier'));
        }

        /**
         * Test if the CacheProvider is used to return the cached content with the LocalCache disabled.
         * @covers Brickoo\Cache\Manager::get
         */
        public function testGetWithCacheProviderWithoutLocalCache()
        {
            $CacheProviderStub = $this->Manager->CacheProvider();
            $CacheProviderStub->expects($this->once())
                              ->method('get')
                              ->will($this->returnValue('cache provider content'));

            $this->Manager->disableLocalCache();

            $this->assertEquals('cache provider content', $this->Manager->get('some_identifier'));
        }

        /**
         * Test is trying to retrieve a cached value with a wrong identifier type throws an exception.
         * @covers Brickoo\Cache\Manager::get
         * @expectedException InvalidArgumentException
         */
        public function testGetArgumentException()
        {
            $this->Manager->get(array('wrongType'));
        }

        /**
         * Test if adding a content to cache the LocalCache and CacheProvider are called and the
         * Manager refrence is returned.
         * @covers Brickoo\Cache\Manager::set
         */
        public function testSet()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('set'));
            $LocalCacheStub->expects($this->once())
                           ->method('set')
                           ->will($this->returnSelf());

            $CacheProviderStub = $this->Manager->CacheProvider();
            $CacheProviderStub->expects($this->once())
                              ->method('set')
                              ->will($this->returnSelf());

            $this->Manager->LocalCache($LocalCacheStub);

            $this->assertSame
            (
                $this->Manager,
                $this->Manager->set('some_identifier', array('content'), 60)
            );
        }

        /**
         * Test is trying to use a wrong identifier type throws an exception.
         * @covers Brickoo\Cache\Manager::set
         * @expectedException InvalidArgumentException
         */
        public function testSetIdentifierArgumentException()
        {
            $this->Manager->set(array('wrongType'), '', 60);
        }

        /**
         * Test is trying to use a wrong lifetime type throws an exception.
         * @covers Brickoo\Cache\Manager::set
         * @expectedException InvalidArgumentException
         */
        public function testSetLifetimeArgumentException()
        {
            $this->Manager->set('some_identifier', '', 'wrongType');
        }

        /**
         * Test if trying to delete some content the LocalCache and CacheProvider are called
         * and the Manager reference is returned.
         * @covers Brickoo\Cache\Manager::delete
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

            $CacheProviderStub = $this->Manager->CacheProvider();
            $CacheProviderStub->expects($this->once())
                             ->method('delete')
                             ->will($this->returnSelf());

            $this->Manager->LocalCache($LocalCacheStub);

            $this->assertSame($this->Manager, $this->Manager->delete('some_identifier'));
        }

        /**
         * Test is trying to use a wrong identifier type throws an exception.
         * @covers Brickoo\Cache\Manager::delete
         * @expectedException InvalidArgumentException
         */
        public function testDeleteArgumentException()
        {
            $this->Manager->delete(array('wrongType'));
        }

        /**
         * Test if trying to flush the cache the LocalCache and CacheProvider are called
         * and the Manager reference is returned.
         * @covers Brickoo\Cache\Manager::flush
         */
        public function testFlush()
        {
            $LocalCacheStub = $this->getLocalCacheStub(array('flush'));
            $LocalCacheStub->expects($this->once())
                           ->method('flush')
                           ->will($this->returnSelf());

            $CacheProviderStub = $this->Manager->CacheProvider();
            $CacheProviderStub->expects($this->once())
                             ->method('flush')
                             ->will($this->returnSelf());

            $this->Manager->LocalCache($LocalCacheStub);

            $this->assertSame($this->Manager, $this->Manager->flush());
        }

        /**
         * Test if the cache callback returns the value which is stored back to the LocalCache and CacheProvider.
         * @covers Brickoo\Cache\Manager::getByCallback
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

            $CacheProviderStub = $this->Manager->CacheProvider();
            $CacheProviderStub->expects($this->once())
                              ->method('get')
                              ->will($this->returnValue(false));
            $CacheProviderStub->expects($this->once())
                             ->method('set')
                             ->will($this->returnSelf());

            $this->Manager->LocalCache($LocalCacheStub);

            $this->assertEquals
            (
                'callback content',
                $this->Manager->getByCallback('unique_identifier', array($this, 'callback'), array(), 60)
            );
        }

        /**
         * Test is trying to use a wrong identifier type throws an exception.
         * @covers Brickoo\Cache\Manager::getByCallback
         * @expectedException InvalidArgumentException
         */
        public function testGetCacheCallbackIdentifierArgumentException()
        {
            $this->Manager->getByCallback(array('wrongType'), 'someFunction', array(), 60);
        }

        /**
         * Test is trying to use a wrong lifetime type throws an exception.
         * @covers Brickoo\Cache\Manager::getByCallback
         * @expectedException InvalidArgumentException
         */
        public function testGetCacheCallbackLifetimeArgumentException()
        {
            $this->Manager->getByCallback('some_identifier', 'someFunction', array(), 'wrongType');
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