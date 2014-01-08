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

use Brickoo\Event\GenericListener,
    PHPUnit_Framework_TestCase;

/**
 * GenericListenerTest
 *
 * Test suite for the Listener class.
 * @see Brickoo\Event\GenericListener
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class GenericListenerTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Event\GenericListener::__construct */
    public function testConstructor() {
        $eventName = "test.event";
        $callback = function(){};
        $priority = 100;
        $condition = function(){return true;};

        $listener = new GenericListener($eventName, $priority, $callback, $condition);
        $this->assertAttributeEquals($eventName, "eventName", $listener);
        $this->assertAttributeEquals($callback, "callback", $listener);
        $this->assertAttributeEquals($priority, "priority", $listener);
        $this->assertAttributeEquals($condition, "condition", $listener);
    }

    /**
     * @covers Brickoo\Event\GenericListener::__construct
     * @expectedException InvalidArgumentException
     */
    public function testConstructorInvalidPriorityArgumentThrowsException() {
        $listener = new GenericListener("test.event", "wrongType", function(){});
    }

    /** @covers Brickoo\Event\GenericListener::getEventName */
    public function testGetEventName() {
        $eventName = "test.event";
        $listener = new GenericListener($eventName, 0, function(){});
        $this->assertEquals($eventName, $listener->getEventName());
    }

    /** @covers Brickoo\Event\GenericListener::getCallback */
    public function testGetCallback() {
        $callback = function(){};
        $listener = new GenericListener("test.event", 0, $callback);
        $this->assertSame($callback, $listener->getCallback());
    }

    /** @covers Brickoo\Event\GenericListener::getPriority */
    public function testGetPriority() {
        $priority = 100;
        $listener = new GenericListener("test.event", $priority, function(){});
        $this->assertEquals($priority, $listener->getPriority());
    }

    /** @covers Brickoo\Event\GenericListener::getCondition */
    public function testGetCondition() {
        $condition = function(){return true;};
        $listener = new GenericListener("test.event", 0, function(){}, $condition);
        $this->assertSame($condition, $listener->getCondition());
    }

}