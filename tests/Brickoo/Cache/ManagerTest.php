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
            $this->assertAttributeSame($ProviderPool, "ProviderPool", $CacheManager);
        }

        /**
         * @covers Brickoo\Cache\Manager::get
         * @covers Brickoo\Cache\Manager::getCurrentProvider
         */
        public function testGetCachedContent() {
            $cacheIdentifier = "someIdentifier";
            $cachedContent = "some cached content";

            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $Provider->expects($this->once())
                     ->method("get")
                     ->with($cacheIdentifier)
                     ->will($this->returnValue($cachedContent));

            $ProviderPool = $this->getMock('Brickoo\Cache\Interfaces\ProviderPool');
            $ProviderPool->expects($this->once())
                         ->method("current")
                         ->will($this->returnValue($Provider));

            $CacheManager = new Manager($ProviderPool);
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
         * @covers Brickoo\Cache\Manager::getCurrentProvider
         */
        public function testStoringContentToCache() {
            $cacheIdentifier = "someIdentifier";
            $cacheContent = "some content ot cache";
            $lifetime = 60;

            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $Provider->expects($this->once())
                     ->method("set")
                     ->with($cacheIdentifier, $cacheContent, $lifetime);

            $ProviderPool = $this->getMock('Brickoo\Cache\Interfaces\ProviderPool');
            $ProviderPool->expects($this->once())
                         ->method("current")
                         ->will($this->returnValue($Provider));

            $CacheManager = new Manager($ProviderPool);
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

            $Memcache = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $Memcache->expects($this->once())
                     ->method("delete")
                     ->with($cacheIdentifier);

            $APC = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $APC->expects($this->once())
                ->method("delete")
                ->with($cacheIdentifier);

            $ProviderPool = new \Brickoo\Cache\ProviderPool(array(
                "memcache" => $Memcache,
                "apc" => $APC,
            ));
            $ProviderPool->select("apc");

            $CacheManager = new Manager($ProviderPool);
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
            $cacheIdentifier = "someIdentifier";

            $Memcache = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $Memcache->expects($this->once())
                     ->method("flush")
                     ->will($this->returnSelf());

            $APC = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $APC->expects($this->once())
                ->method("flush")
                ->will($this->returnSelf());

            $ProviderPool = new \Brickoo\Cache\ProviderPool(array(
                "memcache" => $Memcache,
                "apc" => $APC,
            ));
            $ProviderPool->select("apc");

            $CacheManager = new Manager($ProviderPool);
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

            $ProviderPool = $this->getMock('Brickoo\Cache\Interfaces\ProviderPool');
            $ProviderPool->expects($this->any())
                         ->method("current")
                         ->will($this->returnValue($Provider));

            $CacheManager = new Manager($ProviderPool);
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

    }