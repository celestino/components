<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Tests\Component\Log\Messaging\Message;

use Brickoo\Component\Log\Logger;
use Brickoo\Component\Log\Messaging\Message\LogMessage;

/**
 * LogMessageTest
 *
 * Test suite for the LogMessage class.
    * @see Brickoo\Component\Log\Messaging\Message\LogMessage
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class LogMessageTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Log\Messaging\Message\LogMessage::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidSeverityThrowsException() {
        new LogMessage([], "wrongType");
    }

    /**
     * @covers Brickoo\Component\Log\Messaging\Message\LogMessage::__construct
     * @covers Brickoo\Component\Log\Messaging\Message\LogMessage::getMessages
     */
    public function testGetMessages() {
        $messages = ["first message", "second message"];
        $logMessage = new LogMessage($messages, Logger::SEVERITY_ALERT);
        $this->assertEquals($messages, $logMessage->getMessages());
    }

    /** @covers Brickoo\Component\Log\Messaging\Message\LogMessage::getSeverity */
    public function testGetSeverity() {
        $severity = Logger::SEVERITY_EMERGENCY;
        $logMessage = new LogMessage([], $severity);
        $this->assertEquals($severity, $logMessage->getSeverity());
    }

}
