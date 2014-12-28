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

namespace Tests\Brickoo\Component\Cache;

use Brickoo\Component\Cache\Adapter\MemoryAdapter;
use PHPUnit_Framework_TestCase;

/**
 * MemoryAdapterTest
 *
 * Test suite for the MemoryAdapter class.
 * @see Brickoo\Component\Cache\Adapter\MemoryAdapter
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class MemoryAdapterTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Cache\Adapter\MemoryAdapter::__construct */
    public function testConstructorImplementsInterface() {
        $MemoryAdapter = new MemoryAdapter();
        $this->assertInstanceOf("\\Brickoo\\Component\\Cache\\Adapter\\Adapter", $MemoryAdapter);
    }

    /** @covers Brickoo\Component\Cache\Adapter\MemoryAdapter::set */
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
     * @covers Brickoo\Component\Cache\Adapter\MemoryAdapter::set
     * @expectedException \InvalidArgumentException
     */
    public function testSetIdentifierThrowsArgumentException() {
        $MemoryAdapter = new MemoryAdapter();
        $MemoryAdapter->set(array("wrongTpe"), "some content");
    }

    /** @covers Brickoo\Component\Cache\Adapter\MemoryAdapter::get */
    public function testGetCacheContent() {
        $MemoryAdapter = new MemoryAdapter();
        $MemoryAdapter->set("unique_identifier", "some content");
        $this->assertEquals("some content", $MemoryAdapter->get("unique_identifier"));
    }

    /**
     * @covers Brickoo\Component\Cache\Adapter\MemoryAdapter::get
     * @expectedException \InvalidArgumentException
     */
    public function testGetIdentifierThrowsArgumentException() {
        $MemoryAdapter = new MemoryAdapter();
        $MemoryAdapter->get(array("wrongTpe"));
    }

    /** @covers Brickoo\Component\Cache\Adapter\MemoryAdapter::get */
    public function testGetContentReturnsNullIfContentNotExist() {
        $MemoryAdapter = new MemoryAdapter();
        $this->assertNull($MemoryAdapter->get("some_identifier"));
    }

    /** @covers Brickoo\Component\Cache\Adapter\MemoryAdapter::delete */
    public function testDeleteCachedContent() {
        $MemoryAdapter = new MemoryAdapter();
        $MemoryAdapter->set("unique_identifier", "");
        $this->assertSame($MemoryAdapter, $MemoryAdapter->delete("unique_identifier"));
        $this->assertAttributeEquals([], "cacheValues", $MemoryAdapter);
    }

    /**
     * @covers Brickoo\Component\Cache\Adapter\MemoryAdapter::delete
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteIdentifierThrowsArgumentException() {
        $MemoryAdapter = new MemoryAdapter();
        $MemoryAdapter->delete(array("wrongTpe"));
    }

    /** @covers Brickoo\Component\Cache\Adapter\MemoryAdapter::has */
    public function testHasAnIdentifier() {
        $MemoryAdapter = new MemoryAdapter();
        $this->assertFalse($MemoryAdapter->has("unique_identifier"));

        $MemoryAdapter->set("unique_identifier", "some content");
        $this->assertTrue($MemoryAdapter->has("unique_identifier"));
    }

    /** @covers Brickoo\Component\Cache\Adapter\MemoryAdapter::flush */
    public function testFlushCachedContent() {
        $MemoryAdapter = new MemoryAdapter();
        $MemoryAdapter->set("unique_identifier", "some content");
        $this->assertTrue($MemoryAdapter->has("unique_identifier"));

        $this->assertSame($MemoryAdapter, $MemoryAdapter->flush());
        $this->assertAttributeEquals([], "cacheValues", $MemoryAdapter);
    }

    /** @covers Brickoo\Component\Cache\Adapter\MemoryAdapter::isReady */
    public function testIsReady() {
        $MemoryAdapter =  new MemoryAdapter();
        $this->assertTrue($MemoryAdapter->isReady());
    }

}
