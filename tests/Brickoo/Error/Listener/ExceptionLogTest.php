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

    namespace Tests\Brickoo\Error;

    use Brickoo\Error\Listener\ExceptionLog;

    /**
     * ExceptionLogTest
     *
     * Test suite for the ExceptionLog class.
     * @see Brickoo\Error\Listener\ExceptionLog
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ExceptionLogTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Error\Listener\ExceptionLog::getEventName
         */
        public function testGetEventName() {
            $ExceptionLog = new ExceptionLog();
            $this->assertEquals(\Brickoo\Error\Events::EXCEPTION, $ExceptionLog->getEventName());
        }

        /**
         * @covers Brickoo\Error\Listener\ExceptionLog::getCondition
         */
        public function testGetConditionReturnsCallable() {
            $ExceptionLog = new ExceptionLog();
            $this->assertTrue(is_callable($ExceptionLog->getCondition()));
        }

        /**
         * @covers Brickoo\Error\Listener\ExceptionLog::getCondition
         */
        public function testGetConditionChecksExpectedEvent() {
            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $Event = new \Brickoo\Error\Event\ExceptionEvent(new \Exception("Some exception message."));

            $ExceptionLog = new ExceptionLog();
            $this->assertTrue(call_user_func_array($ExceptionLog->getCondition(), array($Event, $EventManager)));
        }

        /**
         * @covers Brickoo\Error\Listener\ExceptionLog::getCondition
         */
        public function testConditiolnWithoutExceptionEventFails() {
            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $Event = new \Brickoo\Event\Event("Some::Event");

            $ExceptionLog = new ExceptionLog();
            $this->assertFalse(call_user_func_array($ExceptionLog->getCondition(), array($Event, $EventManager)));
        }

        /**
         * @covers Brickoo\Error\Listener\ExceptionLog::getPriority
         */
        public function testGetPriority() {
            $ExceptionLog = new ExceptionLog();
            $this->assertInternalType("integer", $ExceptionLog->getPriority());
        }

        /**
         * @covers Brickoo\Error\Listener\ExceptionLog::getCallback
         */
        public function testGetCallbackReturnsCallable() {
            $ExceptionLog = new ExceptionLog();
            $this->assertTrue(is_callable($ExceptionLog->getCallback()));
        }

        /**
         * @covers Brickoo\Error\Listener\ExceptionLog::getCallback
         */
        public function testGetCallbackCallsEventManager() {
            $Exception = new \Exception("Some exception message");

            $Event = $this->getMock('Brickoo\Error\Event\ExceptionEvent', array("getException"), array($Exception));
            $Event->expects($this->once())
                  ->method("getException")
                  ->will($this->returnValue($Exception));

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $EventManager->expects($this->once())
                         ->method("notify")
                         ->with($this->isInstanceOf('Brickoo\Log\Event\Interfaces\LogEvent'))
                         ->will($this->returnValue($EventManager));

            $ExceptionLog = new ExceptionLog();
            $this->assertSame($EventManager, call_user_func_array($ExceptionLog->getCallback(), array($Event, $EventManager)));
        }

    }