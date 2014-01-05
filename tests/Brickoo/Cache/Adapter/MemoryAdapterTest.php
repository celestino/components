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

use Brickoo\Cache\Adapter\MemoryAdapter,
    PHPUnit_Framework_TestCase;

/**
 * MemoryAdapterTest
 *
 * Test suite for the MemoryAdapter class.
 * @see Brickoo\Cache\Adapter\MemoryAdapter
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MemoryAdapterTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Cache\Adapter\MemoryAdapter::__construct */
    public function testConstructor() {
        $MemoryAdapter = new MemoryAdapter();
        $this->assertInstanceOf("\\Brickoo\\Cache\\Adapter", $MemoryAdapter);
        $this->assertAttributeEquals(array(), "cacheValues", $MemoryAdapter);
    }

    /** @covers Brickoo\Cache\Adapter\MemoryAdapter::set */
    public function testSetCacheContent() {
        $MemoryAdapter = new MemoryAdapter();
        $this->assertSame($MemoryAdapter, $MemoryAdapter->set("unique_identifier", "some content"));
        $this->assertAttributeEquals(
            array("unique_identifier" => "some content"),
            "cacheValues",
            $MemoryAdapter
        );
    }

    /**
     * @covers Brickoo\Cache\Adapter\MemoryAdapter::set
     * @expectedException InvalidArgumentException
     */
    public function testSetIdentifierThrowsArgumentException() {
        $MemoryAdapter = new MemoryAdapter();
        $MemoryAdapter->set(array("wrongTpe"), "some content");
    }

    /** @covers Brickoo\Cache\Adapter\MemoryAdapter::get */
    public function testGetCacheContent() {
        $MemoryAdapter = new MemoryAdapter();
        $MemoryAdapter->set("unique_identifier", "some content");
        $this->assertEquals("some content", $MemoryAdapter->get("unique_identifier"));
    }

    /**
     * @covers Brickoo\Cache\Adapter\MemoryAdapter::get
     * @expectedException InvalidArgumentException
     */
    public function testGetIdentifierThrowsArgumentException() {
        $MemoryAdapter = new MemoryAdapter();
        $MemoryAdapter->get(array("wrongTpe"));
    }

    /** @covers Brickoo\Cache\Adapter\MemoryAdapter::get */
    public function testGetContentReturnsNullIfContentNotExist() {
        $MemoryAdapter = new MemoryAdapter();
        $this->assertNull($MemoryAdapter->get("some_identifier"));
    }

    /** @covers Brickoo\Cache\Adapter\MemoryAdapter::delete */
    public function testDeleteCachedContent() {
        $MemoryAdapter = new MemoryAdapter();
        $MemoryAdapter->set("unique_identifier", "");
        $this->assertSame($MemoryAdapter, $MemoryAdapter->delete("unique_identifier"));
        $this->assertAttributeEquals(array(), "cacheValues", $MemoryAdapter);
    }

    /**
     * @covers Brickoo\Cache\Adapter\MemoryAdapter::delete
     * @expectedException InvalidArgumentException
     */
    public function testDeleteIdentifierThrowsArgumentException() {
        $MemoryAdapter = new MemoryAdapter();
        $MemoryAdapter->delete(array("wrongTpe"));
    }

    /** @covers Brickoo\Cache\Adapter\MemoryAdapter::has */
    public function testHasAnIdentifier() {
        $MemoryAdapter = new MemoryAdapter();
        $this->assertFalse($MemoryAdapter->has("unique_identifier"));

        $MemoryAdapter->set("unique_identifier", "some content");
        $this->assertTrue($MemoryAdapter->has("unique_identifier"));
    }

    /** @covers Brickoo\Cache\Adapter\MemoryAdapter::flush */
    public function testFlushCachedContent() {
        $MemoryAdapter = new MemoryAdapter();
        $MemoryAdapter->set("unique_identifier", "some content");
        $this->assertTrue($MemoryAdapter->has("unique_identifier"));

        $this->assertSame($MemoryAdapter, $MemoryAdapter->flush());
        $this->assertAttributeEquals(array(), "cacheValues", $MemoryAdapter);
    }

    /** @covers Brickoo\Cache\Adapter\MemoryAdapter::isReady */
    public function testIsReady() {
        $MemoryAdapter =  new MemoryAdapter();
        $this->assertTrue($MemoryAdapter->isReady());
    }

}