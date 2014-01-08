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

use Brickoo\Event\EventRecursionDepthList,
    PHPUnit_Framework_TestCase;

/**
 * EventDispatcherTest
 *
 * Test suite for the EventRecursionDepthList class.
 * @see Brickoo\Event\EventRecursionDepthList
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class EventRecursionDepthListTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Event\EventRecursionDepthList::__construct */
    public function testConstructor() {
        $expectedRecursionDepthLimit = 100;
        $eventRecursionDepthList = new EventRecursionDepthList($expectedRecursionDepthLimit);
        $this->assertAttributeEquals($expectedRecursionDepthLimit, "recursionDepthLimit", $eventRecursionDepthList);
    }

    /** @covers Brickoo\Event\EventRecursionDepthList::addEvent */
    public function testAddEvent() {
        $eventName = "test.event";
        $eventRecursionDepthList = new EventRecursionDepthList();
        $this->assertSame($eventRecursionDepthList, $eventRecursionDepthList->addEvent($eventName));
        $this->assertAttributeEquals([$eventName => 0], "container", $eventRecursionDepthList);
    }

    /**
     * @covers Brickoo\Event\EventRecursionDepthList::addEvent
     * @expectedException \InvalidArgumentException
     */
    public function testAddEventInvalidEventNameThrowsException() {
        $eventRecursionDepthList = new EventRecursionDepthList();
        $eventRecursionDepthList->addEvent(["wrongType"]);
    }

    /** @covers Brickoo\Event\EventRecursionDepthList::getRecursionDepth */
    public function testGetRecursionDepthDefaultValue() {
        $eventRecursionDepthList = new EventRecursionDepthList();
        $this->assertEquals(0, $eventRecursionDepthList->getRecursionDepth("test.event"));
    }

    /**
     * @covers Brickoo\Event\EventRecursionDepthList::getRecursionDepth
     * @expectedException \InvalidArgumentException
     */
    public function testGetRecursionDepthInvalidEventNameThrowsException() {
        $eventRecursionDepthList = new EventRecursionDepthList();
        $eventRecursionDepthList->getRecursionDepth(["wrongType"]);
    }

    /** @covers Brickoo\Event\EventRecursionDepthList::isDepthLimitReached */
    public function testIsDepthLimitReached() {
        $eventName = "test.event";
        $eventRecursionDepthList = new EventRecursionDepthList(100);
        $eventRecursionDepthList->addEvent($eventName);
        $this->assertFalse($eventRecursionDepthList->isDepthLimitReached($eventName));

        $eventRecursionDepthList = new EventRecursionDepthList(0);
        $eventRecursionDepthList->addEvent($eventName);
        $this->assertTrue($eventRecursionDepthList->isDepthLimitReached($eventName));
    }

    /** @covers Brickoo\Event\EventRecursionDepthList::increaseDepth */
    public function testIncreaseDepth() {
        $eventName = "test.event";
        $eventRecursionDepthList = new EventRecursionDepthList();
        $this->assertSame($eventRecursionDepthList, $eventRecursionDepthList->increaseDepth($eventName));
        $this->assertEquals(1, $eventRecursionDepthList->getRecursionDepth($eventName));
        $this->assertSame($eventRecursionDepthList, $eventRecursionDepthList->increaseDepth($eventName));
        $this->assertEquals(2, $eventRecursionDepthList->getRecursionDepth($eventName));
    }

    /** @covers Brickoo\Event\EventRecursionDepthList::decreaseDepth */
    public function testDecreaseDepth() {
        $eventName = "test.event";
        $eventRecursionDepthList = new EventRecursionDepthList();
        $this->assertSame($eventRecursionDepthList, $eventRecursionDepthList->increaseDepth($eventName));
        $this->assertEquals(1, $eventRecursionDepthList->getRecursionDepth($eventName));
        $this->assertSame($eventRecursionDepthList, $eventRecursionDepthList->decreaseDepth($eventName));
        $this->assertEquals(0, $eventRecursionDepthList->getRecursionDepth($eventName));
    }

}