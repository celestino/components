<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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

namespace Brickoo\Tests\Component\Error\Messaging\Message;

use Brickoo\Component\Error\Messaging\Message\ErrorMessage;
use PHPUnit_Framework_TestCase;

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
