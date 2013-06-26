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

    namespace Tests\Brickoo\Cache;

    use Brickoo\Cache\Manager;

    /**
     * Manager
     *
     * Test suite for the Manager class.
     * @see Brickoo\Cache\Manager
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ManagerTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Cache\Manager::__construct
         */
        public function testConstructorAssignsTheProperties() {
            $ProviderPool = $this->getMock('Brickoo\Cache\Interfaces\ProviderPool');
            $CacheManager = new Manager($ProviderPool);
            $this->assertAttributeEquals(null, "Provider", $CacheManager);
            $this->assertAttributeSame($ProviderPool, "ProviderPool", $CacheManager);
        }

        /**
         * @covers Brickoo\Cache\Manager::getProvider
         * @covers Brickoo\Cache\Exceptions\ProviderNotFound
         * @expectedException Brickoo\Cache\Exceptions\ProviderNotFound
         */
        public function testNotFoundProviderThrowsAnException() {
            $ProviderPool = $this->getMock('Brickoo\Cache\Interfaces\ProviderPool');
            $ProviderPool->expects($this->once())
                         ->method("isEmpty")
                         ->will($this->returnValue(true));

            $CacheManager = new Manager($ProviderPool);
            $CacheManager->get("SomeIdentifier");
        }

        /**
         * @covers Brickoo\Cache\Manager::getProvider
         * @covers Brickoo\Cache\Exceptions\ProviderNotReady
         * @expectedException Brickoo\Cache\Exceptions\ProviderNotReady
         */
        public function testProviderNotReadyThrowsAnException() {
            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $Provider->expects($this->once())
                     ->method("isReady")
                     ->will($this->returnValue(false));

            $CacheManager = new Manager($this->buildProviderPoolMock($Provider));
            $CacheManager->get("SomeIdentifier");
        }

        /**
         * @covers Brickoo\Cache\Manager::get
         * @covers Brickoo\Cache\Manager::getProvider
         */
        public function testGetCachedContent() {
            $cacheIdentifier = "someIdentifier";
            $cachedContent = "some cached content";

            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $Provider->expects($this->once())
                     ->method("get")
                     ->with($cacheIdentifier)
                     ->will($this->returnValue($cachedContent));

            $CacheManager = new Manager($this->buildProviderPoolMock($Provider));
            $this->assertEquals($cachedContent, $CacheManager->get($cacheIdentifier));
        }

        /**
         * @covers Brickoo\Cache\Manager::get
         * @expectedException InvalidArgumentException
         */
        public function testGetIdentifierThrowsArgumentException() {
            $ProviderPool = $this->getMock('Brickoo\Cache\Interfaces\ProviderPool');
            $CacheManager = new Manager($ProviderPool);
            $CacheManager->get(array('wrongType'));
        }

        /**
         * @covers Brickoo\Cache\Manager::set
         * @covers Brickoo\Cache\Manager::getProvider
         */
        public function testStoringContentToCache() {
            $cacheIdentifier = "someIdentifier";
            $cacheContent = "some content ot cache";
            $lifetime = 60;

            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $Provider->expects($this->once())
                     ->method("set")
                     ->with($cacheIdentifier, $cacheContent, $lifetime);

            $CacheManager = new Manager($this->buildProviderPoolMock($Provider));
            $this->assertSame($CacheManager, $CacheManager->set($cacheIdentifier, $cacheContent, $lifetime));
        }

        /**
         * @covers Brickoo\Cache\Manager::set
         * @expectedException InvalidArgumentException
         */
        public function testSetIdentifierThrowsArgumentException() {
            $ProviderPool = $this->getMock('Brickoo\Cache\Interfaces\ProviderPool');
            $CacheManager = new Manager($ProviderPool);
            $CacheManager->set(array('wrongType'), '', 60);
        }

        /**
         * @covers Brickoo\Cache\Manager::set
         * @expectedException InvalidArgumentException
         */
        public function testSetLifetimeThrowsArgumentException() {
            $ProviderPool = $this->getMock('Brickoo\Cache\Interfaces\ProviderPool');
            $CacheManager = new Manager($ProviderPool);
            $CacheManager->set('some_identifier', '', 'wrongType');
        }

        /**
         * @covers Brickoo\Cache\Manager::delete
         */
        public function testDeleteCachedContent() {
            $cacheIdentifier = "someIdentifier";

            $ProviderPool = $this->buildProviderPool($cacheIdentifier, "delete", "apc");
            $CacheManager = new Manager($ProviderPool);
            $this->assertEquals("apc", $ProviderPool->key());
            $this->assertSame($CacheManager, $CacheManager->delete($cacheIdentifier));
            $this->assertEquals("apc", $ProviderPool->key());
        }

        /**
         * @covers Brickoo\Cache\Manager::delete
         * @expectedException InvalidArgumentException
         */
        public function testDeleteIdentifierThrowsArgumentException() {
            $ProviderPool = $this->getMock('Brickoo\Cache\Interfaces\ProviderPool');
            $CacheManager = new Manager($ProviderPool);
            $CacheManager->delete(array('wrongType'));
        }

        /**
         * @covers Brickoo\Cache\Manager::flush
         */
        public function testFlushCachedContent() {
            $ProviderPool = $this->buildProviderPool(null, "flush", "apc");
            $CacheManager = new Manager($ProviderPool);
            $this->assertEquals("apc", $ProviderPool->key());
            $this->assertSame($CacheManager, $CacheManager->flush());
            $this->assertEquals("apc", $ProviderPool->key());
        }

        /**
         * @covers Brickoo\Cache\Manager::getByCallback
         */
        public function testGetByCallbackFallbackFromProviderPool() {
            $cacheIdentifier = "someIdentifier";
            $callback = array($this, "callbackGetCachedContent");
            $callbackArguments = array();
            $lifetime = 60;

            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $Provider->expects($this->once())
                     ->method("get")
                     ->will($this->returnValue(null));
            $Provider->expects($this->once())
                     ->method("set")
                     ->with($cacheIdentifier, $this->callbackGetCachedContent(), $lifetime);

            $CacheManager = new Manager($this->buildProviderPoolMock($Provider));
            $this->assertEquals(
                $this->callbackGetCachedContent(),
                $CacheManager->getByCallback($cacheIdentifier, $callback, $callbackArguments, $lifetime)
            );
        }

        /**
         * @covers Brickoo\Cache\Manager::getByCallback
         * @expectedException InvalidArgumentException
         */
        public function testGetByCallbackIdentifierThrowsInvalidArgumentException() {
            $ProviderPool = $this->getMock('Brickoo\Cache\Interfaces\ProviderPool');
            $CacheManager = new Manager($ProviderPool);
            $CacheManager->getByCallback(array('wrongType'), "someFunction", array(), 60);
        }

        /**
         * @covers Brickoo\Cache\Manager::getByCallback
         * @expectedException InvalidArgumentException
         */
        public function testGetByCallbackCallableThrowsInvalidArgumentException() {
            $ProviderPool = $this->getMock('Brickoo\Cache\Interfaces\ProviderPool');
            $CacheManager = new Manager($ProviderPool);
            $CacheManager->getByCallback("some_identifier", "this.is.not.callable", array(), 60);
        }

        /**
         * @covers Brickoo\Cache\Manager::getByCallback
         * @expectedException InvalidArgumentException
         */
        public function testGetByCallbackLifetimeThrowsInvalidArgumentException() {
            $ProviderPool = $this->getMock('Brickoo\Cache\Interfaces\ProviderPool');
            $CacheManager = new Manager($ProviderPool);
            $CacheManager->getByCallback("some_identifier", array($this, "callbackGetCachedContent"), array(), "wrongType");
        }

        /**
         * Helper method for the testGetCacheCallback.
         * @return string the callback response
         */
        public function callbackGetCachedContent() {
            return "callback content";
        }

        /**
         * Helper method for the testGetFallbackForTheCallback.
         * @return null
         */
        public function callbackNotFound() {
            return;
        }

        /**
         * Returns a pre-configured ProviderPool object.
         * @param string $cacheIdentifier
         * @param string $calledMethod has to be delete or flush
         * @param string $selectedProvider has to be memcache or apc
         * @return \Brickoo\Cache\Interfaces\ProviderPool
         */
        private function buildProviderPool($cacheIdentifier, $calledMethod, $selectedProvider) {
            if ((! in_array($calledMethod, array("delete", "flush")))
                || (! in_array($selectedProvider, array("memcache", "apc")))
            ){
                throw new \InvalidArgumentException("Invalid arguments provider to buildProviderPool method.");
            }

            $Memcache = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $builder = $Memcache->expects($this->once())->method($calledMethod);
            if ($cacheIdentifier !== null) {
                $builder = $builder->with($cacheIdentifier);
            }
            $builder->will($this->returnSelf());

            $APC = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $builder = $APC->expects($this->once())->method($calledMethod);
            if ($cacheIdentifier !== null) {
                $builder = $builder->with($cacheIdentifier);
            }
            $builder->will($this->returnSelf());

            $ProviderPool = new \Brickoo\Cache\ProviderPool(array(
                "memcache" => $Memcache,
                "apc" => $APC,
            ));
            $ProviderPool->select($selectedProvider);
            return $ProviderPool;
        }

        /**
         * Returns a pre-configured ProviderPool mock object.
         * @param \Brickoo\Cache\Provider\Interfaces\Provider $Provider
         * @param string $poolEntryKey the pool entry key
         * @return \Brickoo\Cache\Interfaces\ProviderPool
         */
        private function buildProviderPoolMock(\Brickoo\Cache\Provider\Interfaces\Provider $Provider = null, $poolEntryKey = 0) {
            if ($Provider !== null) {
                $Provider->expects($this->any())
                         ->method("isReady")
                         ->will($this->returnValue(true));
            }

            $ProviderPool = $this->getMock('Brickoo\Cache\Interfaces\ProviderPool');
            $ProviderPool->expects($this->once())
                         ->method("isEmpty")
                         ->will($this->returnValue(false));
            $ProviderPool->expects($this->once())
                         ->method("rewind");
            $ProviderPool->expects($this->any())
                         ->method("valid")
                         ->will($this->onConsecutiveCalls(($Provider !== null), false));
            $ProviderPool->expects($this->any())
                         ->method("next");
            $ProviderPool->expects($this->any())
                         ->method("current")
                         ->will($this->returnValue($Provider));
            $ProviderPool->expects($this->any())
                         ->method("key")
                         ->will($this->returnValue($poolEntryKey));

            return $ProviderPool;
        }

    }