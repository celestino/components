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

namespace Brickoo\Tests\Component\Messaging;

use Brickoo\Component\Messaging\GenericMessage;
use PHPUnit_Framework_TestCase;

/**
 * GenericMessageTest
 *
 * Test suite for the GenericMessage class.
 * @see Brickoo\Component\Messaging\GenericMessage
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class GenericMessageTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Messaging\GenericMessage::__construct */
    public function testConstructorImplementsInterface() {
        $params = ["key" => "value"];
        $message = new GenericMessage("unittest", new \stdClass(), $params);
        $this->assertInstanceOf("\\Brickoo\\Component\\Messaging\\Message", $message);
    }

    /**
     * @covers Brickoo\Component\Messaging\GenericMessage::isStopped
     * @covers Brickoo\Component\Messaging\GenericMessage::stop
     */
    public function testStopRoutine() {
        $message = new GenericMessage("test.message");
        $this->assertAttributeEquals(false, "stopped", $message);
        $this->assertFalse($message->isStopped());
        $this->assertSame($message, $message->stop());
        $this->assertAttributeEquals(true, "stopped", $message);
        $this->assertTrue($message->isStopped());
    }

    /** @covers Brickoo\Component\Messaging\GenericMessage::getName */
    public function testGetName() {
        $message = new GenericMessage("test.message");
        $this->assertEquals("test.message", $message->getName());
    }

    /** @covers Brickoo\Component\Messaging\GenericMessage::getSender */
    public function testGetSender() {
        $sender = new \stdClass();
        $message = new GenericMessage("test.message", $sender);
        $this->assertSame($sender, $message->getSender());
    }

    /**
     * @covers Brickoo\Component\Messaging\GenericMessage::setParam
     * @covers Brickoo\Component\Messaging\GenericMessage::getParams
     * @covers Brickoo\Component\Messaging\GenericMessage::getParam
     * @covers Brickoo\Component\Messaging\GenericMessage::hasParam
     */
    public function testParamsRoutines() {
        $params = ["key" => "value"];
        $message = new GenericMessage("test.message");
        $this->assertFalse($message->hasParam("key"));
        $this->assertEquals(null, $message->getParam("key"));
        $this->assertSame($message, $message->setParam("key", "value"));
        $this->assertTrue($message->hasParam("key"));
        $this->assertEquals("value", $message->getParam("key"));
        $this->assertEquals($params, $message->getParams());
    }

    /**
     * @covers Brickoo\Component\Messaging\GenericMessage::getParam
     * @expectedException \InvalidArgumentException
     */
    public function testGetParamArgumentException() {
        $message = new GenericMessage("test.message");
        $message->getParam(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Messaging\GenericMessage::hasParam
     * @expectedException \InvalidArgumentException
     */
    public function testHasParamArgumentException() {
        $message = new GenericMessage("test.message");
        $message->hasParam(["wrongType"]);
    }

    /** @covers Brickoo\Component\Messaging\GenericMessage::hasParams */
    public function testHasParams() {
        $message = new GenericMessage("test.message", null, ["id" => 1, "name" => "tester"]);
        $this->assertFalse($message->hasParams("unknown", "notAvailable"));
        $this->assertTrue($message->hasParams("id", "name"));
    }

    /** @covers Brickoo\Component\Messaging\GenericMessage::getResponse */
    public function testGetResponse() {
        $message = new GenericMessage("test.message");
        $this->assertInstanceOf("\\Brickoo\\Component\\Messaging\\MessageResponseCollection", $message->getResponse());
    }

    /** @covers Brickoo\Component\Messaging\GenericMessage::setResponse */
    public function testSetResponse() {
        $response = $this->getMockBuilder("\\Brickoo\\Component\\Messaging\\MessageResponseCollection")
            ->disableOriginalConstructor()->getMock();
        $message = new GenericMessage("test.message");
        $this->assertSame($message, $message->setResponse($response));
        $this->assertSame($response, $message->getResponse());
    }

}
