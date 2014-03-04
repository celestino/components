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

use Brickoo\Component\Messaging\MessageRecursionDepthList,
    PHPUnit_Framework_TestCase;

/**
 * MessageRecursionDepthListTest
 *
 * Test suite for the MessageRecursionDepthList class.
 * @see Brickoo\Component\Messaging\MessageRecursionDepthList
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageRecursionDepthListTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Messaging\MessageRecursionDepthList::__construct */
    public function testConstructor() {
        $expectedRecursionDepthLimit = 100;
        $messageRecursionDepthList = new MessageRecursionDepthList($expectedRecursionDepthLimit);
        $this->assertAttributeEquals($expectedRecursionDepthLimit, "recursionDepthLimit", $messageRecursionDepthList);
    }

    /** @covers Brickoo\Component\Messaging\MessageRecursionDepthList::addMessage */
    public function testAddMessage() {
        $messageName = "test.message";
        $messageRecursionDepthList = new MessageRecursionDepthList();
        $this->assertSame($messageRecursionDepthList, $messageRecursionDepthList->addMessage($messageName));
        $this->assertAttributeEquals([$messageName => 0], "container", $messageRecursionDepthList);
    }

    /**
     * @covers Brickoo\Component\Messaging\MessageRecursionDepthList::addMessage
     * @expectedException \InvalidArgumentException
     */
    public function testAddMessageInvalidMessageNameThrowsException() {
        $messageRecursionDepthList = new MessageRecursionDepthList();
        $messageRecursionDepthList->addMessage(["wrongType"]);
    }

    /** @covers Brickoo\Component\Messaging\MessageRecursionDepthList::getRecursionDepth */
    public function testGetRecursionDepthDefaultValue() {
        $messageRecursionDepthList = new MessageRecursionDepthList();
        $this->assertEquals(0, $messageRecursionDepthList->getRecursionDepth("test.message"));
    }

    /**
     * @covers Brickoo\Component\Messaging\MessageRecursionDepthList::getRecursionDepth
     * @expectedException \InvalidArgumentException
     */
    public function testGetRecursionDepthInvalidMessageNameThrowsException() {
        $messageRecursionDepthList = new MessageRecursionDepthList();
        $messageRecursionDepthList->getRecursionDepth(["wrongType"]);
    }

    /** @covers Brickoo\Component\Messaging\MessageRecursionDepthList::isDepthLimitReached */
    public function testIsDepthLimitReached() {
        $messageName = "test.message";
        $messageRecursionDepthList = new MessageRecursionDepthList(100);
        $messageRecursionDepthList->addMessage($messageName);
        $this->assertFalse($messageRecursionDepthList->isDepthLimitReached($messageName));

        $messageRecursionDepthList = new MessageRecursionDepthList(0);
        $messageRecursionDepthList->addMessage($messageName);
        $this->assertTrue($messageRecursionDepthList->isDepthLimitReached($messageName));
    }

    /** @covers Brickoo\Component\Messaging\MessageRecursionDepthList::increaseDepth */
    public function testIncreaseDepth() {
        $messageName = "test.message";
        $messageRecursionDepthList = new MessageRecursionDepthList();
        $this->assertSame($messageRecursionDepthList, $messageRecursionDepthList->increaseDepth($messageName));
        $this->assertEquals(1, $messageRecursionDepthList->getRecursionDepth($messageName));
        $this->assertSame($messageRecursionDepthList, $messageRecursionDepthList->increaseDepth($messageName));
        $this->assertEquals(2, $messageRecursionDepthList->getRecursionDepth($messageName));
    }

    /** @covers Brickoo\Component\Messaging\MessageRecursionDepthList::decreaseDepth */
    public function testDecreaseDepth() {
        $messageName = "test.message";
        $messageRecursionDepthList = new MessageRecursionDepthList();
        $this->assertSame($messageRecursionDepthList, $messageRecursionDepthList->increaseDepth($messageName));
        $this->assertEquals(1, $messageRecursionDepthList->getRecursionDepth($messageName));
        $this->assertSame($messageRecursionDepthList, $messageRecursionDepthList->decreaseDepth($messageName));
        $this->assertEquals(0, $messageRecursionDepthList->getRecursionDepth($messageName));
    }

}