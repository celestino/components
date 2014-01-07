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

use Brickoo\Cache\CacheProxy,
    PHPUnit_Framework_TestCase;

/**
 * CacheProxyTest
 *
 * Test suite for the CacheProxy class.
 * @see Brickoo\Cache\CacheProxy
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class CacheProxyTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Cache\CacheProxy::__construct */
    public function testConstructorInitializeTheProperties() {
        $adapterPool = $this->getAdapterPoolIteratorStub();
        $cacheProxy = new CacheProxy($adapterPool);
        $this->assertAttributeEquals(null, "adapter", $cacheProxy);
        $this->assertAttributeSame($adapterPool, "adapterPoolIterator", $cacheProxy);
    }

    /**
     * @covers Brickoo\Cache\CacheProxy::getByCallback
     * @covers Brickoo\Cache\CacheProxy::getAdapter
     * @covers Brickoo\Cache\CacheProxy::getReadyAdapter
     */
    public function testGetByCallbackFallbackFromAdapterPoolStoresResultAfter() {
        $cacheIdentifier = "someIdentifier";
        $callback = function() {return "callback content";};
        $callbackArguments = [];
        $lifetime = 60;

        $adapter = $this->getMock("\\Brickoo\\Cache\\Adapter");
        $adapter->expects($this->once())
                ->method("get")
                ->will($this->returnValue(null));
        $adapter->expects($this->once())
                ->method("set")
                ->with($cacheIdentifier, "callback content", $lifetime);

        $cacheProxy = new CacheProxy($this->buildAdapterPoolIteratorStub($adapter));
        $this->assertEquals(
            "callback content",
            $cacheProxy->getByCallback($cacheIdentifier, $callback, $callbackArguments, $lifetime)
        );
    }

    /**
     * @covers Brickoo\Cache\CacheProxy::getByCallback
     * @expectedException InvalidArgumentException
     */
    public function testGetByCallbackIdentifierThrowsInvalidArgumentException() {
        $cacheProxy = new CacheProxy($this->getAdapterPoolIteratorStub());
        $cacheProxy->getByCallback(["wrongType"], function(){}, array(), 60);
    }

    /**
     * @covers Brickoo\Cache\CacheProxy::getByCallback
     * @expectedException InvalidArgumentException
     */
    public function testGetByCallbackLifetimeThrowsInvalidArgumentException() {
        $cacheProxy = new CacheProxy($this->getAdapterPoolIteratorStub());
        $cacheProxy->getByCallback("some_identifier", function(){}, array(), "wrongType");
    }

    /**
     * @covers Brickoo\Cache\CacheProxy::get
     * @covers Brickoo\Cache\CacheProxy::getAdapter
     * @covers Brickoo\Cache\CacheProxy::getReadyAdapter
     */
    public function testGetCachedContentFromAnAdapter() {
        $cacheIdentifier = "someIdentifier";
        $cachedContent = "some cached content";

        $adapter = $this->getMock("\\Brickoo\\Cache\\Adapter");
        $adapter->expects($this->any())
                ->method("get")
                ->with($cacheIdentifier)
                ->will($this->returnValue($cachedContent));

        $cacheProxy = new CacheProxy($this->buildAdapterPoolIteratorStub($adapter));
        $this->assertEquals($cachedContent, $cacheProxy->get($cacheIdentifier));
    }

    /**
     * @covers Brickoo\Cache\CacheProxy::get
     * @covers Brickoo\Cache\CacheProxy::getAdapter
     * @covers Brickoo\Cache\CacheProxy::getReadyAdapter
     * @covers Brickoo\Cache\Exception\AdapterNotFoundException
     * @expectedException Brickoo\Cache\Exception\AdapterNotFoundException
     */
    public function testGetContentWithoutAReadyAdapterThrowsExeption() {
        $cacheProxy = new CacheProxy($this->getAdapterPoolIteratorStub());
        $cacheProxy->get("some_identifier");
    }

    /**
     * @covers Brickoo\Cache\CacheProxy::get
     * @expectedException InvalidArgumentException
     */
    public function testGetWithInvalidIdentifierThrowsArgumentException() {
        $cacheProxy = new CacheProxy($this->getAdapterPoolIteratorStub());
        $cacheProxy->get(["wrongType"]);
    }

    /**
     * @covers Brickoo\Cache\CacheProxy::set
     * @covers Brickoo\Cache\CacheProxy::getAdapter
     * @covers Brickoo\Cache\CacheProxy::getReadyAdapter
     */
    public function testStoringContentToCacheWithAnAdapter() {
        $cacheIdentifier = "someIdentifier";
        $cacheContent = "some content ot cache";
        $lifetime = 60;

        $adapter = $this->getMock("\\Brickoo\\Cache\\Adapter");
        $adapter->expects($this->once())
                 ->method("set")
                 ->with($cacheIdentifier, $cacheContent, $lifetime);

        $cacheProxy = new CacheProxy($this->buildAdapterPoolIteratorStub($adapter));
        $this->assertSame($cacheProxy, $cacheProxy->set($cacheIdentifier, $cacheContent, $lifetime));
    }

    /**
     * @covers Brickoo\Cache\CacheProxy::set
     * @expectedException InvalidArgumentException
     */
    public function testSetWithInvalidIdentifierThrowsArgumentException() {
        $cacheProxy = new CacheProxy($this->getAdapterPoolIteratorStub());
        $cacheProxy->set(["wrongType"], "", 60);
    }

    /**
     * @covers Brickoo\Cache\CacheProxy::set
     * @expectedException InvalidArgumentException
     */
    public function testSetLifetimeThrowsArgumentException() {
        $cacheProxy = new CacheProxy($this->getAdapterPoolIteratorStub());
        $cacheProxy->set("some_valid_identifier", "", "wrongType");
    }

    /** @covers Brickoo\Cache\CacheProxy::delete */
    public function testDeleteCachedContentWithAnAdapter() {
        $cacheIdentifier = "someIdentifier";
        $adapter = $this->getMock("\\Brickoo\\Cache\\Adapter");
        $adapter->expects($this->once())
                ->method("delete")
                ->with($cacheIdentifier);
        $cacheProxy = new CacheProxy($this->buildAdapterPoolIteratorStub($adapter));
        $this->assertSame($cacheProxy, $cacheProxy->delete($cacheIdentifier));
    }

    /**
     * @covers Brickoo\Cache\CacheProxy::delete
     * @expectedException InvalidArgumentException
     */
    public function testDeleteIdentifierThrowsArgumentException() {
        $cacheProxy = new CacheProxy($this->getAdapterPoolIteratorStub());
        $cacheProxy->delete(["wrongType"]);
    }

    /** @covers Brickoo\Cache\CacheProxy::flush */
    public function testFlushCachedContent() {
        $adapter = $this->getMock("\\Brickoo\\Cache\\Adapter");
        $adapter->expects($this->once())
                ->method("flush");
        $cacheProxy = new CacheProxy($this->buildAdapterPoolIteratorStub($adapter));
        $this->assertSame($cacheProxy, $cacheProxy->flush());
    }

    /**
     * Returns an AdapterPoolIterator stub.
     * @return \Brickoo\Cache\AdapterPoolIterator
     */
    private function getAdapterPoolIteratorStub(array $adaptersPool = []) {
        return $this->getMockBuilder("\\Brickoo\\Cache\\AdapterPoolIterator")
            ->setConstructorArgs([$adaptersPool])
            ->getMock();
    }

    /**
     * Returns a pre-configured AdapterPoolIterator stub object.
     * @param \Brickoo\Cache\Adapter $adapter
     * @param string $poolEntryKey the pool entry key
     * @return \Brickoo\Cache\AdapterPoolIterator
     */
    private function buildAdapterPoolIteratorStub(\Brickoo\Cache\Adapter $adapter, $poolEntryKey = 0) {
        $adapterPoolIterator = $this->getAdapterPoolIteratorStub([$poolEntryKey => $adapter]);
        $adapterPoolIterator->expects($this->any())
                            ->method("isEmpty")
                            ->will($this->returnValue(false));
        $adapterPoolIterator->expects($this->any())
                            ->method("valid")
                            ->will($this->onConsecutiveCalls(true, false));
        $adapterPoolIterator->expects($this->once())
                            ->method("isCurrentReady")
                            ->will($this->returnValue(true));
        $adapterPoolIterator->expects($this->any())
                            ->method("current")
                            ->will($this->returnValue($adapter));
        $adapterPoolIterator->expects($this->any())
                            ->method("key")
                            ->will($this->returnValue($poolEntryKey));
        return $adapterPoolIterator;
    }

}