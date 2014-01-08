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

use Brickoo\Event\Event,
    Brickoo\Event\EventDispatcher,
    PHPUnit_Framework_TestCase;

/**
 * EventDispatcherTest
 *
 * Test suite for the EventDispatcher class.
 * @see Brickoo\Event\EventDispatcher
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class EventDispatcherTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Event\EventDispatcher::__construct */
    public function testContructor() {
        $eventProcessor = $this->getEventProcessorStub();
        $listenerCollection = $this->getListenerCollectionStub();
        $eventRecursionDepthList = $this->getEventRecursionDepthListStub();

        $eventDispatcher = new EventDispatcher($eventProcessor, $listenerCollection, $eventRecursionDepthList);
        $this->assertAttributeSame($eventProcessor,"processor", $eventDispatcher);
        $this->assertAttributeSame($listenerCollection, "listenerCollection", $eventDispatcher);
        $this->assertAttributeSame($eventRecursionDepthList, "eventRecursionDepthList", $eventDispatcher);
    }

    /** @covers Brickoo\Event\EventDispatcher::attach */
    public function testAttachListener() {
        $listenerUID = uniqid();
        $priority = 100;

        $listener = $this->getListenerStub();
        $listener->expects($this->once())
                 ->method("getPriority")
                 ->will($this->returnValue($priority));

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->once())
                          ->method("add")
                          ->with($listener, $priority)
                          ->will($this->returnValue($listenerUID));

        $eventDispatcher = new EventDispatcher(
            $this->getEventProcessorStub(),
            $listenerCollection,
            $this->getEventRecursionDepthListStub()
        );
        $this->assertEquals($listenerUID, $eventDispatcher->attach($listener));
    }

    /** @covers Brickoo\Event\EventDispatcher::attachAggregatedListeners */
    public function testAttachAggregatedListeners() {
        require_once "Assets/AggregatedListeners.php";

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->once())
                           ->method("add")
                           ->with($this->isInstanceOf("\\Brickoo\\Event\\Listener"), 100)
                           ->will($this->returnValue(uniqid()));

        $eventDispatcher = new EventDispatcher(
            $this->getEventProcessorStub(),
            $listenerCollection,
            $this->getEventRecursionDepthListStub()
        );

        $listener = new Assets\AggregatedListeners();
        $this->assertEquals($eventDispatcher, $eventDispatcher->attachAggregatedListeners($listener));
    }

    /** @covers Brickoo\Event\EventDispatcher::detach */
    public function testDetachListener() {
        $listenerUID = uniqid();

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->once())
                           ->method("remove")
                           ->with($listenerUID)
                           ->will($this->returnValue(true));

        $eventDispatcher = new EventDispatcher(
            $this->getEventProcessorStub(),
            $listenerCollection,
            $this->getEventRecursionDepthListStub()
        );
        $this->assertSame($eventDispatcher, $eventDispatcher->detach($listenerUID));
    }

    /**
     * @covers Brickoo\Event\EventDispatcher::detach
     * @expectedException InvalidArgumentException
     */
    public function testDetachListenerIdentifierThrowsArgumentException() {
        $eventDispatcher = new EventDispatcher(
            $this->getEventProcessorStub(),
            $this->getListenerCollectionStub(),
            $this->getEventRecursionDepthListStub()
        );
        $eventDispatcher->detach(["wrongType"]);
    }

    /**
     * @covers Brickoo\Event\EventDispatcher::notify
     * @covers Brickoo\Event\EventDispatcher::dispatch
     */
    public function testNotify() {
        $eventName = "test.event.manager.notify";

        $event = $this->getEventStub();
        $event->expects($this->any())
              ->method("getName")
              ->will($this->returnValue($eventName));

        $eventDispatcher = $this->getEventDispatcherFixture($eventName, $event);
        $this->assertSame($eventDispatcher, $eventDispatcher->notify($event));
    }

    /**
     * @covers Brickoo\Event\EventDispatcher::collect
     * @covers Brickoo\Event\EventDispatcher::dispatch
     */
    public function testCollectWithResponseCollection() {
        $expectedResult = "result of asking for response";
        $eventName = "test.event.manager.collect";

        $event = $this->getEventStub();
        $event->expects($this->any())
              ->method("getName")
              ->will($this->returnValue($eventName));

        $eventDispatcher = $this->getEventDispatcherFixture($eventName, $event, $expectedResult);
        $this->assertInstanceOf(
            "\\Brickoo\\Event\\ResponseCollection",
            ($ResponseCollection = $eventDispatcher->collect($event))
        );
        $this->assertEquals($expectedResult, $ResponseCollection->shift());
    }

    /**
     * @covers Brickoo\Event\EventDispatcher::collect
     * @covers Brickoo\Event\EventDispatcher::dispatch
     */
    public function testCollectWithEmptyResponseCollection() {
        $eventName = "test.event.manager.collect";

        $event = $this->getEventStub();
        $event->expects($this->any())
              ->method("getName")
              ->will($this->returnValue($eventName));

        $eventDispatcher = $this->getEventDispatcherFixture($eventName, $event);
        $this->assertInstanceOf(
            "\\Brickoo\\Event\\ResponseCollection",
            ($ResponseCollection = $eventDispatcher->collect($event))
        );
        $this->assertTrue($ResponseCollection->isEmpty());
    }

    /**
    * @covers Brickoo\Event\EventDispatcher::dispatch
    * @covers Brickoo\Event\Exception\MaxRecursionDepthReachedException
    * @expectedException Brickoo\Event\Exception\MaxRecursionDepthReachedException
    */
    public function testProcessRecursionDepthLimitIsDetected() {
        $eventName = "test.event.manager.infinite.loop";

        $event = $this->getEventStub();
        $event->expects($this->any())
              ->method("getName")
              ->will($this->returnValue($eventName));

        $eventRecursionDepthList = $this->getEventRecursionDepthListStub();
        $eventRecursionDepthList->expects($this->once())
                                ->method("isDepthLimitReached")
                                ->with($eventName)
                                ->will($this->returnValue(true));
        $eventRecursionDepthList->expects($this->once())
                                ->method("getRecursionDepth")
                                ->with($eventName)
                                ->will($this->returnValue(10));

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->any())
                           ->method("hasListeners")
                           ->will($this->returnValue(true));

        $eventDispatcher = new EventDispatcher(
            $this->getEventProcessorStub(),
            $listenerCollection,
            $eventRecursionDepthList
        );
        $eventDispatcher->notify($event);
    }

    /** @covers Brickoo\Event\EventDispatcher::dispatch */
    public function testNotificationWithoutListenersDoesSuccess() {
        $eventName = "test.event.manager.notify";

        $event = $this->getEventStub();
        $event->expects($this->any())
              ->method("getName")
              ->will($this->returnValue($eventName));

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->any())
                           ->method("hasListeners")
                           ->will($this->returnValue(false));

        $eventDispatcher = new EventDispatcher(
            $this->getEventProcessorStub(),
            $listenerCollection,
            $this->getEventRecursionDepthListStub()
        );
        $this->assertSame($eventDispatcher, $eventDispatcher->notify($event));
    }

    /** @covers Brickoo\Event\EventDispatcher::dispatch */
    public function testResponseCollectionIsReturnedWithoutHavingListeners() {
        $eventName = "test.event.manager.collect";

        $event = $this->getEventStub();
        $event->expects($this->any())
              ->method("getName")
              ->will($this->returnValue($eventName));

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->any())
                           ->method("hasListeners")
                           ->will($this->returnValue(false));

        $eventDispatcher = new EventDispatcher(
            $this->getEventProcessorStub(),
            $listenerCollection,
            $this->getEventRecursionDepthListStub()
        );
        $this->assertInstanceOf("\\Brickoo\\Event\\ResponseCollection", $eventDispatcher->collect($event));
    }

    /**
     * Returns an event dispatcher fixture configured with the arguments.
     * @param string $eventName the event name
     * @param \Brickoo\Event\Event $event the event triggered
     * @param string|null $expectedResult the expected processor result
     * @return \Brickoo\Event\EventDispatcher
     */
    private function getEventDispatcherFixture($eventName, Event $event, $expectedResult = null) {
        $expectedResult = empty($expectedResult) ? [] : [$expectedResult];
        $listener = $this->getListenerStub();
        $eventProcessor = $this->getEventProcessorStub();

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->any())
                           ->method("hasListeners")
                           ->will($this->returnValue(true));
        $listenerCollection->expects($this->any())
                           ->method("getListeners")
                           ->will($this->returnValue([$listener]));

        $eventRecursionDepthList = $this->getEventRecursionDepthListStub();
        $eventRecursionDepthList->expects($this->once())
                  ->method("isDepthLimitReached")
                  ->with($eventName)
                  ->will($this->returnValue(false));
        $eventRecursionDepthList->expects($this->once())
                  ->method("increaseDepth")
                  ->with($eventName)
                  ->will($this->returnSelf());
        $eventRecursionDepthList->expects($this->once())
                  ->method("decreaseDepth")
                  ->with($eventName)
                  ->will($this->returnSelf());

        $eventDispatcher = new EventDispatcher($eventProcessor, $listenerCollection, $eventRecursionDepthList);

        $eventProcessor->expects($this->once())
                  ->method("process")
                  ->with($eventDispatcher, $event, [$listener])
                  ->will($this->returnValue($expectedResult));

        return $eventDispatcher;
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

    /**
     * Returns an event stub.
     * @return \Brickoo\Event\Event
     */
    private function getEventStub() {
        return $this->getMockBuilder("\\Brickoo\\Event\\Event")
            ->disableOriginalConstructor()
            ->getMock();
    }

}