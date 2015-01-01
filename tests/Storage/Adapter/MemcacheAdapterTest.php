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

namespace Brickoo\Tests\Component\Storage\Adapter;

use Brickoo\Component\Storage\Adapter\MemcacheAdapter;
use PHPUnit_Framework_TestCase;

/**
 * MemcacheAdapterTest
 *
 * Test suite for the MemcacheAdapter class.
 * @see Brickoo\Component\Storage\Adapter\MemcacheAdapter
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class MemcacheAdapterTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        if (! extension_loaded("memcache")) {
            $this->markTestSkipped("The memcache extension is not available.");
        }
    }

    /** @covers Brickoo\Component\Storage\Adapter\MemcacheAdapter::__construct */
    public function testConstructorImplementsInterface() {
        $memcache= $this->getMemcacheStub();
        $memcacheAdapter = new MemcacheAdapter($memcache);
        $this->assertInstanceOf("\\Brickoo\\Component\\Storage\\Adapter\\Adapter", $memcacheAdapter);
    }

    /** @covers Brickoo\Component\Storage\Adapter\MemcacheAdapter::set */
    public function testSetCacheContentToStoreWithMemcache() {
        $cacheIdentifier = "identifier";
        $cacheContent = "some content to cache";
        $cacheCompression = false;
        $cacheLifetime = 60;

        $memcache = $this->getMemcacheStub();
        $memcache->expects($this->once())
                 ->method("set")
                 ->with($cacheIdentifier,  $cacheContent, $cacheCompression, $cacheLifetime)
                 ->will($this->returnSelf());

        $memcacheAdapter = new MemcacheAdapter($memcache, $cacheCompression);
        $this->assertSame($memcacheAdapter, $memcacheAdapter->set($cacheIdentifier, $cacheContent, $cacheLifetime));
    }

    /**
     * @covers Brickoo\Component\Storage\Adapter\MemcacheAdapter::set
     * @expectedException \InvalidArgumentException
     */
    public function testSetThrowsAnArgumentException() {
        $memcacheAdapter = new MemcacheAdapter($this->getMemcacheStub());
        $memcacheAdapter->set(["wrongType"], "whatever", ["wrongType"]);
    }

    /** @covers Brickoo\Component\Storage\Adapter\MemcacheAdapter::get */
    public function testGetCacheContent() {
        $cacheIdentifier = "someIdentifier";
        $cachedContent = "some cached content";

        $memcache = $this->getMemcacheStub();
        $memcache->expects($this->once())
                 ->method("get")
                 ->with($cacheIdentifier)
                 ->will($this->returnValue($cachedContent));

        $memcacheAdapter = new MemcacheAdapter($memcache);
        $this->assertEquals($cachedContent, $memcacheAdapter->get($cacheIdentifier));
    }

    /**
     * @covers Brickoo\Component\Storage\Adapter\MemcacheAdapter::get
     * @expectedException \InvalidArgumentException
     */
    public function testGetThrowsAnArgumentException() {
        $memcacheAdapter = new MemcacheAdapter($this->getMock("\\Memcache"));
        $memcacheAdapter->get(["wrongType"]);
    }


    /** @covers Brickoo\Component\Storage\Adapter\MemcacheAdapter::delete */
    public function testDeleteCacheContent() {
        $cacheIdentifier = "someIdentifier";

        $memcache = $this->getMemcacheStub();
        $memcache->expects($this->once())
                 ->method("delete")
                 ->with($cacheIdentifier)
                 ->will($this->returnSelf());

        $memcacheAdapter = new MemcacheAdapter($memcache);
        $this->assertSame($memcacheAdapter, $memcacheAdapter->delete($cacheIdentifier));
    }

    /**
     * @covers Brickoo\Component\Storage\Adapter\MemcacheAdapter::delete
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteThrowsAnArgumentException() {
        $memcacheAdapter = new MemcacheAdapter($this->getMemcacheStub());
        $memcacheAdapter->delete(["wrongType"]);
    }

    /** @covers Brickoo\Component\Storage\Adapter\MemcacheAdapter::flush */
    public function testFlushCachedContent() {
        $memcache = $this->getMemcacheStub();
        $memcache->expects($this->once())
                 ->method("flush")
                 ->will($this->returnSelf());

        $memcacheAdapter = new MemcacheAdapter($memcache);
        $this->assertSame($memcacheAdapter, $memcacheAdapter->flush());
    }

    /** @covers Brickoo\Component\Storage\Adapter\MemcacheAdapter::isReady */
    public function testIsReady() {
        $memcache = new MemcacheAdapter($this->getMemcacheStub());
        $this->assertTrue($memcache->isReady());
    }

    /** @covers Brickoo\Component\Storage\Adapter\MemcacheAdapter::__call */
    public function testMagicCallToMemcacheMethod() {
        $cacheIdentifier = "someIdentifier";
        $cacheContent = "some content to cache";

        $memcache= $this->getMemcacheStub();
        $memcache->expects($this->once())
                 ->method("add")
                 ->with($cacheIdentifier, $cacheContent)
                 ->will($this->returnValue(true));

        $memcacheAdapter = new MemcacheAdapter($memcache);
        $this->assertTrue($memcacheAdapter->add($cacheIdentifier, $cacheContent));
    }

    /**
     * @covers Brickoo\Component\Storage\Adapter\MemcacheAdapter::__call
     * @expectedException \BadMethodCallException
     */
    public function testMagicCallThrowsABadMethodCallException() {
        $memcacheAdapter = new MemcacheAdapter($this->getMemcacheStub());
        $memcacheAdapter->whatever();
    }

    /**
     * Returns a memcache stub.
     * @return \Memcache
     */
    private function getMemcacheStub() {
        return $this->getMock("\\Memcache");
    }

}
