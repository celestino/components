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

use Brickoo\Component\Messaging\MessageResponseCollection,
    PHPUnit_Framework_TestCase;

/**
 * MessageResponseCollectionTest
 *
 * Test suite for the Collection class.
 * @see Brickoo\Component\Messaging\MessageResponseCollection
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageResponseCollectionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Messaging\MessageResponseCollection::__construct
     * @covers Brickoo\Component\Messaging\MessageResponseCollection::push
     * @covers Brickoo\Component\Messaging\MessageResponseCollection::count
     */
    public function testPushMessage() {
        $messageResponseCollection = new MessageResponseCollection();
        $this->assertSame($messageResponseCollection, $messageResponseCollection->push("msg"));
        $this->assertSame($messageResponseCollection, $messageResponseCollection->push("msg"));
        $this->assertEquals(2, $messageResponseCollection->count());
    }

    /** @covers Brickoo\Component\Messaging\MessageResponseCollection::shift */
    public function testShiftMessageFromList() {
        $messageResponseCollection = new MessageResponseCollection();
        $this->assertSame($messageResponseCollection, $messageResponseCollection->push("msg1"));
        $this->assertSame($messageResponseCollection, $messageResponseCollection->push("msg2"));
        $this->assertEquals(2, $messageResponseCollection->count());
        $this->assertEquals("msg1", $messageResponseCollection->shift());
        $this->assertEquals(1, $messageResponseCollection->count());
    }

    /**
     * @covers Brickoo\Component\Messaging\MessageResponseCollection::shift
     * @covers Brickoo\Component\Messaging\Exception\ResponseNotAvailableException
     * @expectedException Brickoo\Component\Messaging\Exception\ResponseNotAvailableException
     */
    public function testShiftEmptyListThrowsResponseNotAvailableException() {
        $messageResponseCollection = new MessageResponseCollection();
        $messageResponseCollection->shift();
    }

    /** @covers Brickoo\Component\Messaging\MessageResponseCollection::pop */
    public function testPopMessageFromList() {
        $messageResponseCollection =  new MessageResponseCollection();
        $this->assertSame($messageResponseCollection, $messageResponseCollection->push("msg1"));
        $this->assertSame($messageResponseCollection, $messageResponseCollection->push("msg2"));
        $this->assertEquals(2, $messageResponseCollection->count());
        $this->assertEquals("msg2", $messageResponseCollection->pop());
        $this->assertEquals(1, $messageResponseCollection->count());
    }

    /**
     * @covers Brickoo\Component\Messaging\MessageResponseCollection::pop
     * @covers Brickoo\Component\Messaging\Exception\ResponseNotAvailableException
     * @expectedException Brickoo\Component\Messaging\Exception\ResponseNotAvailableException
     */
    public function testPopEmptyListThrowsResponseNotAvailableException() {
        $messageResponseCollection = new MessageResponseCollection();
        $messageResponseCollection->pop();
    }

    /** @covers Brickoo\Component\Messaging\MessageResponseCollection::getIterator */
    public function testGetIterator() {
        $messageResponseCollection =  new MessageResponseCollection();
        $this->assertInstanceOf("\\ArrayIterator", $messageResponseCollection->getIterator());
    }

    /** @covers Brickoo\Component\Messaging\MessageResponseCollection::isEmpty */
    public function testIsEmpty() {
        $messageResponseCollection = new MessageResponseCollection();
        $this->assertTrue($messageResponseCollection->isEmpty());

        $messageResponseCollection->push("msg");
        $this->assertFalse($messageResponseCollection->isEmpty());
    }

}