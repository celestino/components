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

namespace Brickoo\Tests\Log;

use Brickoo\Log\Events,
    Brickoo\Log\Logger,
    Brickoo\Log\LogListener;

/**
 * LogListenerTest
 *
 * Test suite for the LogListener class.
 * @see Brickoo\Log\LogListener
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class LogListenerTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Log\LogListener::__construct
     * @expectedException InvalidArgumentException
     */
    public function testConstructorThrowsArgumentException() {
        $listener = new LogListener($this->getLoggerStub(), "wrongType");
    }

    /**
     * @covers Brickoo\Log\LogListener::__construct
     * @covers Brickoo\Log\LogListener::getEventName
     */
    public function testGetEventName() {
        $listener = new LogListener($this->getLoggerStub(), 100);
        $this->assertEquals(Events::LOG, $listener->getEventName());
    }

    /** @covers Brickoo\Log\LogListener::getPriority */
    public function testGetPriority() {
        $priority = 99;
        $listener = new LogListener($this->getLoggerStub(), $priority);
        $this->assertEquals($priority, $listener->getPriority());
    }

    /** @covers Brickoo\Log\LogListener::getCondition */
    public function testGetConditionAllowsOnlyLogEvent() {
        $listener = new LogListener($this->getLoggerStub(), 100);
        $this->assertInstanceOf("\Closure", ($condition =$listener->getCondition()));
        $this->assertFalse($condition(
            $this->getMock("\\Brickoo\\Event\\Event"),
            $this->getEventDispatcherStub()
        ));
        $this->assertTrue($condition(
            $this->getLogEventStub(),
            $this->getEventDispatcherStub()
        ));
    }

    /** @covers Brickoo\Log\LogListener::getCallback */
    public function testGetCallbackIsCallable() {
        $listener = new LogListener($this->getLoggerStub(), 100);
        $this->assertTrue(is_callable($listener->getCallback()));
    }

    /** @covers Brickoo\Log\LogListener::handleLogEvent */
    public function testHandleLogEvent() {
        $messages = array("log this test message");
        $severity = Logger::SEVERITY_INFO;

        $logger = $this->getLoggerStub();
        $logger->expects($this->once())
               ->method("log")
               ->with($messages, $severity);

        $eventDispatcher = $this->getEventDispatcherStub();
        $event = $this->getLogEventStub();
        $event->expects($this->once())
              ->method("getMessages")
              ->will($this->returnValue($messages));
        $event->expects($this->once())
              ->method("getSeverity")
              ->will($this->returnValue($severity));


        $listener = new LogListener($logger, 100);
        $listener->handleLogEvent($event, $eventDispatcher);
    }

    /**
     * Returns a logger stub.
     * @return \Brickoo\Log\LogListener
     */
    private function getLoggerStub() {
        return $this->getMockBuilder("\\Brickoo\\Log\\Logger")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns an event dispatcher stub.
     * @return \Brickoo\Event\EventDispatcher
     */
    private function getEventDispatcherStub() {
        return $this->getMockBuilder("\\Brickoo\\Event\\EventDispatcher")
            ->disableOriginalConstructor()
            ->getMock();

    }

    /**
     * Returns an event stub.
     * @return \Brickoo\Log\LogEvent
     */
    private function getLogEventStub() {
        return $this->getMockBuilder("\\Brickoo\\Log\\LogEvent")
            ->disableOriginalConstructor()
            ->getMock();
    }

}