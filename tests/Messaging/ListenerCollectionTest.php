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

use Brickoo\Component\Messaging\ListenerCollection;
use Brickoo\Component\Messaging\MessageListener;
use PHPUnit_Framework_TestCase;

/**
 * CollectionTest
 *
 * Test suite for the ListenerCollection class.
 * @see Brickoo\Component\Messaging\ListenerCollection
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ListenerCollectionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Messaging\ListenerCollection::__construct
     * @covers Brickoo\Component\Messaging\ListenerCollection::add
     */
    public function testCommonListenerRoutines() {
        $listener = new MessageListener("unit-test", 100, function() {});

        $listenerCollection = new ListenerCollection();
        $this->assertEquals(spl_object_hash($listener), ($listenerUID = $listenerCollection->add($listener)));
        $this->assertSame($listener, $listenerCollection->get($listenerUID));
        $this->assertFalse($listenerCollection->has("unknownUID"));
        $this->assertTrue($listenerCollection->has($listenerUID));
        $this->assertSame($listenerCollection, $listenerCollection->remove($listenerUID));
        $this->assertFalse($listenerCollection->has($listenerUID));
    }

    /**
     * @covers Brickoo\Component\Messaging\ListenerCollection::get
     * @covers Brickoo\Component\Messaging\Exception\ListenerNotAvailableException
     * @expectedException \Brickoo\Component\Messaging\Exception\ListenerNotAvailableException
     */
    public function testTryingToGetUnknownListenerThrowsException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->get("doesNotExistIdentifier");
    }

    /**
     * @covers Brickoo\Component\Messaging\ListenerCollection::remove
     * @covers Brickoo\Component\Messaging\Exception\ListenerNotAvailableException
     * @expectedException \Brickoo\Component\Messaging\Exception\ListenerNotAvailableException
     */
    public function testTryingToRemoveUnknownListenerThrowsException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->remove("failIdentifier");
    }

    /**
     * @covers Brickoo\Component\Messaging\ListenerCollection::getListeners
     * @covers Brickoo\Component\Messaging\ListenerCollection::collectMessageListeners
     * @covers Brickoo\Component\Messaging\ListenerCollection::getListenerPriorityQueue
     */
    public function testGetMessageListenersOrderedByPriority() {
        $messageName = "test.listener";

        $listener1 = new MessageListener($messageName, 50, function() {});
        $listener2 = new MessageListener($messageName, 100, function() {});

        $listenerCollection = new ListenerCollection();
        $listenerCollection->add($listener1);
        $listenerCollection->add($listener2);

        $listeners = $listenerCollection->getListeners($messageName);
        $this->assertSame($listener2, $listeners[0]);
        $this->assertSame($listener1, $listeners[1]);
    }

    /**
     * @covers Brickoo\Component\Messaging\ListenerCollection::getListeners
     * @covers Brickoo\Component\Messaging\Exception\ListenersNotAvailableException
     * @expectedException \Brickoo\Component\Messaging\Exception\ListenersNotAvailableException
     */
    public function testGetListenersNotAvailableThrowsException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->getListeners("test.get.listeners");
    }

    /** @covers Brickoo\Component\Messaging\ListenerCollection::hasListeners */
    public function testHasListeners() {
        $messageName = "test.listener";
        $listenerCollection = new ListenerCollection();
        $this->assertFalse($listenerCollection->hasListeners($messageName));
        $listenerCollection->add(new MessageListener($messageName, 0, function() {}));
        $this->assertTrue($listenerCollection->hasListeners($messageName));
    }

}
