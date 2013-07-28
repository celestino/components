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

    namespace Tests\Brickoo\Log\Event;

    use Brickoo\Log\Event\LogEvent;

    /**
     * LogEventTest
     *
     * Test suite for the LogEvent class.
     * @see Brickoo\Log\Event\LogEvent
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class LogEventTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Log\Event\LogEvent::__construct
         */
        public function testConstructor() {
            $messages = array(
                "first message",
                "second message"
            );
            $severity = \Brickoo\Log\Logger::SEVERITY_EMERGENCY;

            $LogEvent = new LogEvent($messages, $severity);
            $this->assertInstanceOf('Brickoo\Log\Event\Interfaces\LogEvent', $LogEvent);
            $this->assertInstanceOf('Brickoo\Event\Interfaces\Event', $LogEvent);
            $this->assertAttributeEquals(array(LogEvent::PARAM_LOG_MESSAGES => $messages, LogEvent::PARAM_LOG_SEVERITY => $severity), "params", $LogEvent);
        }

        /**
         * @covers Brickoo\Log\Event\LogEvent::getMessages
         */
        public function testGetMessages() {
            $messages = array(
                "first message",
                "second message"
            );

            $LogEvent = new LogEvent($messages);
            $this->assertEquals($messages, $LogEvent->getMessages());
        }

        /**
         * @covers Brickoo\Log\Event\LogEvent::getSeverity
         */
        public function testGetSeverity() {
            $messages = array("Some log message");
            $severity = \Brickoo\Log\Logger::SEVERITY_EMERGENCY;
            $LogEvent = new LogEvent($messages, $severity);
            $this->assertEquals($severity, $LogEvent->getSeverity());
        }

    }