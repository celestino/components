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

use Brickoo\Event\GenericEvent,
    PHPUnit_Framework_TestCase;

/**
 * GenericEventTest
 *
 * Test suite for the GenericEvent class.
 * @see Brickoo\Event\GenericEvent
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class GenericEventTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Event\GenericEvent::__construct */
    public function testConstructor() {
        $params = ["key" => "value"];
        $event = new GenericEvent("unittest", ($obj = new \stdClass()), $params);
        $this->assertInstanceOf("\\Brickoo\\Event\\GenericEvent", $event);
        $this->assertAttributeEquals("unittest", "name", $event);
        $this->assertAttributeSame($obj, "sender", $event);
        $this->assertAttributeEquals($params, "params", $event);
    }

    /**
     * @covers Brickoo\Event\GenericEvent::isStopped
     * @covers Brickoo\Event\GenericEvent::stop
     */
    public function testStopRoutine() {
        $event = new GenericEvent("test.event");
        $this->assertAttributeEquals(false, "stopped", $event);
        $this->assertFalse($event->isStopped());
        $this->assertSame($event, $event->stop());
        $this->assertAttributeEquals(true, "stopped", $event);
        $this->assertTrue($event->isStopped());
    }

    /** @covers Brickoo\Event\GenericEvent::getName */
    public function testGetName() {
        $event = new GenericEvent("test.event");
        $this->assertEquals("test.event", $event->getName());
    }

    /** @covers Brickoo\Event\GenericEvent::getSender */
    public function testGetSender() {
        $sender = new \stdClass();
        $event = new GenericEvent("test.event", $sender);
        $this->assertSame($sender, $event->getSender());
    }

    /**
     * @covers Brickoo\Event\GenericEvent::getParams
     * @covers Brickoo\Event\GenericEvent::getParam
     * @covers Brickoo\Event\GenericEvent::hasParam
     */
    public function testParamsRoutines() {
        $params = ["key" => "value"];
        $event = new GenericEvent("test.event", null, $params);
        $this->assertFalse($event->hasParam("none"));
        $this->assertEquals(null, $event->getParam("none"));
        $this->assertTrue($event->hasParam("key"));
        $this->assertEquals("value", $event->getParam("key"));
        $this->assertEquals($params, $event->getParams());
    }

    /**
     * @covers Brickoo\Event\GenericEvent::getParam
     * @expectedException InvalidArgumentException
     */
    public function testGetParamArgumentException() {
        $event = new GenericEvent("test.event");
        $event->getParam(["wrongType"]);
    }

    /**
     * @covers Brickoo\Event\GenericEvent::hasParam
     * @expectedException InvalidArgumentException
     */
    public function testHasParamArgumentException() {
        $event = new GenericEvent("test.event");
        $event->hasParam(["wrongType"]);
    }

    /** @covers Brickoo\Event\GenericEvent::hasParams */
    public function testHasParams() {
        $event = new GenericEvent("test.event", null, ["id" => 1, "name" => "tester"]);
        $this->assertFalse($event->hasParams("unknowed", "notAvailable"));
        $this->assertTrue($event->hasParams("id", "name"));
    }

    /** @covers Brickoo\Event\GenericEvent::getSender */
    public function AtestGetSender() {
        $event = new GenericEvent("test.event", ($Sender = new \stdClass()));
        $this->assertAttributeSame($Sender, "Sender", $event);
        $this->assertSame($Sender, $event->getSender());
    }

}
