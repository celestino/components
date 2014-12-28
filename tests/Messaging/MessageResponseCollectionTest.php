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

namespace Brickoo\Tests\Component\Messaging;

use Brickoo\Component\Messaging\MessageResponseCollection;
use PHPUnit_Framework_TestCase;

/**
 * MessageResponseCollectionTest
 *
 * Test suite for the Collection class.
 * @see Brickoo\Component\Messaging\MessageResponseCollection
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageResponseCollectionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Messaging\MessageResponseCollection::__construct
     * @covers Brickoo\Component\Messaging\MessageResponseCollection::push
     * @covers Brickoo\Component\Messaging\MessageResponseCollection::count
     */
    public function testPushMessage() {
        $messageResponseCollection = new MessageResponseCollection();
        $this->assertSame($messageResponseCollection, $messageResponseCollection->push("msg"));
        $this->assertSame($messageResponseCollection, $messageResponseCollection->push("msg"));
        $this->assertEquals(2, $messageResponseCollection->count());
    }

    /** @covers Brickoo\Component\Messaging\MessageResponseCollection::shift */
    public function testShiftMessageFromList() {
        $messageResponseCollection = new MessageResponseCollection();
        $this->assertSame($messageResponseCollection, $messageResponseCollection->push("msg1"));
        $this->assertSame($messageResponseCollection, $messageResponseCollection->push("msg2"));
        $this->assertEquals(2, $messageResponseCollection->count());
        $this->assertEquals("msg1", $messageResponseCollection->shift());
        $this->assertEquals(1, $messageResponseCollection->count());
    }

    /**
     * @covers Brickoo\Component\Messaging\MessageResponseCollection::shift
     * @covers Brickoo\Component\Messaging\Exception\ResponseNotAvailableException
     * @expectedException \Brickoo\Component\Messaging\Exception\ResponseNotAvailableException
     */
    public function testShiftEmptyListThrowsResponseNotAvailableException() {
        $messageResponseCollection = new MessageResponseCollection();
        $messageResponseCollection->shift();
    }

    /** @covers Brickoo\Component\Messaging\MessageResponseCollection::pop */
    public function testPopMessageFromList() {
        $messageResponseCollection =  new MessageResponseCollection();
        $this->assertSame($messageResponseCollection, $messageResponseCollection->push("msg1"));
        $this->assertSame($messageResponseCollection, $messageResponseCollection->push("msg2"));
        $this->assertEquals(2, $messageResponseCollection->count());
        $this->assertEquals("msg2", $messageResponseCollection->pop());
        $this->assertEquals(1, $messageResponseCollection->count());
    }

    /**
     * @covers Brickoo\Component\Messaging\MessageResponseCollection::pop
     * @covers Brickoo\Component\Messaging\Exception\ResponseNotAvailableException
     * @expectedException \Brickoo\Component\Messaging\Exception\ResponseNotAvailableException
     */
    public function testPopEmptyListThrowsResponseNotAvailableException() {
        $messageResponseCollection = new MessageResponseCollection();
        $messageResponseCollection->pop();
    }

    /** @covers Brickoo\Component\Messaging\MessageResponseCollection::getIterator */
    public function testGetIterator() {
        $messageResponseCollection =  new MessageResponseCollection();
        $this->assertInstanceOf("\\ArrayIterator", $messageResponseCollection->getIterator());
    }

    /** @covers Brickoo\Component\Messaging\MessageResponseCollection::isEmpty */
    public function testIsEmpty() {
        $messageResponseCollection = new MessageResponseCollection();
        $this->assertTrue($messageResponseCollection->isEmpty());

        $messageResponseCollection->push("msg");
        $this->assertFalse($messageResponseCollection->isEmpty());
    }

}
