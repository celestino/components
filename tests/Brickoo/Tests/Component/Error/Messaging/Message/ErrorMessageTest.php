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

namespace Brickoo\Tests\Component\Error\Messaging\Message;

use Brickoo\Component\Error\Messaging\Message\ErrorMessage,
    PHPUnit_Framework_TestCase;

/**
 * ErrorMessageTest
 *
 * Test suite for the ErrorMessage class.
 * @see Brickoo\Component\Error\Messaging\Message\ErrorMessage
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ErrorMessageTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Error\Messaging\Message\ErrorMessage::__construct */
    public function testContructor() {
        $errorMessage = "An error occurred.";
        $message = new ErrorMessage($errorMessage);
        $this->assertInstanceOf("\\Brickoo\\Component\\Messaging\\Message", $message);
    }

    /**
     * @covers Brickoo\Component\Error\Messaging\Message\ErrorMessage::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorThrowsInvalidArgumentException() {
        new ErrorMessage(["wrongType"]);
    }

    /** @covers Brickoo\Component\Error\Messaging\Message\ErrorMessage::getErrorMessage */
    public function testGetErrorMessage() {
        $errorMessage = "An error occurred.";
        $message = new ErrorMessage($errorMessage);
        $this->assertEquals($errorMessage, $message->getErrorMessage());
    }

}