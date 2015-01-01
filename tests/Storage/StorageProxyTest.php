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

namespace Brickoo\Tests\Component\Storage;

use Brickoo\Component\Storage\StorageProxy;
use Brickoo\Component\Storage\Adapter\Adapter;
use PHPUnit_Framework_TestCase;

/**
 * StorageProxyTest
 *
 * Test suite for the StorageProxy class.
 * @see Brickoo\Component\Storage\StorageProxy
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class StorageProxyTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::__construct
     * @covers Brickoo\Component\Storage\StorageProxy::getByCallback
     * @covers Brickoo\Component\Storage\StorageProxy::getAdapter
     * @covers Brickoo\Component\Storage\StorageProxy::getReadyAdapter
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

        $storageProxy = new StorageProxy($this->buildAdapterPoolIteratorStub($adapter));
        $this->assertEquals(
            "callback content",
            $storageProxy->getByCallback($cacheIdentifier, $callback, $callbackArguments, $lifetime)
        );
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::getByCallback
     * @expectedException \InvalidArgumentException
     */
    public function testGetByCallbackIdentifierThrowsInvalidArgumentException() {
        $storageProxy = new StorageProxy($this->getAdapterPoolIteratorStub());
        $storageProxy->getByCallback(["wrongType"], function(){}, [], 60);
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::getByCallback
     * @expectedException \InvalidArgumentException
     */
    public function testGetByCallbackLifetimeThrowsInvalidArgumentException() {
        $storageProxy = new StorageProxy($this->getAdapterPoolIteratorStub());
        $storageProxy->getByCallback("some_identifier", function(){}, [], "wrongType");
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::get
     * @covers Brickoo\Component\Storage\StorageProxy::getAdapter
     * @covers Brickoo\Component\Storage\StorageProxy::getReadyAdapter
     * @covers Brickoo\Component\Storage\StorageProxy::executeIterationCallback
     * @covers Brickoo\Component\Storage\StorageProxy::rewindAdapterPool
     */
    public function testGetCachedContentFromAnAdapter() {
        $cacheIdentifier = "someIdentifier";
        $cachedContent = "some cached content";

        $adapter = $this->getAdapterStub();
        $adapter->expects($this->any())
                ->method("get")
                ->with($cacheIdentifier)
                ->will($this->returnValue($cachedContent));

        $storageProxy = new StorageProxy($this->buildAdapterPoolIteratorStub($adapter));
        $this->assertEquals($cachedContent, $storageProxy->get($cacheIdentifier));
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::get
     * @covers Brickoo\Component\Storage\StorageProxy::getAdapter
     * @covers Brickoo\Component\Storage\StorageProxy::getReadyAdapter
     * @covers Brickoo\Component\Storage\Exception\AdapterNotFoundException
     * @expectedException \Brickoo\Component\Storage\Exception\AdapterNotFoundException
     */
    public function testGetContentWithoutAReadyAdapterThrowsException() {
        $storageProxy = new StorageProxy($this->getAdapterPoolIteratorStub());
        $storageProxy->get("some_identifier");
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::get
     * @expectedException \InvalidArgumentException
     */
    public function testGetWithInvalidIdentifierThrowsArgumentException() {
        $storageProxy = new StorageProxy($this->getAdapterPoolIteratorStub());
        $storageProxy->get(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::set
     * @covers Brickoo\Component\Storage\StorageProxy::getAdapter
     * @covers Brickoo\Component\Storage\StorageProxy::getReadyAdapter
     * @covers Brickoo\Component\Storage\StorageProxy::executeIterationCallback
     * @covers Brickoo\Component\Storage\StorageProxy::rewindAdapterPool
     */
    public function testStoringContentToCacheWithAnAdapter() {
        $cacheIdentifier = "someIdentifier";
        $cacheContent = "some content ot cache";
        $lifetime = 60;

        $adapter = $this->getAdapterStub();
        $adapter->expects($this->once())
                 ->method("set")
                 ->with($cacheIdentifier, $cacheContent, $lifetime);

        $storageProxy = new StorageProxy($this->buildAdapterPoolIteratorStub($adapter));
        $this->assertSame($storageProxy, $storageProxy->set($cacheIdentifier, $cacheContent, $lifetime));
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::set
     * @expectedException \InvalidArgumentException
     */
    public function testSetWithInvalidIdentifierThrowsArgumentException() {
        $storageProxy = new StorageProxy($this->getAdapterPoolIteratorStub());
        $storageProxy->set(["wrongType"], "", 60);
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::set
     * @expectedException \InvalidArgumentException
     */
    public function testSetLifetimeThrowsArgumentException() {
        $storageProxy = new StorageProxy($this->getAdapterPoolIteratorStub());
        $storageProxy->set("some_valid_identifier", "", "wrongType");
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::delete
     * @covers Brickoo\Component\Storage\StorageProxy::executeIterationCallback
     * @covers Brickoo\Component\Storage\StorageProxy::rewindAdapterPool
     */
    public function testDeleteCachedContentWithAnAdapter() {
        $cacheIdentifier = "someIdentifier";
        $adapter = $this->getAdapterStub();
        $adapter->expects($this->once())
                ->method("delete")
                ->with($cacheIdentifier);
        $storageProxy = new StorageProxy($this->buildAdapterPoolIteratorStub($adapter));
        $this->assertSame($storageProxy, $storageProxy->delete($cacheIdentifier));
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::delete
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteIdentifierThrowsArgumentException() {
        $storageProxy = new StorageProxy($this->getAdapterPoolIteratorStub());
        $storageProxy->delete(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::flush
     * @covers Brickoo\Component\Storage\StorageProxy::executeIterationCallback
     * @covers Brickoo\Component\Storage\StorageProxy::rewindAdapterPool
     */
    public function testFlushCachedContent() {
        $adapter = $this->getAdapterStub();
        $adapter->expects($this->once())
                ->method("flush");
        $storageProxy = new StorageProxy($this->buildAdapterPoolIteratorStub($adapter));
        $this->assertSame($storageProxy, $storageProxy->flush());
    }

    /**
     * Returns an AdapterPoolIterator stub.
     * @param array $adaptersPool
     * @return \Brickoo\Component\Storage\Adapter\AdapterPoolIterator
     */
    private function getAdapterPoolIteratorStub(array $adaptersPool = []) {
        return $this->getMockBuilder("\\Brickoo\\Component\\Storage\\Adapter\\AdapterPoolIterator")
            ->setConstructorArgs([$adaptersPool])
            ->getMock();
    }

    /**
     * Return an adapter stub.
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getAdapterStub() {
        return $this->getMock("\\Brickoo\\Component\\Storage\\Adapter\\Adapter");
    }

    /**
     * Returns a pre-configured AdapterPoolIterator stub object.
     * @param \Brickoo\Component\Storage\Adapter\Adapter $adapter
     * @param integer $poolEntryKey the pool entry key
     * @return \Brickoo\Component\Storage\Adapter\AdapterPoolIterator
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
