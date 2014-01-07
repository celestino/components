<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
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

namespace Brickoo\Tests\Cache;

use Brickoo\Cache\CacheEventListener,
    Brickoo\Cache\Events,
    Brickoo\Cache\Event\CacheEvent,
    Brickoo\Cache\Event\DeleteEvent,
    Brickoo\Cache\Event\FlushEvent,
    Brickoo\Cache\Event\RetrieveByCallbackEvent,
    Brickoo\Cache\Event\RetrieveEvent,
    Brickoo\Cache\Event\StoreEvent,
    Brickoo\Event\GenericEvent,
    PHPUnit_Framework_TestCase;

/**
 * CacheEventListenerTest
 *
 * Test suite for the CacheEventListener class.
 * @see Brickoo\Cache\CacheEventListener
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class CacheEventListenerTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Cache\CacheEventListener::__construct */
    public function testConstructtorInitializesProperties() {
        $cacheProxy = $this->getCacheProxyStub();
        $listenerPriority = 100;
        $cacheEventListener = new CacheEventListener($cacheProxy, $listenerPriority);
        $this->assertInstanceOf("\Brickoo\Event\ListenerAggregate", $cacheEventListener);
        $this->assertAttributeSame($cacheProxy, "cacheProxy", $cacheEventListener);
        $this->assertAttributeEquals($listenerPriority, "listenerPriority", $cacheEventListener);
    }

    /** @covers Brickoo\Cache\CacheEventListener::attachListeners */
    public function testAttachCacheEventListenersToEventDispatcher() {
        $eventDispatcher = $this->getEventDispatcherStub();
        $eventDispatcher->expects($this->exactly(5))
                        ->method("attach")
                        ->with($this->isInstanceOf("\\Brickoo\\Event\\Listener"));

        $cacheEventListener = new CacheEventListener($this->getCacheProxyStub());
        $this->assertNull($cacheEventListener->attachListeners($eventDispatcher));
    }

    /** @covers Brickoo\Cache\CacheEventListener::isEventSupported */
    public function testIsEventSupportedConditionCheck() {
        $cacheEventListener = new CacheEventListener($this->getCacheProxyStub());
        $this->assertFalse($cacheEventListener->isEventSupported(
            new GenericEvent(Events::GET), $this->getEventDispatcherStub()
        ));
        $this->assertTrue($cacheEventListener->isEventSupported(
            new CacheEvent(Events::GET), $this->getEventDispatcherStub()
        ));
    }

    /** @covers Brickoo\Cache\CacheEventListener::handleRetrieveEvent */
    public function testHandleRetrieveEvent() {
        $cacheIdentifier = "test";
        $cachedResponse  = "cached test response";

        $cacheProxy = $this->getCacheProxyStub();
        $cacheProxy->expects($this->once())
                     ->method("get")
                     ->with($cacheIdentifier)
                     ->will($this->returnValue($cachedResponse));

        $event = new RetrieveEvent($cacheIdentifier);
        $cacheEventListener = new CacheEventListener($cacheProxy);
        $this->assertEquals(
            $cachedResponse,
            $cacheEventListener->handleRetrieveEvent($event, $this->getEventDispatcherStub())
         );
    }

    /** @covers Brickoo\Cache\CacheEventListener::handleRetrieveByCallbackEvent */
    public function testHandleRetrieveByCallbackEvent() {
        $cacheIdentifier = "test";
        $cacheCallback = function(){ /*do nothing */ };
        $callbackArguments = array();
        $lifetime = 60;
        $cachedResponse = "cached test callback response";

        $cacheProxy = $this->getCacheProxyStub();
        $cacheProxy->expects($this->once())
                     ->method("getByCallback")
                     ->with($cacheIdentifier, $cacheCallback, $callbackArguments, $lifetime)
                     ->will($this->returnValue($cachedResponse));

        $event = new RetrieveByCallbackEvent(
            $cacheIdentifier,
            $cacheCallback,
            $callbackArguments,
            $lifetime
        );

        $cacheEventListener = new CacheEventListener($cacheProxy);
        $this->assertEquals(
            $cachedResponse,
            $cacheEventListener->handleRetrieveByCallbackEvent($event, $this->getEventDispatcherStub())
        );
    }

    /** @covers Brickoo\Cache\CacheEventListener::handleStoreEvent */
    public function testHandleStoreEvent() {
        $cacheIdentifier = "test";
        $content = "content to cache";
        $cacheLifetime = 60;

        $cacheProxy = $this->getCacheProxyStub();
        $cacheProxy->expects($this->once())
                     ->method("set")
                     ->with($cacheIdentifier, $content, $cacheLifetime)
                     ->will($this->returnSelf());

        $event = new StoreEvent(
            $cacheIdentifier,
            $content,
            $cacheLifetime
        );

        $cacheEventListener = new CacheEventListener($cacheProxy);
        $this->assertNull($cacheEventListener->handleStoreEvent($event, $this->getEventDispatcherStub()));
    }

    /** @covers Brickoo\Cache\CacheEventListener::handleDeleteEvent */
    public function testHandleDeleteEvent() {
        $cacheIdentifier = "test";

        $cacheProxy = $this->getCacheProxyStub();
        $cacheProxy->expects($this->once())
                     ->method("delete")
                     ->with($cacheIdentifier)
                     ->will($this->returnSelf());

        $event = new DeleteEvent($cacheIdentifier);
        $cacheEventListener = new CacheEventListener($cacheProxy);
        $this->assertNull($cacheEventListener->handleDeleteEvent($event, $this->getEventDispatcherStub()));
    }

    /** @covers Brickoo\Cache\CacheEventListener::handleFlushEvent */
    public function testHandleFlushEvent() {
        $cacheProxy = $this->getCacheProxyStub();
        $cacheProxy->expects($this->once())
                     ->method("flush")
                     ->will($this->returnSelf());

        $event = new FlushEvent();
        $cacheEventListener = new CacheEventListener($cacheProxy);
        $this->assertNull($cacheEventListener->handleFlushEvent($event, $this->getEventDispatcherStub()));
    }

    /**
     * Returns a cache proxy stub.
     * @return \Brickoo\Cache\CacheProxy
     */
    private function getCacheProxyStub() {
        return $this->getMockBuilder("\\Brickoo\\Cache\\CacheProxy")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns an event dispatcher stub.
     * @return \Brickoo\Event\EventDispatcher
     */
    private function getEventDispatcherStub() {
        return $this->getMockBuilder("\\Brickoo\\Event\\EventDispatcher")
            ->disableOriginalConstructor()
            ->getMock();
    }

}