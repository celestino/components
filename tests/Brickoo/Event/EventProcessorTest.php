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

use Brickoo\Event\EventProcessor,
    PHPUnit_Framework_TestCase;

/**
 * ProcessorTest
 *
 * Test suite for the Processor class.
 * @see Brickoo\Event\EventProcessor
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ProcessorTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Event\EventProcessor::process
     * @covers Brickoo\Event\EventProcessor::isValidEventCondition
     * @covers Brickoo\Event\EventProcessor::collectEventListenerResponse
     */
    public function testHandlingEventWithConditionReturnsTheEventNameAsResponse() {
        $eventName = "test.processor";
        $listenerCallback = function($event, $eventDispatcher){ return $event->getName();};

        $eventDispatcher = $this->getEventDispatcherStub();
        $event = $this->getEventStub();
        $event->expects($this->any())
              ->method("getName")
              ->will($this->returnValue($eventName));
        $event->expects($this->once())
              ->method("isStopped")
              ->will($this->returnValue(true));

        $listener = $this->getEventListenerStub();
        $listener->expects($this->once())
                 ->method("getCallback")
                 ->will($this->returnValue($listenerCallback));
        $listener->expects($this->once())
                 ->method("getCondition")
                 ->will($this->returnValue(function(){return true;}));

        $eventProcessor = new EventProcessor();
        $this->assertEquals([$eventName], $eventProcessor->process($eventDispatcher, $event, [$listener]));
    }

    /**
     * @covers Brickoo\Event\EventProcessor::process
     * @covers Brickoo\Event\EventProcessor::isValidEventCondition
     * @covers Brickoo\Event\EventProcessor::collectEventListenerResponse
     */
    public function testHandlingEventWithoutConditionReturnsTheEventNameAsResponse() {
        $eventName = "test.processor";
        $listenerCallback = function($event, $eventDispatcher){ return $event->getName();};

        $eventDispatcher = $this->getEventDispatcherStub();
        $event = $this->getEventStub();
        $event->expects($this->any())
              ->method("getName")
              ->will($this->returnValue($eventName));
        $event->expects($this->once())
              ->method("isStopped")
              ->will($this->returnValue(true));

        $listener = $this->getEventListenerStub();
        $listener->expects($this->once())
                 ->method("getCallback")
                 ->will($this->returnValue($listenerCallback));
        $listener->expects($this->once())
                 ->method("getCondition")
                 ->will($this->returnValue(null));

        $eventProcessor = new EventProcessor();
        $this->assertEquals([$eventName], $eventProcessor->process($eventDispatcher, $event, [$listener]));
    }

    /**
     * @covers Brickoo\Event\EventProcessor::process
     * @covers Brickoo\Event\EventProcessor::isValidEventCondition
     * @covers Brickoo\Event\EventProcessor::collectEventListenerResponse
     */
    public function testHandlingWithInvalidConditionReturnsEmptyResponse() {
        $eventName = "test.processor";
        $listenerCondition = function(){ return false; };

        $eventDispatcher = $this->getEventDispatcherStub();
        $event = $this->getEventStub();
        $event->expects($this->any())
              ->method("getName")
              ->will($this->returnValue($eventName));


        $listener = $this->getEventListenerStub();
        $listener->expects($this->once())
                 ->method("getCondition")
                 ->will($this->returnValue($listenerCondition));

        $eventProcessor = new EventProcessor();
        $this->assertEquals(array(), $eventProcessor->process($eventDispatcher, $event, [$listener]));
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

    /**
     * Returns an event listener stub.
     * @return \Brickoo\Event\Listener
     */
    private function getEventListenerStub() {
        return $this->getMockBuilder("\\Brickoo\\Event\\Listener")
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

}