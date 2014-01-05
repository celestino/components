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

namespace Brickoo\Tests\Cache;

use Brickoo\Cache\AdapterPoolIterator,
    PHPUnit_Framework_TestCase;

/**
 * AdapterPoolIteratorTest
 *
 * Test suite for the Adapter class.
 * @see Brickoo\Cache\Adapter
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AdapterPoolIteratorTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Cache\AdapterPoolIterator::__construct */
    public function testConstructorInitializeProperties() {
        $poolEntries = ["doNothing" => ($adapter = new \Brickoo\Cache\Adapter\DoNothingAdapter())];
        $adapterPoolIterator = new AdapterPoolIterator($poolEntries);
        $this->assertAttributeContains("doNothing", "mappingKeys", $adapterPoolIterator);
        $this->assertAttributeContains($adapter, "poolEntries", $adapterPoolIterator);
    }

    /**
     * @covers Brickoo\Cache\AdapterPoolIterator::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testWrongEntriesTypeThrowsInvalidArgumentException() {
        $poolEntries = ["someEntry" => "WRONG_VALUE"];
        $adapterPoolIterator = new AdapterPoolIterator($poolEntries);
    }

    /** @covers Brickoo\Cache\AdapterPoolIterator::isCurrentReady */
    public function testIsCurrentReady() {
        $adapter = $this->getMock("\\Brickoo\\Cache\\Adapter");
        $adapter->expects($this->once())
                ->method("isready")
                ->will($this->returnValue(true));
        $adapterPoolIterator = new AdapterPoolIterator(["adapter" => $adapter]);
        $this->assertTrue($adapterPoolIterator->isCurrentReady());
    }

    /**
     * @covers Brickoo\Cache\AdapterPoolIterator::isCurrentReady
     * @covers Brickoo\Cache\AdapterPoolIterator::isEmpty
     * @covers Brickoo\Cache\Exception\PoolIsEmptyException
     * @expectedException Brickoo\Cache\Exception\PoolIsEmptyException
     */
    public function testIsCurrentReadyThrowsExceptionIfPoolIsEmpty() {
        $adapterPoolIterator = new AdapterPoolIterator([]);
        $adapterPoolIterator->isCurrentReady();
    }

    /**
     * @covers Brickoo\Cache\AdapterPoolIterator::valid
     * @covers Brickoo\Cache\AdapterPoolIterator::rewind
     * @covers Brickoo\Cache\AdapterPoolIterator::next
     * @covers Brickoo\Cache\AdapterPoolIterator::key
     * @covers Brickoo\Cache\AdapterPoolIterator::current
     */
    public function testIteratorImplementation() {
        $adapter = $this->getMock("\\Brickoo\\Cache\\Adapter");
        $adapterPoolIterator = new AdapterPoolIterator(["adapter" => $adapter]);
        $adapterPoolIterator->rewind();
        $this->assertAttributeEquals(0, "currentPointerPosition", $adapterPoolIterator);
        $this->assertTrue($adapterPoolIterator->valid());
        $this->assertEquals("adapter", $adapterPoolIterator->key());
        $this->assertSame($adapter, $adapterPoolIterator->current());
        $adapterPoolIterator->next();
        $this->assertAttributeEquals(1, "currentPointerPosition", $adapterPoolIterator);
        $this->assertFalse($adapterPoolIterator->valid());
        $this->assertEquals("1", $adapterPoolIterator->key());
        $adapterPoolIterator->rewind();
        $this->assertAttributeEquals(0, "currentPointerPosition", $adapterPoolIterator);
    }

    /**
     * @covers Brickoo\Cache\AdapterPoolIterator::current
     * @covers Brickoo\Cache\AdapterPoolIterator::isEmpty
     * @covers Brickoo\Cache\Exception\PoolIsEmptyException
     * @expectedException Brickoo\Cache\Exception\PoolIsEmptyException
     */
    public function testIteratorThrowsExceptionIfCurrentValueIsNotAvailable() {
        $adapter = $this->getMock("\\Brickoo\\Cache\\Adapter");
        $adapterPoolIterator = new AdapterPoolIterator(["adapter" => $adapter]);
        $adapterPoolIterator->rewind();
        $this->assertSame($adapter, $adapterPoolIterator->current());
        $adapterPoolIterator->next();
        $adapterPoolIterator->current();
    }

    /** @covers Brickoo\Cache\AdapterPoolIterator::select */
    public function testSelectionOfAnAdapter() {
        $adapterPoolIterator = new AdapterPoolIterator($this->getPoolEntries());
        $this->assertSame($adapterPoolIterator, $adapterPoolIterator->select("adapter_2"));
        $this->assertAttributeEquals(1, "currentPointerPosition", $adapterPoolIterator);
    }

    /**
     * @covers Brickoo\Cache\AdapterPoolIterator::select
     * @covers Brickoo\Cache\AdapterPoolIterator::getMappingPosition
     * @covers Brickoo\Cache\Exception\PoolIndentifierDoesNotExistException
     * @expectedException Brickoo\Cache\Exception\PoolIndentifierDoesNotExistException
     */
    public function testSelectThrowsExceptionIfAdapterDoesNotExist() {
        $adapterPoolIterator = new AdapterPoolIterator([]);
        $adapterPoolIterator->select("some_adapter");
    }

    /**
     * @covers Brickoo\Cache\AdapterPoolIterator::remove
     * @covers Brickoo\Cache\AdapterPoolIterator::getMappingPosition
     */
    public function testRemoveAnAdapterFromPool() {
        $adapterPoolIterator = new AdapterPoolIterator($this->getPoolEntries());
        $this->assertEquals(2, count($adapterPoolIterator));
        $this->assertSame($adapterPoolIterator, $adapterPoolIterator->remove("adapter_2"));
        $this->assertEquals(1, count($adapterPoolIterator));
    }

    /** @covers Brickoo\Cache\AdapterPoolIterator::remove */
    public function testRemoveAnAdapterMovesInternalPointer() {
        $adapterPoolIterator = new AdapterPoolIterator($this->getPoolEntries());
        $adapterPoolIterator->select("adapter_2");
        $this->assertAttributeEquals(1, "currentPointerPosition", $adapterPoolIterator);
        $adapterPoolIterator->remove("adapter_2");
        $this->assertAttributeEquals(0, "currentPointerPosition", $adapterPoolIterator);
    }

    /**
     * @covers Brickoo\Cache\AdapterPoolIterator::remove
     * @covers Brickoo\Cache\Exception\PoolIndentifierDoesNotExistException
     * @expectedException Brickoo\Cache\Exception\PoolIndentifierDoesNotExistException
     */
    public function testRemoveThrowsExceptionIfAdapterDoesNotExist() {
        $adapterPoolIterator = new AdapterPoolIterator([]);
        $adapterPoolIterator->remove("some_adapter");
    }

    /** @covers Brickoo\Cache\AdapterPoolIterator::has */
    public function testHasAnAdapterInPool() {
        $adapterPoolIterator = new AdapterPoolIterator($this->getPoolEntries());
        $this->assertTrue($adapterPoolIterator->has("adapter_2"));
        $this->assertFalse($adapterPoolIterator->has("some_adapter"));
    }

    /** @covers Brickoo\Cache\AdapterPoolIterator::isEmpty */
    public function testPoolIsEmpty() {
        $adapterPoolIterator = new AdapterPoolIterator([]);
        $this->assertTrue($adapterPoolIterator->isEmpty());

        $adapterPoolIterator = new AdapterPoolIterator($this->getPoolEntries());
        $this->assertFalse($adapterPoolIterator->isEmpty());
    }

    /** @covers Brickoo\Cache\AdapterPoolIterator::count     */
    public function testCountAdapterPoolEntries() {
        $adapterPoolIterator = new AdapterPoolIterator([]);
        $this->assertEquals(0, count($adapterPoolIterator));

        $adapterPoolIterator = new AdapterPoolIterator($this->getPoolEntries());
        $this->assertEquals(2, count($adapterPoolIterator));
    }

    /**
     * Returns test adapter entries.
     * @return array instancesOf \Brickoo\Cache\Adapter
     */
    private function getPoolEntries() {
        $adapter = $this->getMock("\\Brickoo\\Cache\\Adapter");
        return  ["adapter_1" => $adapter, "adapter_2" => $adapter];
    }

}