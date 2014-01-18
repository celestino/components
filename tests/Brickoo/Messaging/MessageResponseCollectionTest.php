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

namespace Brickoo\Tests\Messaging;

use Brickoo\Messaging\MessageResponseCollection,
    PHPUnit_Framework_TestCase;

/**
 * MessageResponseCollectionTest
 *
 * Test suite for the Collection class.
 * @see Brickoo\Messaging\MessageResponseCollection
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageResponseCollectionTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Messaging\MessageResponseCollection::__construct */
    public function testConstructor() {
        $responsesContainer = array("response1", "response2");
        $messageResponseCollection = new MessageResponseCollection($responsesContainer);
        $this->assertAttributeEquals($responsesContainer, "responsesContainer", $messageResponseCollection);
    }

    /** @covers Brickoo\Messaging\MessageResponseCollection::shift */
    public function testShift() {
        $responsesContainer = array("response1", "response2");
        $messageResponseCollection = new MessageResponseCollection($responsesContainer);
        $this->assertEquals("response1", $messageResponseCollection->shift());
        $this->assertAttributeEquals(array("response2"), "responsesContainer", $messageResponseCollection);
    }

    /**
     * @covers Brickoo\Messaging\MessageResponseCollection::shift
     * @covers Brickoo\Messaging\Exception\ResponseNotAvailableException
     * @expectedException Brickoo\Messaging\Exception\ResponseNotAvailableException
     */
    public function testShiftEmptyListThrowsResponseNotAvailableException() {
        $messageResponseCollection = new MessageResponseCollection(array());
        $messageResponseCollection->shift();
    }

    /** @covers Brickoo\Messaging\MessageResponseCollection::pop */
    public function testPop() {
        $responsesContainer = array("response1", "response2");
        $messageResponseCollection =  new MessageResponseCollection($responsesContainer);
        $this->assertEquals("response2", $messageResponseCollection->pop());
        $this->assertAttributeEquals(array("response1"), "responsesContainer", $messageResponseCollection);
    }

    /**
     * @covers Brickoo\Messaging\MessageResponseCollection::pop
     * @covers Brickoo\Messaging\Exception\ResponseNotAvailableException
     * @expectedException Brickoo\Messaging\Exception\ResponseNotAvailableException
     */
    public function testPopEmptyListThrowsResponseNotAvailableException() {
        $messageResponseCollection = new MessageResponseCollection(array());
        $messageResponseCollection->pop();
    }

    /** @covers Brickoo\Messaging\MessageResponseCollection::getIterator */
    public function testGetIterator() {
        $messageResponseCollection =  new MessageResponseCollection(array());
        $this->assertInstanceOf("\\ArrayIterator", $messageResponseCollection->getIterator());
    }

    /** @covers Brickoo\Messaging\MessageResponseCollection::isEmpty */
    public function testIsEmpty() {
        $messageResponseCollection = new MessageResponseCollection(array());
        $this->assertTrue($messageResponseCollection->isEmpty());

        $messageResponseCollection = new MessageResponseCollection(array("response"));
        $this->assertFalse($messageResponseCollection->isEmpty());
    }

    /** @covers Brickoo\Messaging\MessageResponseCollection::count */
    public function testCount() {
        $messageResponseCollection = new MessageResponseCollection(array("r1", "r2", "r3", "r4"));
        $this->assertEquals(4, count($messageResponseCollection));
    }

}