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

namespace Brickoo\Tests\Component\Messaging;

use Brickoo\Component\Http\Header\GenericHeaderField;
use Brickoo\Component\Messaging\GenericMessage;
use Brickoo\Component\Messaging\ListenerCollection;
use Brickoo\Component\Messaging\MessageDispatcher;
use Brickoo\Component\Messaging\MessageRecursionDepthList;
use Brickoo\Tests\Component\Messaging\Assets\AggregatableListenerFixture;
use Brickoo\Tests\Component\Messaging\Assets\MessageListenerFixture;
use Brickoo\Tests\Component\Messaging\Assets\MessageListenerInfiniteRecursionFixture;
use PHPUnit_Framework_TestCase;

/**
 * MessageDispatcherTest
 *
 * Test suite for the MessageDispatcher class.
 * @see Brickoo\Component\Messaging\MessageDispatcher
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class MessageDispatcherTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Messaging\MessageDispatcher::__construct
     * @covers Brickoo\Component\Messaging\MessageDispatcher::attach
     * @covers Brickoo\Component\Messaging\MessageDispatcher::detach
     */
    public function testAttachAndDetachListener() {
        $messageDispatcher = new MessageDispatcher(
            new ListenerCollection(),
            new MessageRecursionDepthList()
        );
        $listenerId = $messageDispatcher->attach(new MessageListenerFixture());
        $this->assertNotEmpty($listenerId);
        $this->assertSame($messageDispatcher, $messageDispatcher->detach($listenerId));
    }

    /** @covers Brickoo\Component\Messaging\MessageDispatcher::attachAggregatedListeners */
    public function testAttachAggregatedListeners() {
        $messageDispatcher = new MessageDispatcher(
            new ListenerCollection(),
            new MessageRecursionDepthList()
        );

        $this->assertSame(
            $messageDispatcher,
            $messageDispatcher->attachAggregatedListeners(new AggregatableListenerFixture())
        );
    }

    /**
     * @covers Brickoo\Component\Messaging\MessageDispatcher::dispatch
     * @covers Brickoo\Component\Messaging\MessageDispatcher::processMessage
     */
    public function testDispatchMessage() {
        $messageDispatcher = new MessageDispatcher(
            new ListenerCollection(),
            new MessageRecursionDepthList()
        );

        $messageDispatcher->attach(new MessageListenerFixture());

        $message = new GenericMessage("message.test");
        $this->assertSame(
            $messageDispatcher,
            $messageDispatcher->dispatch($message)
        );
        $this->assertEquals("message.test processed", $message->getResponseList()->first());
    }

    /** @covers Brickoo\Component\Messaging\MessageDispatcher::dispatch */
    public function testDispatchMessageWithoutListeners() {
        $messageDispatcher = new MessageDispatcher(
            new ListenerCollection(),
            new MessageRecursionDepthList()
        );
        $this->assertSame(
            $messageDispatcher,
            $messageDispatcher->dispatch(new GenericMessage("message.test"))
        );
    }

    /**
     * @covers Brickoo\Component\Messaging\MessageDispatcher::dispatch
     * @covers Brickoo\Component\Messaging\MessageDispatcher::processMessage
     */
    public function testDispatchMessageWithStoppingListener() {
        $messageDispatcher = new MessageDispatcher(
            new ListenerCollection(),
            new MessageRecursionDepthList()
        );

        $messageDispatcher->attach(new MessageListenerFixture(true));

        $message = new GenericMessage("message.test");
        $this->assertSame(
            $messageDispatcher,
            $messageDispatcher->dispatch($message)
        );
        $this->assertSame($messageDispatcher, $messageDispatcher->dispatch($message));
    }

    /**
     * @covers Brickoo\Component\Messaging\MessageDispatcher::dispatch
     * @covers Brickoo\Component\Messaging\MessageDispatcher::processMessage
     * @covers Brickoo\Component\Messaging\Exception\MaxRecursionDepthReachedException
     * @expectedException \Brickoo\Component\Messaging\Exception\MaxRecursionDepthReachedException
     */
    public function testProcessRecursionDepthLimitIsDetected() {
        $messageDispatcher = new MessageDispatcher(
            new ListenerCollection(),
            new MessageRecursionDepthList()
        );

        $messageDispatcher->attach(new MessageListenerInfiniteRecursionFixture());

        $message = new GenericMessage("message.test");
        $this->assertSame(
            $messageDispatcher,
            $messageDispatcher->dispatch($message)
        );
        $messageDispatcher->dispatch($message);
    }

}
