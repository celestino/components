<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
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

namespace Brickoo\Tests\Event\Listener;

use Brickoo\Event\ListenerCollection,
    PHPUnit_Framework_TestCase;

/**
 * CollectionTest
 *
 * Test suite for the ListenerCollection class.
 * @see Brickoo\Event\ListenerCollection
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ListenerCollectionTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Event\ListenerCollection::__construct */
    public function testConstructor() {
        $listenerCollection = new ListenerCollection();
        $this->assertAttributeEquals(array(), "listenerQueues", $listenerCollection);
        $this->assertAttributeEquals(array(), "listeners", $listenerCollection);
    }

    /** @covers Brickoo\Event\ListenerCollection::add */
    public function testAddingEventListeners() {
        $eventName = "test.add.listener";
        $priority = 100;

        $listener = $this->getEventListenerStub();
        $listener->expects($this->once())
                 ->method("getEventName")
                 ->will($this->returnValue($eventName));

        $listenerCollection = new ListenerCollection();
        $this->assertEquals(spl_object_hash($listener), $listenerCollection->add($listener, $priority));
        $this->assertAttributeContains($listener, "listeners", $listenerCollection);
        $this->assertAttributeCount(1, "listeners", $listenerCollection);
        $this->assertAttributeContainsOnly("\\Brickoo\\Event\\ListenerQueue", "listenerQueues", $listenerCollection);
        $this->assertAttributeCount(1, "listenerQueues", $listenerCollection);
    }

    /**
     * @covers Brickoo\Event\ListenerCollection::add
     * @expectedException \InvalidArgumentException
     */
    public function testAddPriorityArgumentException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->add($this->getEventListenerStub(),["wrongType"]);
    }

    /** @covers Brickoo\Event\ListenerCollection::get */
    public function testGettingAnEventListener() {
        $eventName = "test.add.listener";
        $priority = 100;

        $listener = $this->getEventListenerStub();
        $listener->expects($this->once())
                 ->method("getEventName")
                 ->will($this->returnValue($eventName));

        $listenerCollection = new ListenerCollection();
        $listenerUID = $listenerCollection->add($listener, $priority);
        $this->assertSame($listener, $listenerCollection->get($listenerUID));
    }

    /**
     * @covers Brickoo\Event\ListenerCollection::get
     * @expectedException \InvalidArgumentException
     */
    public function testGetIdentifierThrowsArgumentException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->get(["wrongType"]);
    }

    /**
     * @covers Brickoo\Event\ListenerCollection::get
     * @covers Brickoo\Event\Exception\ListenerNotAvailableException
     * @expectedException \Brickoo\Event\Exception\ListenerNotAvailableException
     */
    public function testGetHasNotListenerThrowsException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->get("doesNotExistIdentifier");
    }

    /** @covers Brickoo\Event\ListenerCollection::has */
    public function testHasEventListener() {
        $eventName = "test.add.listener";
        $priority = 100;

        $listener = $this->getEventListenerStub();
        $listener->expects($this->once())
                 ->method("getEventName")
                 ->will($this->returnValue($eventName));

        $listenerCollection = new ListenerCollection();
        $listenerUID = $listenerCollection->add($listener, $priority);

        $this->assertFalse($listenerCollection->has("fail"));
        $this->assertTrue($listenerCollection->has($listenerUID));
    }

    /**
     * @covers Brickoo\Event\ListenerCollection::has
     * @expectedException \InvalidArgumentException
     */
    public function testHasIdentifierThrowsArgumentException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->has(["wrongType"]);
    }

    /**
     * @covers Brickoo\Event\ListenerCollection::remove
     * @covers Brickoo\Event\ListenerCollection::removeListenerFromQueue
     */
    public function testRemovingEventListener() {
        $eventName = "test.add.listener";
        $priority = 100;

        $listener = $this->getEventListenerStub();
        $listener->expects($this->any())
                 ->method("getEventName")
                 ->will($this->returnValue($eventName));
        $listener2 = $this->getEventListenerStub();
        $listener2->expects($this->any())
                  ->method("getEventName")
                  ->will($this->returnValue($eventName));

        $listenerCollection = new ListenerCollection();
        $listenerUID = $listenerCollection->add($listener, $priority);
        $listenerCollection->add($listener2, $priority);

        $this->assertSame($listenerCollection, $listenerCollection->remove($listenerUID));
        $this->assertAttributeContains($listener2, "listeners", $listenerCollection);
    }

    /**
     * @covers Brickoo\Event\ListenerCollection::remove
     * @expectedException \InvalidArgumentException
     */
    public function testRemoveIdentifierThrowsArgumentException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->remove(["wrongType"]);
    }

    /**
     * @covers Brickoo\Event\ListenerCollection::remove
     * @covers Brickoo\Event\Exception\ListenerNotAvailableException
     * @expectedException \Brickoo\Event\Exception\ListenerNotAvailableException
     */
    public function testRemoveListenerNotAvailableThrowsException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->remove("failIdentifier");
    }

    /**
     * @covers Brickoo\Event\ListenerCollection::getListeners
     * @covers Brickoo\Event\ListenerCollection::collectEventListeners
     */
    public function testGetListenersOrderedByPriority() {
        $eventName = "test.add.listener";

        $listener = $this->getEventListenerStub();
        $listener->expects($this->any())
                 ->method("getEventName")
                 ->will($this->returnValue($eventName));
        $listener2 = $this->getEventListenerStub();
        $listener2->expects($this->any())
                  ->method("getEventName")
                  ->will($this->returnValue($eventName));

        $listenerCollection = new ListenerCollection();
        $listenerCollection->add($listener, 100);
        $listenerCollection->add($listener2, 500);

        $this->assertEquals(array($listener2, $listener),$listenerCollection->getListeners($eventName));
    }

    /**
     * @covers Brickoo\Event\ListenerCollection::getListeners
     * @expectedException \InvalidArgumentException
     */
    public function testGetListenersEventNameThrowsArgumentException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->getListeners(["wrongType"]);
    }

    /**
     * @covers Brickoo\Event\ListenerCollection::getListeners
     * @covers Brickoo\Event\Exception\ListenersNotAvailableException
     * @expectedException \Brickoo\Event\Exception\ListenersNotAvailableException
     */
    public function testGetListenersNotAvailableThrowsException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->getListeners("test.get.listeners");
    }

    /** @covers Brickoo\Event\ListenerCollection::hasListeners */
    public function testHasListeners() {
        $eventName = "test.add.listener";
        $priority = 100;

        $listener = $this->getEventListenerStub();
        $listener->expects($this->any())
                 ->method("getEventName")
                 ->will($this->returnValue($eventName));

        $listenerCollection = new ListenerCollection();

        $this->assertFalse($listenerCollection->hasListeners($eventName));
        $listenerUID = $listenerCollection->add($listener, $priority);
        $this->assertTrue($listenerCollection->hasListeners($eventName));
    }

    /**
     * @covers Brickoo\Event\ListenerCollection::hasListeners
     * @expectedException \InvalidArgumentException
     */
    public function testHasListenerThrowsEventNameArgumentException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->hasListeners(["wrongType"]);
    }

    /**
     * Returns an event listener stub.
     * @return \Brickoo\Event\Listener
     */
    private function getEventListenerStub() {
        return $this->getMockBuilder("\\Brickoo\\Event\\Listener")
            ->disableOriginalConstructor()
            ->getMock();
    }

}