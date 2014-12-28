<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Tests\Component\Cache;

use Brickoo\Component\Cache\CacheProxy;
use Brickoo\Component\Cache\Adapter\Adapter;
use PHPUnit_Framework_TestCase;

/**
 * CacheProxyTest
 *
 * Test suite for the CacheProxy class.
 * @see Brickoo\Component\Cache\CacheProxy
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class CacheProxyTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Cache\CacheProxy::__construct
     * @covers Brickoo\Component\Cache\CacheProxy::getByCallback
     * @covers Brickoo\Component\Cache\CacheProxy::getAdapter
     * @covers Brickoo\Component\Cache\CacheProxy::getReadyAdapter
     */
    public function testGetByCallbackFallbackFromAdapterPoolStoresResultAfter() {
        $cacheIdentifier = "someIdentifier";
        $callback = function() {return "callback content";};
        $callbackArguments = [];
        $lifetime = 60;

        $adapter = $this->getAdapterStub();
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
     * @covers Brickoo\Component\Cache\CacheProxy::getByCallback
     * @expectedException \InvalidArgumentException
     */
    public function testGetByCallbackIdentifierThrowsInvalidArgumentException() {
        $cacheProxy = new CacheProxy($this->getAdapterPoolIteratorStub());
        $cacheProxy->getByCallback(["wrongType"], function(){}, [], 60);
    }

    /**
     * @covers Brickoo\Component\Cache\CacheProxy::getByCallback
     * @expectedException \InvalidArgumentException
     */
    public function testGetByCallbackLifetimeThrowsInvalidArgumentException() {
        $cacheProxy = new CacheProxy($this->getAdapterPoolIteratorStub());
        $cacheProxy->getByCallback("some_identifier", function(){}, [], "wrongType");
    }

    /**
     * @covers Brickoo\Component\Cache\CacheProxy::get
     * @covers Brickoo\Component\Cache\CacheProxy::getAdapter
     * @covers Brickoo\Component\Cache\CacheProxy::getReadyAdapter
     * @covers Brickoo\Component\Cache\CacheProxy::executeIterationCallback
     * @covers Brickoo\Component\Cache\CacheProxy::rewindAdapterPool
     */
    public function testGetCachedContentFromAnAdapter() {
        $cacheIdentifier = "someIdentifier";
        $cachedContent = "some cached content";

        $adapter = $this->getAdapterStub();
        $adapter->expects($this->any())
                ->method("get")
                ->with($cacheIdentifier)
                ->will($this->returnValue($cachedContent));

        $cacheProxy = new CacheProxy($this->buildAdapterPoolIteratorStub($adapter));
        $this->assertEquals($cachedContent, $cacheProxy->get($cacheIdentifier));
    }

    /**
     * @covers Brickoo\Component\Cache\CacheProxy::get
     * @covers Brickoo\Component\Cache\CacheProxy::getAdapter
     * @covers Brickoo\Component\Cache\CacheProxy::getReadyAdapter
     * @covers Brickoo\Component\Cache\Exception\AdapterNotFoundException
     * @expectedException \Brickoo\Component\Cache\Exception\AdapterNotFoundException
     */
    public function testGetContentWithoutAReadyAdapterThrowsException() {
        $cacheProxy = new CacheProxy($this->getAdapterPoolIteratorStub());
        $cacheProxy->get("some_identifier");
    }

    /**
     * @covers Brickoo\Component\Cache\CacheProxy::get
     * @expectedException \InvalidArgumentException
     */
    public function testGetWithInvalidIdentifierThrowsArgumentException() {
        $cacheProxy = new CacheProxy($this->getAdapterPoolIteratorStub());
        $cacheProxy->get(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Cache\CacheProxy::set
     * @covers Brickoo\Component\Cache\CacheProxy::getAdapter
     * @covers Brickoo\Component\Cache\CacheProxy::getReadyAdapter
     * @covers Brickoo\Component\Cache\CacheProxy::executeIterationCallback
     * @covers Brickoo\Component\Cache\CacheProxy::rewindAdapterPool
     */
    public function testStoringContentToCacheWithAnAdapter() {
        $cacheIdentifier = "someIdentifier";
        $cacheContent = "some content ot cache";
        $lifetime = 60;

        $adapter = $this->getAdapterStub();
        $adapter->expects($this->once())
                 ->method("set")
                 ->with($cacheIdentifier, $cacheContent, $lifetime);

        $cacheProxy = new CacheProxy($this->buildAdapterPoolIteratorStub($adapter));
        $this->assertSame($cacheProxy, $cacheProxy->set($cacheIdentifier, $cacheContent, $lifetime));
    }

    /**
     * @covers Brickoo\Component\Cache\CacheProxy::set
     * @expectedException \InvalidArgumentException
     */
    public function testSetWithInvalidIdentifierThrowsArgumentException() {
        $cacheProxy = new CacheProxy($this->getAdapterPoolIteratorStub());
        $cacheProxy->set(["wrongType"], "", 60);
    }

    /**
     * @covers Brickoo\Component\Cache\CacheProxy::set
     * @expectedException \InvalidArgumentException
     */
    public function testSetLifetimeThrowsArgumentException() {
        $cacheProxy = new CacheProxy($this->getAdapterPoolIteratorStub());
        $cacheProxy->set("some_valid_identifier", "", "wrongType");
    }

    /**
     * @covers Brickoo\Component\Cache\CacheProxy::delete
     * @covers Brickoo\Component\Cache\CacheProxy::executeIterationCallback
     * @covers Brickoo\Component\Cache\CacheProxy::rewindAdapterPool
     */
    public function testDeleteCachedContentWithAnAdapter() {
        $cacheIdentifier = "someIdentifier";
        $adapter = $this->getAdapterStub();
        $adapter->expects($this->once())
                ->method("delete")
                ->with($cacheIdentifier);
        $cacheProxy = new CacheProxy($this->buildAdapterPoolIteratorStub($adapter));
        $this->assertSame($cacheProxy, $cacheProxy->delete($cacheIdentifier));
    }

    /**
     * @covers Brickoo\Component\Cache\CacheProxy::delete
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteIdentifierThrowsArgumentException() {
        $cacheProxy = new CacheProxy($this->getAdapterPoolIteratorStub());
        $cacheProxy->delete(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Cache\CacheProxy::flush
     * @covers Brickoo\Component\Cache\CacheProxy::executeIterationCallback
     * @covers Brickoo\Component\Cache\CacheProxy::rewindAdapterPool
     */
    public function testFlushCachedContent() {
        $adapter = $this->getAdapterStub();
        $adapter->expects($this->once())
                ->method("flush");
        $cacheProxy = new CacheProxy($this->buildAdapterPoolIteratorStub($adapter));
        $this->assertSame($cacheProxy, $cacheProxy->flush());
    }

    /**
     * Returns an AdapterPoolIterator stub.
     * @param array $adaptersPool
     * @return \Brickoo\Component\Cache\Adapter\AdapterPoolIterator
     */
    private function getAdapterPoolIteratorStub(array $adaptersPool = []) {
        return $this->getMockBuilder("\\Brickoo\\Component\\Cache\\Adapter\\AdapterPoolIterator")
            ->setConstructorArgs([$adaptersPool])
            ->getMock();
    }

    /**
     * Return an adapter stub.
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getAdapterStub() {
        return $this->getMock("\\Brickoo\\Component\\Cache\\Adapter\\Adapter");
    }

    /**
     * Returns a pre-configured AdapterPoolIterator stub object.
     * @param \Brickoo\Component\Cache\Adapter\Adapter $adapter
     * @param integer $poolEntryKey the pool entry key
     * @return \Brickoo\Component\Cache\Adapter\AdapterPoolIterator
     */
    private function buildAdapterPoolIteratorStub(Adapter $adapter, $poolEntryKey = 0) {
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
