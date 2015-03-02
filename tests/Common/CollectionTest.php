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

namespace Brickoo\Tests\Component\Common;

use Brickoo\Component\Common\Collection;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * CollectionTest
 *
 * Test suite for the Collection class.
 * @see Brickoo\Component\Common\Collection
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class CollectionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Common\Collection::__construct
     * @covers Brickoo\Component\Common\Collection::fromArray
     * @covers Brickoo\Component\Common\Collection::add
     * @covers Brickoo\Component\Common\Collection::checkItemType
     * @covers Brickoo\Component\Common\Collection::isEmpty
     * @covers Brickoo\Component\Common\Collection::getType
     * @covers Brickoo\Component\Common\Collection::getItemType
     * @covers Brickoo\Component\Common\Exception\InvalidTypeException
     * @expectedException \Brickoo\Component\Common\Exception\InvalidTypeException
     */
    public function testCollectionCanOnlyContainOneTypeOfValues() {
        $collection = new Collection(["a", "b", "c"]);
        $this->assertEquals("string", $collection->getType());

        $collection = new Collection([new \stdClass()]);
        $this->assertEquals("stdClass", $collection->getType());

        new Collection(["a", 1, 2]);
    }

    /**
     * @covers Brickoo\Component\Common\Collection::add
     * @covers Brickoo\Component\Common\Collection::count
     */
    public function testItemCanBeAddedAndRetrievedFromCollection() {
        $collection = new Collection(["a", "b", "c"]);
        $this->assertSame($collection, $collection->add("d"));
        $this->assertEquals(4, count($collection));
    }

    /**
     * @covers Brickoo\Component\Common\Collection::shift
     * @covers Brickoo\Component\Common\Collection::pop
     */
    public function testItemCanBeRetrievedFromCollection() {
        $collection = new Collection(["a", "b", "c"]);
        $this->assertEquals("a", $collection->shift());
        $this->assertEquals("c", $collection->pop());
    }

    /** @covers Brickoo\Component\Common\Collection::contains */
    public function testCollectionContainsItem() {
        $expectedObject = new stdClass();
        $collection = new Collection([$expectedObject, new stdClass()]);
        $this->assertTrue($collection->contains($expectedObject));
        $this->assertFalse($collection->contains(new stdClass()));
    }

    /**
     * @covers Brickoo\Component\Common\Collection::shift
     * @covers Brickoo\Component\Common\Exception\CollectionEmptyException
     * @expectedException \Brickoo\Component\Common\Exception\CollectionEmptyException
     */
    public function testTryingToShiftAnItemOnEmptyStackThrowsException() {
        $collection = new Collection();
        $collection->shift();
    }

    /**
     * @covers Brickoo\Component\Common\Collection::pop
     * @covers Brickoo\Component\Common\Exception\CollectionEmptyException
     * @expectedException \Brickoo\Component\Common\Exception\CollectionEmptyException
     */
    public function testTryingToPopAnItemOnEmptyStackThrowsException() {
        $collection = new Collection();
        $collection->pop();
    }

    /**
     * @covers Brickoo\Component\Common\Collection::toArray
     * @covers Brickoo\Component\Common\Collection::getIterator
     */
    public function testCollectionItemsCanBeExported() {
        $collection = new Collection(["a", "b", "c"]);
        $this->assertEquals(["a", "b", "c"], $collection->toArray());
        $iterator = $collection->getIterator();
        $this->assertEquals(["a", "b", "c"], $iterator->getArrayCopy());
    }

}
