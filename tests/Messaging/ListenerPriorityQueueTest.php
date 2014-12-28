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

namespace Brickoo\Tests\Component\Messaging\Listener;

use Brickoo\Component\Messaging\ListenerPriorityQueue;
use PHPUnit_Framework_TestCase;

/**
 * ListenerPriorityQueueTest
 *
 * Test suite for the ListenerPriorityQueue class.
 * @see Brickoo\Component\Messaging\ListenerPriorityQueue
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ListenerPriorityQueueTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Messaging\ListenerPriorityQueue::__construct
     * @covers Brickoo\Component\Messaging\ListenerPriorityQueue::insert
     * @covers Brickoo\Component\Messaging\ListenerPriorityQueue::count
     */
    public function testInsertionOfQueuedValues() {
        $listenerQueue = new ListenerPriorityQueue();
        $listenerQueue->insert("A", 100);
        $listenerQueue->insert("B", 100);
        $listenerQueue->insert("C", 200);

        $this->assertEquals(3, count($listenerQueue));
        $this->assertAttributeEquals(["C" => 200, "A" => 100, "B" => 100], "items", $listenerQueue);
    }

    /**
     * @covers Brickoo\Component\Messaging\ListenerPriorityQueue::remove
     * @covers Brickoo\Component\Messaging\ListenerPriorityQueue::isEmpty
     * @covers Brickoo\Component\Messaging\ListenerPriorityQueue::count
     */
    public function testRemoveQueuedEntry() {
        $listenerQueue = new ListenerPriorityQueue();

        $listenerQueue->insert("A", 100);
        $this->assertEquals(1, count($listenerQueue));
        $this->assertFalse($listenerQueue->isEmpty());

        $listenerQueue->remove("A");
        $this->assertEquals(0, count($listenerQueue));
        $this->assertTrue($listenerQueue->isEmpty());
    }

    /** @covers Brickoo\Component\Messaging\ListenerPriorityQueue::getIterator */
    public function testRetrieveQueueIterator() {
        $listenerQueue = new ListenerPriorityQueue();
        $listenerQueue->insert("A", 100);
        $listenerQueue->insert("B", 100);
        $listenerQueue->insert("C", 200);

        $this->assertEquals(3, count($listenerQueue->getIterator()));
    }

}
