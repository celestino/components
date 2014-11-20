<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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

namespace Brickoo\Tests\Component\Cache\Adapter;

use Brickoo\Component\Cache\Adapter\AdapterPoolIterator;
use Brickoo\Component\Cache\Adapter\DoNothingAdapter;
use PHPUnit_Framework_TestCase;

/**
 * AdapterPoolIteratorTest
 *
 * Test suite for the Adapter class.
 * @see Brickoo\Component\Cache\Adapter
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AdapterPoolIteratorTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::__construct */
    public function testConstructorInitializeProperties() {
        $poolEntries = ["doNothing" => ($adapter = new DoNothingAdapter())];
        $adapterPoolIterator = new AdapterPoolIterator($poolEntries);
        $this->assertAttributeContains("doNothing", "mappingKeys", $adapterPoolIterator);
        $this->assertAttributeContains($adapter, "poolEntries", $adapterPoolIterator);
    }

    /**
     * @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testWrongEntriesTypeThrowsInvalidArgumentException() {
        $poolEntries = ["someEntry" => "WRONG_VALUE"];
        new AdapterPoolIterator($poolEntries);
    }

    /** @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::isCurrentReady */
    public function testIsCurrentReady() {
        $adapter = $this->getAdapterStub();
        $adapter->expects($this->once())
                ->method("isReady")
                ->will($this->returnValue(true));
        $adapterPoolIterator = new AdapterPoolIterator(["adapter" => $adapter]);
        $this->assertTrue($adapterPoolIterator->isCurrentReady());
    }

    /**
     * @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::isCurrentReady
     * @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::isEmpty
     * @covers Brickoo\Component\Cache\Adapter\Exception\PoolIsEmptyException
     * @expectedException \Brickoo\Component\Cache\Adapter\Exception\PoolIsEmptyException
     */
    public function testIsCurrentReadyThrowsExceptionIfPoolIsEmpty() {
        $adapterPoolIterator = new AdapterPoolIterator([]);
        $adapterPoolIterator->isCurrentReady();
    }

    /**
     * @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::valid
     * @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::rewind
     * @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::next
     * @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::key
     * @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::current
     */
    public function testIteratorImplementation() {
        $adapter = $this->getAdapterStub();
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
     * @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::current
     * @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::isEmpty
     * @covers Brickoo\Component\Cache\Adapter\Exception\PoolIsEmptyException
     * @expectedException \Brickoo\Component\Cache\Adapter\Exception\PoolIsEmptyException
     */
    public function testIteratorThrowsExceptionIfCurrentValueIsNotAvailable() {
        $adapter = $this->getAdapterStub();
        $adapterPoolIterator = new AdapterPoolIterator(["adapter" => $adapter]);
        $adapterPoolIterator->rewind();
        $this->assertSame($adapter, $adapterPoolIterator->current());
        $adapterPoolIterator->next();
        $adapterPoolIterator->current();
    }

    /** @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::select */
    public function testSelectionOfAnAdapter() {
        $adapterPoolIterator = new AdapterPoolIterator($this->getPoolEntries());
        $this->assertSame($adapterPoolIterator, $adapterPoolIterator->select("adapter_2"));
        $this->assertAttributeEquals(1, "currentPointerPosition", $adapterPoolIterator);
    }

    /**
     * @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::select
     * @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::getMappingPosition
     * @covers Brickoo\Component\Cache\Adapter\Exception\PoolIdentifierDoesNotExistException
     * @expectedException \Brickoo\Component\Cache\Adapter\Exception\PoolIdentifierDoesNotExistException
     */
    public function testSelectThrowsExceptionIfAdapterDoesNotExist() {
        $adapterPoolIterator = new AdapterPoolIterator([]);
        $adapterPoolIterator->select("some_adapter");
    }

    /**
     * @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::remove
     * @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::getMappingPosition
     */
    public function testRemoveAnAdapterFromPool() {
        $adapterPoolIterator = new AdapterPoolIterator($this->getPoolEntries());
        $this->assertEquals(2, count($adapterPoolIterator));
        $this->assertSame($adapterPoolIterator, $adapterPoolIterator->remove("adapter_2"));
        $this->assertEquals(1, count($adapterPoolIterator));
    }

    /** @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::remove */
    public function testRemoveAnAdapterMovesInternalPointer() {
        $adapterPoolIterator = new AdapterPoolIterator($this->getPoolEntries());
        $adapterPoolIterator->select("adapter_2");
        $this->assertAttributeEquals(1, "currentPointerPosition", $adapterPoolIterator);
        $adapterPoolIterator->remove("adapter_2");
        $this->assertAttributeEquals(0, "currentPointerPosition", $adapterPoolIterator);
    }

    /**
     * @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::remove
     * @covers Brickoo\Component\Cache\Adapter\Exception\PoolIdentifierDoesNotExistException
     * @expectedException \Brickoo\Component\Cache\Adapter\Exception\PoolIdentifierDoesNotExistException
     */
    public function testRemoveThrowsExceptionIfAdapterDoesNotExist() {
        $adapterPoolIterator = new AdapterPoolIterator([]);
        $adapterPoolIterator->remove("some_adapter");
    }

    /** @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::has */
    public function testHasAnAdapterInPool() {
        $adapterPoolIterator = new AdapterPoolIterator($this->getPoolEntries());
        $this->assertTrue($adapterPoolIterator->has("adapter_2"));
        $this->assertFalse($adapterPoolIterator->has("some_adapter"));
    }

    /** @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::isEmpty */
    public function testPoolIsEmpty() {
        $adapterPoolIterator = new AdapterPoolIterator([]);
        $this->assertTrue($adapterPoolIterator->isEmpty());

        $adapterPoolIterator = new AdapterPoolIterator($this->getPoolEntries());
        $this->assertFalse($adapterPoolIterator->isEmpty());
    }

    /** @covers Brickoo\Component\Cache\Adapter\AdapterPoolIterator::count     */
    public function testCountAdapterPoolEntries() {
        $adapterPoolIterator = new AdapterPoolIterator([]);
        $this->assertEquals(0, count($adapterPoolIterator));

        $adapterPoolIterator = new AdapterPoolIterator($this->getPoolEntries());
        $this->assertEquals(2, count($adapterPoolIterator));
    }

    /**
     * Returns test adapter entries.
     * @return array instancesOf \Brickoo\Component\Cache\Adapter
     */
    private function getPoolEntries() {
        $adapter = $this->getAdapterStub();
        return  ["adapter_1" => $adapter, "adapter_2" => $adapter];
    }

    /**
     * Returns a cache adapter stub.
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getAdapterStub() {
        return $this->getMock("\\Brickoo\\Component\\Cache\\Adapter\\Adapter");
    }

}
