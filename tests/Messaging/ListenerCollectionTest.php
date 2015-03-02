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
    public function testAddingMessageListeners() {
        $messageName = "test.add.listener";
        $priority = 100;

        $listener = $this->getMessageListenerStub();
        $listener->expects($this->once())
                 ->method("getMessageName")
                 ->will($this->returnValue($messageName));
        $listener->expects($this->once())
                 ->method("getPriority")
                 ->will($this->returnValue(0));

        $listenerCollection = new ListenerCollection();
        $this->assertEquals(spl_object_hash($listener), $listenerCollection->add($listener, $priority));
        $this->assertAttributeContains($listener, "listeners", $listenerCollection);
        $this->assertAttributeCount(1, "listeners", $listenerCollection);
        $this->assertAttributeContainsOnly("\\Brickoo\\Component\\Messaging\\ListenerPriorityQueue", "listenerQueues", $listenerCollection);
        $this->assertAttributeCount(1, "listenerQueues", $listenerCollection);
    }

    /** @covers Brickoo\Component\Messaging\ListenerCollection::get */
    public function testGettingAMessageListener() {
        $messageName = "test.add.listener";
        $priority = 100;

        $listener = $this->getMessageListenerStub();
        $listener->expects($this->once())
                 ->method("getMessageName")
                 ->will($this->returnValue($messageName));

        $listenerCollection = new ListenerCollection();
        $listenerUID = $listenerCollection->add($listener, $priority);
        $this->assertSame($listener, $listenerCollection->get($listenerUID));
    }

    /**
     * @covers Brickoo\Component\Messaging\ListenerCollection::get
     * @expectedException \InvalidArgumentException
     */
    public function testGetIdentifierThrowsArgumentException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->get(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Messaging\ListenerCollection::get
     * @covers Brickoo\Component\Messaging\Exception\ListenerNotAvailableException
     * @expectedException \Brickoo\Component\Messaging\Exception\ListenerNotAvailableException
     */
    public function testGetHasNotListenerThrowsException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->get("doesNotExistIdentifier");
    }

    /** @covers Brickoo\Component\Messaging\ListenerCollection::has */
    public function testHasMessageListener() {
        $messageName = "test.add.listener";

        $listener = $this->getMessageListenerStub();
        $listener->expects($this->once())
                 ->method("getMessageName")
                 ->will($this->returnValue($messageName));
        $listener->expects($this->once())
                 ->method("getPriority")
                 ->will($this->returnValue(0));

        $listenerCollection = new ListenerCollection();
        $listenerUID = $listenerCollection->add($listener);

        $this->assertFalse($listenerCollection->has("fail"));
        $this->assertTrue($listenerCollection->has($listenerUID));
    }

    /**
     * @covers Brickoo\Component\Messaging\ListenerCollection::has
     * @expectedException \InvalidArgumentException
     */
    public function testHasIdentifierThrowsArgumentException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->has(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Messaging\ListenerCollection::remove
     * @covers Brickoo\Component\Messaging\ListenerCollection::getListenerPriorityQueue
     */
    public function testRemovingMessageListener() {
        $messageName = "test.add.listener";

        $listener = $this->getMessageListenerStub();
        $listener->expects($this->any())
                 ->method("getMessageName")
                 ->will($this->returnValue($messageName));
        $listener->expects($this->once())
                 ->method("getPriority")
                 ->will($this->returnValue(0));

        $listener2 = $this->getMessageListenerStub();
        $listener2->expects($this->any())
                  ->method("getMessageName")
                  ->will($this->returnValue($messageName));
        $listener2->expects($this->once())
                  ->method("getPriority")
                  ->will($this->returnValue(0));

        $listenerCollection = new ListenerCollection();
        $listenerUID = $listenerCollection->add($listener);
        $listenerCollection->add($listener2);

        $this->assertSame($listenerCollection, $listenerCollection->remove($listenerUID));
        $this->assertAttributeContains($listener2, "listeners", $listenerCollection);
    }

    /**
     * @covers Brickoo\Component\Messaging\ListenerCollection::remove
     * @expectedException \InvalidArgumentException
     */
    public function testRemoveIdentifierThrowsArgumentException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->remove(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Messaging\ListenerCollection::remove
     * @covers Brickoo\Component\Messaging\Exception\ListenerNotAvailableException
     * @expectedException \Brickoo\Component\Messaging\Exception\ListenerNotAvailableException
     */
    public function testRemoveListenerNotAvailableThrowsException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->remove("failIdentifier");
    }

    /**
     * @covers Brickoo\Component\Messaging\ListenerCollection::getListeners
     * @covers Brickoo\Component\Messaging\ListenerCollection::collectMessageListeners
     * @covers Brickoo\Component\Messaging\ListenerCollection::getListenerPriorityQueue
     */
    public function testGetListenersOrderedByPriority() {
        $messageName = "test.add.listener";

        $listener = $this->getMessageListenerStub();
        $listener->expects($this->any())
                 ->method("getMessageName")
                 ->will($this->returnValue($messageName));
        $listener->expects($this->once())
                 ->method("getPriority")
                 ->will($this->returnValue(0));

        $listener2 = $this->getMessageListenerStub();
        $listener2->expects($this->any())
                  ->method("getMessageName")
                  ->will($this->returnValue($messageName));
        $listener2->expects($this->once())
                  ->method("getPriority")
                  ->will($this->returnValue(0));

        $listenerCollection = new ListenerCollection();
        $listenerCollection->add($listener, 100);
        $listenerCollection->add($listener2, 500);

        $this->assertEquals(array($listener2, $listener),$listenerCollection->getListeners($messageName));
    }

    /**
     * @covers Brickoo\Component\Messaging\ListenerCollection::getListeners
     * @expectedException \InvalidArgumentException
     */
    public function testGetListenersMessageNameThrowsArgumentException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->getListeners(["wrongType"]);
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
        $messageName = "test.add.listener";
        $priority = 100;

        $listener = $this->getMessageListenerStub();
        $listener->expects($this->any())
                 ->method("getMessageName")
                 ->will($this->returnValue($messageName));
        $listener->expects($this->once())
                 ->method("getPriority")
                 ->will($this->returnValue(0));

        $listenerCollection = new ListenerCollection();

        $this->assertFalse($listenerCollection->hasListeners($messageName));
        $listenerCollection->add($listener, $priority);
        $this->assertTrue($listenerCollection->hasListeners($messageName));
    }

    /**
     * @covers Brickoo\Component\Messaging\ListenerCollection::hasListeners
     * @expectedException \InvalidArgumentException
     */
    public function testHasListenerThrowsMessageNameArgumentException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->hasListeners(["wrongType"]);
    }

    /**
     * Returns a message listener stub.
     * @return \Brickoo\Component\Messaging\MessageListener
     */
    private function getMessageListenerStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Messaging\\Listener")
            ->disableOriginalConstructor()
            ->getMock();
    }

}
