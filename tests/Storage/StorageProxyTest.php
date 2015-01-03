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
use Brickoo\Component\Storage\Adapter\AdapterPoolIterator;
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
        $storageIdentifier = "someIdentifier";
        $callback = function() {return "callback content";};
        $callbackArguments = [];
        $lifetime = 60;

        $adapter = $this->getAdapterStub();
        $adapter->expects($this->once())
                ->method("get")
                ->will($this->returnValue(null));
        $adapter->expects($this->once())
                ->method("set")
                ->with($storageIdentifier, "callback content", $lifetime);

        $storageProxy = new StorageProxy($this->getAdapterPoolIterator([$adapter]));
        $this->assertEquals(
            "callback content",
            $storageProxy->getByCallback($storageIdentifier, $callback, $callbackArguments, $lifetime)
        );
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::getByCallback
     * @expectedException \InvalidArgumentException
     */
    public function testGetByCallbackIdentifierThrowsInvalidArgumentException() {
        $storageProxy = new StorageProxy($this->getAdapterPoolIterator());
        $storageProxy->getByCallback(["wrongType"], function(){}, [], 60);
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::getByCallback
     * @expectedException \InvalidArgumentException
     */
    public function testGetByCallbackLifetimeThrowsInvalidArgumentException() {
        $storageProxy = new StorageProxy($this->getAdapterPoolIterator());
        $storageProxy->getByCallback("some_identifier", function(){}, [], "wrongType");
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::get
     * @covers Brickoo\Component\Storage\StorageProxy::getAdapter
     * @covers Brickoo\Component\Storage\StorageProxy::getReadyAdapter
     * @covers Brickoo\Component\Storage\StorageProxy::executeIterationCallback
     * @covers Brickoo\Component\Storage\StorageProxy::rewindAdapterPool
     */
    public function testGetStoredContentFromAnAdapter() {
        $storageIdentifier = "someIdentifier";
        $storedContent = "some stored content";

        $adapter = $this->getAdapterStub();
        $adapter->expects($this->any())
                ->method("get")
                ->with($storageIdentifier)
                ->will($this->returnValue($storedContent));

        $storageProxy = new StorageProxy($this->getAdapterPoolIterator([$adapter]));
        $this->assertEquals($storedContent, $storageProxy->get($storageIdentifier));
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::get
     * @covers Brickoo\Component\Storage\StorageProxy::getAdapter
     * @covers Brickoo\Component\Storage\StorageProxy::getReadyAdapter
     * @covers Brickoo\Component\Storage\Exception\AdapterNotFoundException
     * @expectedException \Brickoo\Component\Storage\Exception\AdapterNotFoundException
     */
    public function testGetContentWithoutAReadyAdapterThrowsException() {
        $storageProxy = new StorageProxy($this->getAdapterPoolIterator());
        $storageProxy->get("some_identifier");
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::get
     * @expectedException \InvalidArgumentException
     */
    public function testGetWithInvalidIdentifierThrowsArgumentException() {
        $storageProxy = new StorageProxy($this->getAdapterPoolIterator());
        $storageProxy->get(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::set
     * @covers Brickoo\Component\Storage\StorageProxy::getAdapter
     * @covers Brickoo\Component\Storage\StorageProxy::getReadyAdapter
     * @covers Brickoo\Component\Storage\StorageProxy::executeIterationCallback
     * @covers Brickoo\Component\Storage\StorageProxy::rewindAdapterPool
     */
    public function testStoringContentWithAnAdapter() {
        $storageIdentifier = "someIdentifier";
        $storedContent = "some content to store";
        $lifetime = 60;

        $adapter = $this->getAdapterStub();
        $adapter->expects($this->once())
                ->method("set")
                ->with($storageIdentifier, $storedContent, $lifetime);

        $storageProxy = new StorageProxy($this->getAdapterPoolIterator([$adapter]));
        $this->assertSame($storageProxy, $storageProxy->set($storageIdentifier, $storedContent, $lifetime));
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::set
     * @expectedException \InvalidArgumentException
     */
    public function testSetWithInvalidIdentifierThrowsArgumentException() {
        $storageProxy = new StorageProxy($this->getAdapterPoolIterator());
        $storageProxy->set(["wrongType"], "", 60);
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::set
     * @expectedException \InvalidArgumentException
     */
    public function testSetLifetimeThrowsArgumentException() {
        $storageProxy = new StorageProxy($this->getAdapterPoolIterator());
        $storageProxy->set("some_valid_identifier", "", "wrongType");
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::delete
     * @covers Brickoo\Component\Storage\StorageProxy::executeIterationCallback
     * @covers Brickoo\Component\Storage\StorageProxy::rewindAdapterPool
     */
    public function testDeleteCachedContentWithAnAdapter() {
        $storageIdentifier = "someIdentifier";
        $adapter = $this->getAdapterStub();
        $adapter->expects($this->once())
                ->method("delete")
                ->with($storageIdentifier);
        $storageProxy = new StorageProxy($this->getAdapterPoolIterator([$adapter]));
        $this->assertSame($storageProxy, $storageProxy->delete($storageIdentifier));
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::delete
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteIdentifierThrowsArgumentException() {
        $storageProxy = new StorageProxy($this->getAdapterPoolIterator());
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
        $storageProxy = new StorageProxy($this->getAdapterPoolIterator([$adapter]));
        $this->assertSame($storageProxy, $storageProxy->flush());
    }

    /**
     * @covers Brickoo\Component\Storage\StorageProxy::get
     * @covers Brickoo\Component\Storage\StorageProxy::getAdapter
     * @covers Brickoo\Component\Storage\StorageProxy::getReadyAdapter
     * @covers Brickoo\Component\Storage\StorageProxy::executeIterationCallback
     */
    public function testGetAdapterUsesFirstReadyAdapter() {
        $storageIdentifier = "someId";
        $storageContent = "some content";

        $adapterExpected = $this->getAdapterStub();
        $adapterExpected->expects($this->once())
                        ->method("get")
                        ->with($storageIdentifier)
                        ->will($this->returnValue($storageContent));
        $adapterUnexpected = $this->getAdapterStub();

        $storageProxy = new StorageProxy($this->getAdapterPoolIterator([$adapterExpected, $adapterUnexpected]));
        $this->assertEquals($storageContent, $storageProxy->get($storageIdentifier));
    }

    /**
     * Returns an AdapterPoolIterator.
     * @param array $adaptersPool
     * @return \Brickoo\Component\Storage\Adapter\AdapterPoolIterator
     */
    private function getAdapterPoolIterator(array $adaptersPool = []) {
        return new AdapterPoolIterator($adaptersPool);
    }

    /**
     * Return an adapter stub.
     * @param boolean $isReady
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getAdapterStub($isReady = true) {
        $mock = $this->getMock("\\Brickoo\\Component\\Storage\\Adapter\\Adapter");
        $mock->expects($this->any())
             ->method("isReady")
             ->will($this->returnValue($isReady));
        return $mock;
    }

}
