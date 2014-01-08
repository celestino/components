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

namespace Brickoo\Tests\Event;

use Brickoo\Event\EventDispatcherBuilder,
    PHPUnit_Framework_TestCase;

/**
 * EventDispatcherBuilderTest
 *
 * Test suite for the EventDispatcherBuilder class.
 * @see Brickoo\Event\EventDispatcherBuilder
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class EventDispatcherBuilderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Event\EventDispatcherBuilder::__construct
     */
    public function testConstructor() {
        $eventDispatcherBuilder = new EventDispatcherBuilder();
        $this->assertAttributeEquals(array(), "listeners", $eventDispatcherBuilder);
    }

    /**
     * @covers Brickoo\Event\EventDispatcherBuilder::setEventProcessor
     */
    public function testSetEventProcessor() {
        $Processor = $this->getEventProcessorStub();
        $eventDispatcherBuilder = new EventDispatcherBuilder();
        $this->assertSame($eventDispatcherBuilder, $eventDispatcherBuilder->setEventProcessor($Processor));
        $this->assertAttributeSame($Processor, "eventProcessor", $eventDispatcherBuilder);
    }

    /**
     * @covers Brickoo\Event\EventDispatcherBuilder::setListenerCollection
     */
    public function testSetListenerCollection() {
        $ListenerCollection = $this->getListenerCollectionStub();
        $eventDispatcherBuilder = new EventDispatcherBuilder();
        $this->assertSame($eventDispatcherBuilder, $eventDispatcherBuilder->setListenerCollection($ListenerCollection));
        $this->assertAttributeSame($ListenerCollection, "listenerCollection", $eventDispatcherBuilder);
    }

    /**
     * @covers Brickoo\Event\EventDispatcherBuilder::setEventRecursionDepthList
     */
    public function testSetEventRecursiondepthList() {
        $EventList = $this->getEventRecursionDepthListStub();
        $eventDispatcherBuilder = new EventDispatcherBuilder();
        $this->assertSame($eventDispatcherBuilder, $eventDispatcherBuilder->setEventRecursionDepthList($EventList));
        $this->assertAttributeSame($EventList, "eventRecursionDepthList", $eventDispatcherBuilder);
    }

    /**
     * @covers Brickoo\Event\EventDispatcherBuilder::setListeners
     */
    public function testSetListenersWithArrayContainer() {
        $listeners = array(
            $this->getListenerStub(),
            $this->getListenerStub()
        );

        $eventDispatcherBuilder = new EventDispatcherBuilder();
        $this->assertSame($eventDispatcherBuilder, $eventDispatcherBuilder->setListeners($listeners));
        $this->assertAttributeEquals($listeners, "listeners", $eventDispatcherBuilder);
    }

    /**
     * @covers Brickoo\Event\EventDispatcherBuilder::setListeners
     */
    public function testSetListenersWithTraversableClass() {
        $Traversable = new \Brickoo\Memory\Container(array(
            $this->getListenerStub(),
            $this->getListenerStub()
        ));

        $eventDispatcherBuilder = new EventDispatcherBuilder();
        $this->assertSame($eventDispatcherBuilder, $eventDispatcherBuilder->setListeners($Traversable));
        $this->assertAttributeSame($Traversable, "listeners", $eventDispatcherBuilder);
    }

    /**
     * @covers Brickoo\Event\EventDispatcherBuilder::setListeners
     * @expectedException \InvalidArgumentException
     */
    public function testSetListenersThrowsInvalidArgumentException() {
        $eventDispatcherBuilder = new EventDispatcherBuilder();
        $eventDispatcherBuilder->setListeners("wrongType");
    }

    /**
     * @covers Brickoo\Event\EventDispatcherBuilder::setListeners
     * @expectedException \InvalidArgumentException
     */
    public function testSetListenersThrowsTraversableValuesException() {
        $eventDispatcherBuilder = new EventDispatcherBuilder();
        $eventDispatcherBuilder->setListeners(["wrongType"]);
    }

    /**
     * @covers Brickoo\Event\EventDispatcherBuilder::build
     * @covers Brickoo\Event\EventDispatcherBuilder::getEventProcessor
     * @covers Brickoo\Event\EventDispatcherBuilder::getListenerCollection
     * @covers Brickoo\Event\EventDispatcherBuilder::getEventList
     * @covers Brickoo\Event\EventDispatcherBuilder::attachListeners
     */
    public function testBuildAnEventManagerAutomaticly() {
        $eventDispatcherBuilder = new EventDispatcherBuilder();
        $this->assertInstanceOf("\\Brickoo\\Event\\EventDispatcher", $eventDispatcherBuilder->build());
    }

    /**
     * @covers Brickoo\Event\EventDispatcherBuilder::build
     * @covers Brickoo\Event\EventDispatcherBuilder::getEventProcessor
     * @covers Brickoo\Event\EventDispatcherBuilder::getListenerCollection
     * @covers Brickoo\Event\EventDispatcherBuilder::getEventList
     * @covers Brickoo\Event\EventDispatcherBuilder::attachListeners
     */
    public function testBuildeAnEventManagerConfigured() {
        $listenerPriority = 100;

        $Listener = $this->getListenerStub();
        $Listener->expects($this->any())
                 ->method("getPriority")
                 ->will($this->returnValue($listenerPriority));
        $Traversable = new \Brickoo\Memory\Container(array($Listener));

        $Processor = $this->getEventProcessorStub();
        $EventList = $this->getEventRecursionDepthListStub();

        $ListenerCollection = $this->getListenerCollectionStub();
        $ListenerCollection->expects($this->once())
                           ->method("add")
                           ->with($Listener, $listenerPriority);

        $eventDispatcherBuilder = new EventDispatcherBuilder();
        $eventDispatcherBuilder->setEventProcessor($Processor)
                ->setListenerCollection($ListenerCollection)
                ->setEventRecursionDepthList($EventList)
                ->setListeners($Traversable);

        $EventManager = $eventDispatcherBuilder->build();
        $this->assertAttributeSame($Processor, "processor", $EventManager);
        $this->assertAttributeSame($ListenerCollection, "listenerCollection", $EventManager);
        $this->assertAttributeSame($EventList, "eventRecursionDepthList", $EventManager);
    }

    /**
     * Returns an event processor stub.
     * @return \Brickoo\Event\EventProcessor
     */
    private function  getEventProcessorStub() {
        return $this->getMockBuilder("\\Brickoo\\Event\\EventProcessor")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a listener collection stub.
     * @return \Brickoo\Event\ListenerCollection
     */
    private function getListenerCollectionStub() {
        return $this->getMockBuilder("\\Brickoo\\Event\\ListenerCollection")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns an event listener stub.
     * @return \Brickoo\Event\Listener
     */
    private function getListenerStub() {
        return $this->getMockBuilder("\\Brickoo\\Event\\Listener")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns an event recursion depth list stub.
     * @return \Brickoo\Event\EventRecursionDepthList
     */
    private function getEventRecursionDepthListStub() {
        return $this->getMockBuilder("\\Brickoo\\Event\\EventRecursionDepthList")
            ->disableOriginalConstructor()
            ->getMock();
    }

}