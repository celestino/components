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

namespace Brickoo\Tests\Component\Messaging;

use Brickoo\Component\Messaging\GenericMessage,
    PHPUnit_Framework_TestCase;

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
        $message = new GenericMessage("unittest", ($obj = new \stdClass()), $params);
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
     * @covers Brickoo\Component\Messaging\GenericMessage::getParams
     * @covers Brickoo\Component\Messaging\GenericMessage::getParam
     * @covers Brickoo\Component\Messaging\GenericMessage::hasParam
     */
    public function testParamsRoutines() {
        $params = ["key" => "value"];
        $message = new GenericMessage("test.message", null, $params);
        $this->assertFalse($message->hasParam("none"));
        $this->assertEquals(null, $message->getParam("none"));
        $this->assertTrue($message->hasParam("key"));
        $this->assertEquals("value", $message->getParam("key"));
        $this->assertEquals($params, $message->getParams());
    }

    /**
     * @covers Brickoo\Component\Messaging\GenericMessage::getParam
     * @expectedException InvalidArgumentException
     */
    public function testGetParamArgumentException() {
        $message = new GenericMessage("test.message");
        $message->getParam(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Messaging\GenericMessage::hasParam
     * @expectedException InvalidArgumentException
     */
    public function testHasParamArgumentException() {
        $message = new GenericMessage("test.message");
        $message->hasParam(["wrongType"]);
    }

    /** @covers Brickoo\Component\Messaging\GenericMessage::hasParams */
    public function testHasParams() {
        $message = new GenericMessage("test.message", null, ["id" => 1, "name" => "tester"]);
        $this->assertFalse($message->hasParams("unknowed", "notAvailable"));
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
