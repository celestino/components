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

namespace Brickoo\Tests\Component\Log;

use Brickoo\Component\Log\Logger,
    Brickoo\Component\Log\LogMessage;

/**
 * LogMessageTest
 *
 * Test suite for the LogMessage class.
    * @see Brickoo\Component\Log\LogMessage
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class LogMessageTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Log\LogMessage::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidSeverityThrowsException() {
        $logMessage = new LogMessage([], "wrongType");
    }

    /**
     * @covers Brickoo\Component\Log\LogMessage::__construct
     * @covers Brickoo\Component\Log\LogMessage::getMessages
     */
    public function testGetMessages() {
        $messages = ["first message", "second message"];
        $logMessage = new LogMessage($messages, Logger::SEVERITY_ALERT);
        $this->assertEquals($messages, $logMessage->getMessages());
    }

    /** @covers Brickoo\Component\Log\LogMessage::getSeverity */
    public function testGetSeverity() {
        $severity = Logger::SEVERITY_EMERGENCY;
        $logMessage = new LogMessage([], $severity);
        $this->assertEquals($severity, $logMessage->getSeverity());
    }

}