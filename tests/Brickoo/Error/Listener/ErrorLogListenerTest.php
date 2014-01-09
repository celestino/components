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

namespace Brickoo\Tests\Error;

use Brickoo\Error\Events,
    Brickoo\Error\Event\ErrorEvent,
    Brickoo\Error\Listener\ErrorLogListener,
    Brickoo\Event\GenericEvent,
    PHPUnit_Framework_TestCase;

/**
 * ErrorLogListenerTest
 *
 * Test suite for the ErrorLogListener class.
 * @see Brickoo\Error\Listener\ErrorLogListener
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ErrorLogListenerListenerTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Error\Listener\ErrorLogListener::getEventName */
    public function testGetEventName() {
        $errorLogListener = new ErrorLogListener();
        $this->assertEquals(Events::ERROR, $errorLogListener->getEventName());
    }

    /** @covers Brickoo\Error\Listener\ErrorLogListener::getCondition */
    public function testGetConditionReturnsCallable() {
        $errorLogListener = new ErrorLogListener();
        $this->assertTrue(is_callable($errorLogListener->getCondition()));
    }

    /** @covers Brickoo\Error\Listener\ErrorLogListener::getCondition */
    public function testGetConditionExpectedErrorEvent() {
        $errorLogListener = new ErrorLogListener();
        $this->assertTrue(call_user_func_array(
            $errorLogListener->getCondition(),
            array(new ErrorEvent("An error occurred."), $this->getEventDispatcherStub())
        ));
    }

    /** @covers Brickoo\Error\Listener\ErrorLogListener::getCondition */
    public function testConditionWithoutErrorEventFails() {
        $errorLogListener = new ErrorLogListener();
        $this->assertFalse(call_user_func_array(
            $errorLogListener->getCondition(),
            array(new GenericEvent("error.event"), $this->getEventDispatcherStub())
        ));
    }

    /** @covers Brickoo\Error\Listener\ErrorLogListener::getPriority */
    public function testGetPriority() {
        $errorLogListener = new ErrorLogListener();
        $this->assertInternalType("integer", $errorLogListener->getPriority());
    }

    /** @covers Brickoo\Error\Listener\ErrorLogListener::getCallback */
    public function testGetCallbackReturnsCallable() {
        $errorLogListener = new ErrorLogListener();
        $this->assertTrue(is_callable($errorLogListener->getCallback()));
    }

    /** @covers Brickoo\Error\Listener\ErrorLogListener::getCallback */
    public function testGetCallbackCallsEventManager() {
        $errorMessage = "An error occurred.";
        $errorStacktrace = "Somefile.php on line 10.";

        $event = $this->getMockBuilder("\\Brickoo\\Error\\Event\\ErrorEvent")
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->once())
              ->method("getErrorMessage")
              ->will($this->returnValue($errorMessage));

        $eventDispatcher = $this->getEventDispatcherStub();
        $eventDispatcher->expects($this->once())
                        ->method("notify")
                        ->with($this->isInstanceOf("\\Brickoo\\Log\\LogEvent"))
                        ->will($this->returnValue($eventDispatcher));

        $errorLogListener = new ErrorLogListener();
        $this->assertSame($eventDispatcher, call_user_func_array(
            $errorLogListener->getCallback(), array($event, $eventDispatcher)
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