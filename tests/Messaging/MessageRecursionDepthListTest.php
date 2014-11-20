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

use Brickoo\Component\Messaging\MessageRecursionDepthList;
use PHPUnit_Framework_TestCase;

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
