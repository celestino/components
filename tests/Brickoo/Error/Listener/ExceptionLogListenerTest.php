<?php

/*
 * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

namespace Brickoo\Tests\Error;

use Brickoo\Error\Events,
    Brickoo\Error\Event\ExceptionEvent,
    Brickoo\Error\Listener\ExceptionLogListener,
    Brickoo\Event\GenericEvent,
    PHPUnit_Framework_TestCase;

/**
 * ExceptionLogListenerTest
 *
 * Test suite for the ExceptionLogListener class.
 * @see Brickoo\Error\Listener\ExceptionLogListener
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ExceptionLogListenerTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Error\Listener\ExceptionLogListener::getEventName */
    public function testGetEventName() {
        $exceptionLogListener = new ExceptionLogListener();
        $this->assertEquals(Events::EXCEPTION, $exceptionLogListener->getEventName());
    }

    /** @covers Brickoo\Error\Listener\ExceptionLogListener::getCondition */
    public function testGetConditionReturnsCallable() {
        $exceptionLogListener = new ExceptionLogListener();
        $this->assertTrue(is_callable($exceptionLogListener->getCondition()));
    }

    /** @covers Brickoo\Error\Listener\ExceptionLogListener::getCondition */
    public function testGetConditionChecksExpectedExceptionEvent() {
        $exceptionLogListener = new ExceptionLogListener();
        $this->assertTrue(call_user_func_array(
            $exceptionLogListener->getCondition(),
            [new ExceptionEvent(new \Exception("Some exception message.")), $this->getEventDispatcherStub()]
        ));
    }

    /** @covers Brickoo\Error\Listener\ExceptionLogListener::getCondition */
    public function testConditiolnWithoutExceptionEventFails() {
        $exceptionLogListener = new ExceptionLogListener();
        $this->assertFalse(call_user_func_array(
            $exceptionLogListener->getCondition(),
            [new GenericEvent("test.event"), $this->getEventDispatcherStub()]
        ));
    }

    /** @covers Brickoo\Error\Listener\ExceptionLogListener::getPriority */
    public function testGetPriority() {
        $exceptionLogListener = new ExceptionLogListener();
        $this->assertInternalType("integer", $exceptionLogListener->getPriority());
    }

    /** @covers Brickoo\Error\Listener\ExceptionLogListener::getCallback */
    public function testGetCallbackReturnsCallable() {
        $exceptionLogListener = new ExceptionLogListener();
        $this->assertTrue(is_callable($exceptionLogListener->getCallback()));
    }

    /**
     * @covers Brickoo\Error\Listener\ExceptionLogListener::getCallback
     * @covers Brickoo\Error\Listener\ExceptionLogListener::getExceptionMessage
     * @covers Brickoo\Error\Listener\ExceptionLogListener::generateLogMessage
     */
    public function testGetCallbackCallsEventManager() {
        $previousExcpetion = new \Exception();
        $exception = new \Exception("Some exception message", 0, $previousExcpetion);

        $event = $this->getMockBuilder("\\Brickoo\\Error\\Event\\ExceptionEvent")
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->once())
              ->method("getException")
              ->will($this->returnValue($exception));

        $eventManager = $this->getEventDispatcherStub();
        $eventManager->expects($this->once())
                     ->method("notify")
                     ->with($this->isInstanceOf("\\Brickoo\\Log\\Event\\LogEvent"))
                     ->will($this->returnValue($eventManager));

        $exceptionLogListener = new ExceptionLogListener();
        $this->assertSame($eventManager, call_user_func_array(
            $exceptionLogListener->getCallback(), array($event, $eventManager)
        ));
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

}