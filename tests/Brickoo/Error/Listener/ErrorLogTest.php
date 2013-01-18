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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    use Brickoo\Error\Listener\ErrorLog;

    /**
     * ErrorLogTest
     *
     * Test suite for the ErrorLog class.
     * @see Brickoo\Error\Listener\ErrorLog
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ErrorLogTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Error\Listener\ErrorLog::getEventName
         */
        public function testGetEventName() {
            $ErrorLog = new ErrorLog();
            $this->assertEquals(\Brickoo\Error\Events::ERROR, $ErrorLog->getEventName());
        }

        /**
         * @covers Brickoo\Error\Listener\ErrorLog::getCondition
         */
        public function testGetConditionReturnsCallable() {
            $ErrorLog = new ErrorLog();
            $this->assertTrue(is_callable($ErrorLog->getCondition()));
        }

        /**
         * @covers Brickoo\Error\Listener\ErrorLog::getCondition
         */
        public function testGetConditionChecksExpectedErrorLogEvent() {
            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $Event = new \Brickoo\Error\Event\ErrorEvent("An error occurred.");

            $ErrorLog = new ErrorLog();
            $this->assertTrue(call_user_func_array($ErrorLog->getCondition(), array($Event, $EventManager)));
        }

        /**
         * @covers Brickoo\Error\Listener\ErrorLog::getPriority
         */
        public function testGetPriority() {
            $ErrorLog = new ErrorLog();
            $this->assertInternalType("integer", $ErrorLog->getPriority());
        }

        /**
         * @covers Brickoo\Error\Listener\ErrorLog::getCallback
         */
        public function testGetCallbackReturnsCallable() {
            $ErrorLog = new ErrorLog();
            $this->assertTrue(is_callable($ErrorLog->getCallback()));
        }

        /**
         * @covers Brickoo\Error\Listener\ErrorLog::getCallback
         */
        public function testGetCallbackCallsEventManager() {
            $errorMessage = "An error occurred.";
            $errorStacktrace = "Somefile.php on line 10.";

            $Event = $this->getMock('Brickoo\Error\Event\ErrorEvent',
                array("getErrorMessage", "getErrorStacktrace"),
                array($errorMessage, $errorStacktrace)
            );
            $Event->expects($this->once())
                  ->method("getErrorMessage")
                  ->will($this->returnValue($errorMessage));
            $Event->expects($this->once())
                  ->method("getErrorStacktrace")
                  ->will($this->returnValue($errorStacktrace));

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $EventManager->expects($this->once())
                         ->method("notify")
                         ->with($this->isInstanceOf('Brickoo\Log\Event\Interfaces\LogEvent'))
                         ->will($this->returnValue($EventManager));

            $ErrorLog = new ErrorLog();
            $this->assertSame($EventManager, call_user_func_array($ErrorLog->getCallback(), array($Event, $EventManager)));
        }

    }