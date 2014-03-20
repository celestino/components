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

namespace Brickoo\Tests\Component\Cache\Adapter;

use Brickoo\Component\Cache\Adapter\MemcacheAdapter,
    PHPUnit_Framework_TestCase;

/**
 * MemcacheAdapterTest
 *
 * Test suite for the MemcacheAdapter class.
 * @see Brickoo\Component\Cache\Adapter\MemcacheAdapter
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MemcacheAdapterTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        if (! extension_loaded("memcache")) {
            $this->markTestSkipped("The memcache extension is not available.");
        }
    }

    /** @covers Brickoo\Component\Cache\Adapter\MemcacheAdapter::__construct */
    public function testConstructorInitializesProperties() {
        $memcache= $this->getMemcacheStub();
        $memcacheAdapter = new MemcacheAdapter($memcache);
        $this->assertAttributeSame($memcache, "memcache", $memcacheAdapter);
    }

    /** @covers Brickoo\Component\Cache\Adapter\MemcacheAdapter::set */
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
     * @covers Brickoo\Component\Cache\Adapter\MemcacheAdapter::set
     * @expectedException \InvalidArgumentException
     */
    public function testSetThrowsAnArgumentException() {
        $memcacheAdapter = new MemcacheAdapter($this->getMemcacheStub());
        $memcacheAdapter->set(array("wrongType"), "whatever", array("wrongType"));
    }

    /** @covers Brickoo\Component\Cache\Adapter\MemcacheAdapter::get */
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
     * @covers Brickoo\Component\Cache\Adapter\MemcacheAdapter::get
     * @expectedException \InvalidArgumentException
     */
    public function testGetThrowsAnArgumentException() {
        $memcacheAdapter = new MemcacheAdapter($this->getMock("\\Memcache"));
        $memcacheAdapter->get(array("wrongType"));
    }


    /** @covers Brickoo\Component\Cache\Adapter\MemcacheAdapter::delete */
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
     * @covers Brickoo\Component\Cache\Adapter\MemcacheAdapter::delete
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteThrowsAnArgumentException() {
        $memcacheAdapter = new MemcacheAdapter($this->getMemcacheStub());
        $memcacheAdapter->delete(array("wrongType"));
    }

    /** @covers Brickoo\Component\Cache\Adapter\MemcacheAdapter::flush */
    public function testFlushCachedContent() {
        $memcache = $this->getMemcacheStub();
        $memcache->expects($this->once())
                 ->method("flush")
                 ->will($this->returnSelf());

        $memcacheAdapter = new MemcacheAdapter($memcache);
        $this->assertSame($memcacheAdapter, $memcacheAdapter->flush());
    }

    /** @covers Brickoo\Component\Cache\Adapter\MemcacheAdapter::isReady */
    public function testIsReady() {
        $memcache = new MemcacheAdapter($this->getMemcacheStub());
        $this->assertTrue($memcache->isReady());
    }

    /** @covers Brickoo\Component\Cache\Adapter\MemcacheAdapter::__call */
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
     * @covers Brickoo\Component\Cache\Adapter\MemcacheAdapter::__call
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
        return $this->getMock("Memcache");
    }

}
