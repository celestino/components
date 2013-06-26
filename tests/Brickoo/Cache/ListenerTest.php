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

    use Brickoo\Cache\Listener;

    /**
     * ListenerTest
     *
     * Test suite for the Listener class.
     * @see Brickoo\Cache\Listener
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ListenerTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Cache\Listener::__construct
         */
        public function testConstructor() {
            $CacheManager = $this->getMock('Brickoo\Cache\Interfaces\Manager');
            $Listener = new Listener($CacheManager, 222);
            $this->assertAttributeSame($CacheManager, 'CacheManager', $Listener);
            $this->assertAttributeEquals(222, 'listenerPriority', $Listener);
        }

        /**
         * @covers Brickoo\Cache\Listener::attachListeners
         * @covers Brickoo\Cache\Events
         */
        public function testAttachListeners() {
            $priority = 111;
            $CacheManager = $this->getMock('Brickoo\Cache\Interfaces\Manager');

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $EventManager->expects($this->any())
                         ->method('attach')
                         ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Listener'))
                         ->will($this->returnSelf());

            $Listener = new Listener($CacheManager, $priority);
            $this->assertAttributeSame($CacheManager, 'CacheManager', $Listener);
            $this->assertAttributeEquals($priority, 'listenerPriority', $Listener);
            $this->assertNull($Listener->attachListeners($EventManager));
        }

        /**
         * @covers Brickoo\Cache\Listener::handleCacheEventGet
         */
        public function testHandleCacheEventGet() {
            $cacheIdentifier = 'test';
            $cachedResponse  = 'cached test response';

            $CacheManager = $this->getMock('Brickoo\Cache\Interfaces\Manager');
            $CacheManager->expects($this->once())
                         ->method('get')
                         ->with($cacheIdentifier)
                         ->will($this->returnValue($cachedResponse));

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $Event = new \Brickoo\Event\Event('test.event', null, array('id' => $cacheIdentifier));

            $Listener = new Listener($CacheManager);
            $this->assertEquals($cachedResponse, $Listener->handleCacheEventGet($Event, $EventManager));
        }

        /**
         * @coves Brickoo\Cache\Listener::handleCacheEventGet
         */
        public function testHandleCacheEventGetReturnsNull() {
            $CacheManager = $this->getMock('Brickoo\Cache\Interfaces\Manager');
            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $Event = new \Brickoo\Event\Event('test.event', null, array('missed identifier'));

            $Listener = new Listener($CacheManager);
            $this->assertNull($Listener->handleCacheEventGet($Event, $EventManager));
        }

        /**
         * @covers Brickoo\Cache\Listener::handleCacheEventGetByCallback
         */
        public function testHandleCacheEventGetByCallback() {
            $cacheIdentifier = "test";
            $cacheCallback = function(){ /*do nothing */ };
            $callbackArguments = array();
            $lifetime = 60;
            $cachedResponse = "cached test callback response";

            $CacheManager = $this->getMock('Brickoo\Cache\Interfaces\Manager');
            $CacheManager->expects($this->once())
                         ->method("getByCallback")
                         ->with($cacheIdentifier, $cacheCallback, $callbackArguments, $lifetime)
                         ->will($this->returnValue($cachedResponse));

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $Event = new \Brickoo\Event\Event(
                "test.event",
                null,
                array(
                    "id" => $cacheIdentifier,
                    "callback" => $cacheCallback,
                    "callbackArguments" => $callbackArguments,
                    "lifetime" => $lifetime
                )
            );

            $Listener = new Listener($CacheManager);
            $this->assertEquals($cachedResponse, $Listener->handleCacheEventGetByCallback($Event, $EventManager));
        }

        /**
         * @covers Brickoo\Cache\Listener::handleCacheEventGetByCallback
         */
        public function testHandleCacheEventGetByCallbackReturnsNull() {
            $CacheManager = $this->getMock('Brickoo\Cache\Interfaces\Manager');
            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $Event = new \Brickoo\Event\Event('test.event', null, array('missed identifier, callback andcallback arguments'));

            $Listener = new Listener($CacheManager);
            $this->assertNull($Listener->handleCacheEventGetByCallback($Event, $EventManager));
        }

        /**
         * @covers Brickoo\Cache\Listener::handleCacheEventSet
         */
        public function testHandleCacheEventSet() {
            $cacheIdentifier      = 'test';
            $content              = 'content to cache';
            $cacheLifetime        = 60;

            $CacheManager = $this->getMock('Brickoo\Cache\Interfaces\Manager');
            $CacheManager->expects($this->once())
                         ->method('set')
                         ->with($cacheIdentifier, $content, $cacheLifetime)
                         ->will($this->returnSelf());

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $Event = new \Brickoo\Event\Event(
                'test.event',
                null,
                array('id' => $cacheIdentifier, 'content' => $content, 'lifetime' => $cacheLifetime)
            );

            $Listener = new Listener($CacheManager);
            $this->assertNull($Listener->handleCacheEventSet($Event, $EventManager));
        }

        /**
         * @covers Brickoo\Cache\Listener::handleCacheEventDelete
         */
        public function testHandleCacheEventDelete() {
            $cacheIdentifier = 'test';

            $CacheManager = $this->getMock('Brickoo\Cache\Interfaces\Manager');
            $CacheManager->expects($this->once())
                         ->method('delete')
                         ->with($cacheIdentifier)
                         ->will($this->returnSelf());

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $Event = new \Brickoo\Event\Event('test.event', null, array('id' => $cacheIdentifier));

            $Listener = new Listener($CacheManager);
            $this->assertNull($Listener->handleCacheEventDelete($Event, $EventManager));
        }

        /**
         * @covers Brickoo\Cache\Listener::handleCacheEventFLush
         */
        public function testHandleCacheEventFlush() {
            $CacheManager = $this->getMock('Brickoo\Cache\Interfaces\Manager');
            $CacheManager->expects($this->once())
                         ->method('flush')
                         ->will($this->returnSelf());

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $Event = new \Brickoo\Event\Event('test.event');

            $Listener = new Listener($CacheManager);
            $this->assertNull($Listener->handleCacheEventFlush($Event, $EventManager));
        }

    }