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

use Brickoo\Component\Common\ArrayList;
use PHPUnit_Framework_TestCase;

/**
 * ArrayListTest
 *
 * Test suite for the ArrayList class.
 * @see Brickoo\Component\Common\ArrayList
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ArrayListTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Common\ArrayList::__construct
     * @covers Brickoo\Component\Common\ArrayList::add
     * @covers Brickoo\Component\Common\ArrayList::get
     * @covers Brickoo\Component\Common\ArrayList::indexOf
     * @covers Brickoo\Component\Common\ArrayList::isEmpty
     * @covers Brickoo\Component\Common\ArrayList::contains
     * @covers Brickoo\Component\Common\ArrayList::has
     * @covers Brickoo\Component\Common\ArrayList::count
     * @covers Brickoo\Component\Common\ArrayList::toArray
     * @covers Brickoo\Component\Common\ArrayList::remove
     */
    public function testCommonItemRoutines() {
        $item = "some value";
        $list = new ArrayList();
        $this->assertTrue($list->isEmpty());
        $list->add($item);
        $this->assertFalse($list->isEmpty());
        $this->assertTrue($list->contains($item));
        $this->assertTrue($list->has(($index = $list->indexOf($item))));
        $this->assertEquals($item, $list->get($index));
        $this->assertEquals(1, $list->count());
        $this->assertEquals([$item], $list->toArray());
        $list->remove($index);
        $this->assertTrue($list->isEmpty());
    }

    /**
     * @covers Brickoo\Component\Common\ArrayList::get
     * @covers Brickoo\Component\Common\Exception\InvalidIndexException
     * @expectedException \Brickoo\Component\Common\Exception\InvalidIndexException
     */
    public function testTryingToRetrieveAnItemByInvalidIndexThrowsException() {
        $list = new ArrayList();
        $list->get(0);
    }

    /**
     * @covers Brickoo\Component\Common\ArrayList::remove
     * @covers Brickoo\Component\Common\Exception\InvalidIndexException
     * @expectedException \Brickoo\Component\Common\Exception\InvalidIndexException
     */
    public function testTryingToRemoveAnItemByInvalidIndexThrowsException() {
        $list = new ArrayList();
        $list->remove(0);
    }

    /**
     * @covers Brickoo\Component\Common\ArrayList::first
     * @covers Brickoo\Component\Common\ArrayList::last
     */
    public function testRetrieveFirstAndLastItemsFromList() {
        $list = new ArrayList();
        $this->assertNull($list->first());
        $this->assertNull($list->last());

        $list = new ArrayList([1, 2, 3]);
        $this->assertEquals(1, $list->first());
        $this->assertEquals(3, $list->last());
    }

    /**
     * @covers Brickoo\Component\Common\ArrayList::reverse
     * @covers Brickoo\Component\Common\ArrayList::toArray
     */
    public function testListItemsOrderCanBeReverted() {
        $expectedArray = [3, 2, 1];
        $list = new ArrayList([1, 2, 3]);
        $list->reverse();
        $this->assertEquals($expectedArray, $list->toArray());
    }

    /**
     * Test is only for the purpose of code coverage only
     * since the shuffle could also retain the items order.
     * @covers Brickoo\Component\Common\ArrayList::shuffle
     * @covers Brickoo\Component\Common\ArrayList::toArray
     */
    public function testListItemsOrderCanBeShuffled() {
        $list = new ArrayList([1, 2, 3]);
        $list->shuffle();
        $this->assertTrue(true);
    }

    /**
     * @covers Brickoo\Component\Common\ArrayList::uniquify
     * @covers Brickoo\Component\Common\ArrayList::toArray
     */
    public function testListDuplicateItemsAreRemoved() {
        $expectedArray = [1, 2];
        $list = new ArrayList([1, 2, 1, 2]);
        $list->uniquify();
        $this->assertEquals($expectedArray, $list->toArray());
    }

    /** @covers Brickoo\Component\Common\ArrayList::getIterator */
    public function testListCanBeConvertedToAnIterableInstance() {
        $expectedArray = [1, 2, 3];
        $list = new ArrayList($expectedArray);
        $this->assertInstanceOf("Iterator", ($iterator = $list->getIterator()));
        $this->assertEquals($expectedArray, $iterator->getArrayCopy());
    }

    /** @covers Brickoo\Component\Common\ArrayList::toString */
    public function testListStringRepresentation() {
        $expectedString = "1\n2\n3\ntrue\nO:8:\"stdClass\":0:{}\na:0:{}";
        $list= new ArrayList([1, 2, 3, true, new \stdClass(), []]);
        $this->assertEquals($expectedString, $list->toString());
    }

}
