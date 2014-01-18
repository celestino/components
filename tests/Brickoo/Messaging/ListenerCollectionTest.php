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

namespace Brickoo\Tests\Messaging\Listener;

use Brickoo\Messaging\ListenerCollection,
    PHPUnit_Framework_TestCase;

/**
 * CollectionTest
 *
 * Test suite for the ListenerCollection class.
 * @see Brickoo\Messaging\ListenerCollection
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ListenerCollectionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Messaging\ListenerCollection::__construct
     * @covers Brickoo\Messaging\ListenerCollection::add
     */
    public function testAddingMessageListeners() {
        $eventName = "test.add.listener";
        $priority = 100;

        $listener = $this->getMessageListenerStub();
        $listener->expects($this->once())
                 ->method("getMessageName")
                 ->will($this->returnValue($eventName));
        $listener->expects($this->once())
                 ->method("getPriority")
                 ->will($this->returnValue(0));

        $listenerCollection = new ListenerCollection();
        $this->assertEquals(spl_object_hash($listener), $listenerCollection->add($listener, $priority));
        $this->assertAttributeContains($listener, "listeners", $listenerCollection);
        $this->assertAttributeCount(1, "listeners", $listenerCollection);
        $this->assertAttributeContainsOnly("\\Brickoo\\Messaging\\ListenerQueue", "listenerQueues", $listenerCollection);
        $this->assertAttributeCount(1, "listenerQueues", $listenerCollection);
    }

    /** @covers Brickoo\Messaging\ListenerCollection::get */
    public function testGettingAMessageListener() {
        $eventName = "test.add.listener";
        $priority = 100;

        $listener = $this->getMessageListenerStub();
        $listener->expects($this->once())
                 ->method("getMessageName")
                 ->will($this->returnValue($eventName));

        $listenerCollection = new ListenerCollection();
        $listenerUID = $listenerCollection->add($listener, $priority);
        $this->assertSame($listener, $listenerCollection->get($listenerUID));
    }

    /**
     * @covers Brickoo\Messaging\ListenerCollection::get
     * @expectedException \InvalidArgumentException
     */
    public function testGetIdentifierThrowsArgumentException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->get(["wrongType"]);
    }

    /**
     * @covers Brickoo\Messaging\ListenerCollection::get
     * @covers Brickoo\Messaging\Exception\ListenerNotAvailableException
     * @expectedException \Brickoo\Messaging\Exception\ListenerNotAvailableException
     */
    public function testGetHasNotListenerThrowsException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->get("doesNotExistIdentifier");
    }

    /** @covers Brickoo\Messaging\ListenerCollection::has */
    public function testHasMessageListener() {
        $eventName = "test.add.listener";

        $listener = $this->getMessageListenerStub();
        $listener->expects($this->once())
                 ->method("getMessageName")
                 ->will($this->returnValue($eventName));
        $listener->expects($this->once())
                 ->method("getPriority")
                 ->will($this->returnValue(0));

        $listenerCollection = new ListenerCollection();
        $listenerUID = $listenerCollection->add($listener);

        $this->assertFalse($listenerCollection->has("fail"));
        $this->assertTrue($listenerCollection->has($listenerUID));
    }

    /**
     * @covers Brickoo\Messaging\ListenerCollection::has
     * @expectedException \InvalidArgumentException
     */
    public function testHasIdentifierThrowsArgumentException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->has(["wrongType"]);
    }

    /**
     * @covers Brickoo\Messaging\ListenerCollection::remove
     * @covers Brickoo\Messaging\ListenerCollection::removeListenerFromQueue
     */
    public function testRemovingMessageListener() {
        $eventName = "test.add.listener";

        $listener = $this->getMessageListenerStub();
        $listener->expects($this->any())
                 ->method("getMessageName")
                 ->will($this->returnValue($eventName));
        $listener->expects($this->once())
                 ->method("getPriority")
                 ->will($this->returnValue(0));

        $listener2 = $this->getMessageListenerStub();
        $listener2->expects($this->any())
                  ->method("getMessageName")
                  ->will($this->returnValue($eventName));
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
     * @covers Brickoo\Messaging\ListenerCollection::remove
     * @expectedException \InvalidArgumentException
     */
    public function testRemoveIdentifierThrowsArgumentException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->remove(["wrongType"]);
    }

    /**
     * @covers Brickoo\Messaging\ListenerCollection::remove
     * @covers Brickoo\Messaging\Exception\ListenerNotAvailableException
     * @expectedException \Brickoo\Messaging\Exception\ListenerNotAvailableException
     */
    public function testRemoveListenerNotAvailableThrowsException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->remove("failIdentifier");
    }

    /**
     * @covers Brickoo\Messaging\ListenerCollection::getListeners
     * @covers Brickoo\Messaging\ListenerCollection::collectMessageListeners
     */
    public function testGetListenersOrderedByPriority() {
        $eventName = "test.add.listener";

        $listener = $this->getMessageListenerStub();
        $listener->expects($this->any())
                 ->method("getMessageName")
                 ->will($this->returnValue($eventName));
        $listener->expects($this->once())
                 ->method("getPriority")
                 ->will($this->returnValue(0));

        $listener2 = $this->getMessageListenerStub();
        $listener2->expects($this->any())
                  ->method("getMessageName")
                  ->will($this->returnValue($eventName));
        $listener2->expects($this->once())
                  ->method("getPriority")
                  ->will($this->returnValue(0));

        $listenerCollection = new ListenerCollection();
        $listenerCollection->add($listener, 100);
        $listenerCollection->add($listener2, 500);

        $this->assertEquals(array($listener2, $listener),$listenerCollection->getListeners($eventName));
    }

    /**
     * @covers Brickoo\Messaging\ListenerCollection::getListeners
     * @expectedException \InvalidArgumentException
     */
    public function testGetListenersMessageNameThrowsArgumentException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->getListeners(["wrongType"]);
    }

    /**
     * @covers Brickoo\Messaging\ListenerCollection::getListeners
     * @covers Brickoo\Messaging\Exception\ListenersNotAvailableException
     * @expectedException \Brickoo\Messaging\Exception\ListenersNotAvailableException
     */
    public function testGetListenersNotAvailableThrowsException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->getListeners("test.get.listeners");
    }

    /** @covers Brickoo\Messaging\ListenerCollection::hasListeners */
    public function testHasListeners() {
        $eventName = "test.add.listener";
        $priority = 100;

        $listener = $this->getMessageListenerStub();
        $listener->expects($this->any())
                 ->method("getMessageName")
                 ->will($this->returnValue($eventName));
        $listener->expects($this->once())
                 ->method("getPriority")
                 ->will($this->returnValue(0));

        $listenerCollection = new ListenerCollection();

        $this->assertFalse($listenerCollection->hasListeners($eventName));
        $listenerUID = $listenerCollection->add($listener, $priority);
        $this->assertTrue($listenerCollection->hasListeners($eventName));
    }

    /**
     * @covers Brickoo\Messaging\ListenerCollection::hasListeners
     * @expectedException \InvalidArgumentException
     */
    public function testHasListenerThrowsMessageNameArgumentException() {
        $listenerCollection = new ListenerCollection();
        $listenerCollection->hasListeners(["wrongType"]);
    }

    /**
     * Returns a message listener stub.
     * @return \Brickoo\Messaging\MessageListener
     */
    private function getMessageListenerStub() {
        return $this->getMockBuilder("\\Brickoo\\Messaging\\Listener")
            ->disableOriginalConstructor()
            ->getMock();
    }

}