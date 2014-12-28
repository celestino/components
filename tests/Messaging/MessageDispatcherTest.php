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

use Brickoo\Component\Messaging\MessageDispatcher;
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
     */
    public function testAttachListener() {
        $listenerUID = uniqid();
        $listener = $this->getListenerStub();

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->once())
                          ->method("add")
                          ->with($listener)
                          ->will($this->returnValue($listenerUID));

        $messageDispatcher = new MessageDispatcher(
            $listenerCollection,
            $this->getMessageRecursionDepthListStub()
        );
        $this->assertEquals($listenerUID, $messageDispatcher->attach($listener));
    }

    /** @covers Brickoo\Component\Messaging\MessageDispatcher::attachAggregatedListeners */
    public function testAttachAggregatedListeners() {
        require_once "Assets/AggregatedListeners.php";

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->once())
                           ->method("add")
                           ->with($this->isInstanceOf("\\Brickoo\\Component\\Messaging\\Listener"))
                           ->will($this->returnValue(uniqid()));

        $messageDispatcher = new MessageDispatcher(
            $listenerCollection,
            $this->getMessageRecursionDepthListStub()
        );

        $listener = new Assets\AggregatedListeners();
        $this->assertEquals($messageDispatcher, $messageDispatcher->attachAggregatedListeners($listener));
    }

    /** @covers Brickoo\Component\Messaging\MessageDispatcher::detach */
    public function testDetachListener() {
        $listenerUID = uniqid();

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->once())
                           ->method("remove")
                           ->with($listenerUID)
                           ->will($this->returnValue(true));

        $messageDispatcher = new MessageDispatcher(
            $listenerCollection,
            $this->getMessageRecursionDepthListStub()
        );
        $this->assertSame($messageDispatcher, $messageDispatcher->detach($listenerUID));
    }

    /**
     * @covers Brickoo\Component\Messaging\MessageDispatcher::detach
     * @expectedException \InvalidArgumentException
     */
    public function testDetachListenerIdentifierThrowsArgumentException() {
        $messageDispatcher = new MessageDispatcher(
            $this->getListenerCollectionStub(),
            $this->getMessageRecursionDepthListStub()
        );
        $messageDispatcher->detach(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Messaging\MessageDispatcher::dispatch
     * @covers Brickoo\Component\Messaging\MessageDispatcher::processMessage
     */
    public function testDispatch() {
        $messageName = "test.message.manager.notify";

        $message = $this->getMessageStub();
        $message->expects($this->any())
                ->method("getName")
                ->will($this->returnValue($messageName));
        $message->expects($this->any())
                ->method("getResponse")
                ->will($this->returnValue($this->getMessageResponseStub()));

        $messageDispatcher = $this->getMessageDispatcherFixture($messageName);
        $this->assertSame($messageDispatcher, $messageDispatcher->dispatch($message));
    }

    /** @covers Brickoo\Component\Messaging\MessageDispatcher::dispatch */
    public function testDispatchWithoutListeners() {
        $messageName = "test.message.manager.notify";

        $message = $this->getMessageStub();
        $message->expects($this->any())
              ->method("getName")
              ->will($this->returnValue($messageName));
        $message->expects($this->any())
                ->method("getResponse")
                ->will($this->returnValue($this->getMessageResponseStub()));

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->once())
                           ->method("hasListeners")
                           ->with($messageName)
                           ->will($this->returnValue(false));

        $messageDispatcher = new MessageDispatcher(
            $listenerCollection,
            $this->getMessageRecursionDepthListStub()
        );
        $this->assertSame($messageDispatcher, $messageDispatcher->dispatch($message));
    }

    /**
     * @covers Brickoo\Component\Messaging\MessageDispatcher::dispatch
     * @covers Brickoo\Component\Messaging\MessageDispatcher::processMessage
     */
    public function testDispatchWithRespondingListeners() {
        $messageName = "test.message.manager.notify";

        $message = $this->getMessageStub();
        $message->expects($this->any())
                ->method("getName")
                ->will($this->returnValue($messageName));
        $message->expects($this->any())
                ->method("isStopped")
                ->will($this->returnValue(true));
        $message->expects($this->any())
                ->method("getResponse")
                ->will($this->returnValue($this->getMessageResponseStub()));

        $listenerCollection = $this->getListenerCollectionStub();

        $messageRecursionDepthList = $this->getMessageRecursionDepthListStub();
        $messageRecursionDepthList->expects($this->once())
                                  ->method("isDepthLimitReached")
                                  ->with($messageName)
                                  ->will($this->returnValue(false));

        $messageDispatcher = new MessageDispatcher($listenerCollection, $messageRecursionDepthList);

        $listener = $this->getListenerStub();
        $listener->expects($this->any())
                 ->method("handleMessage")
                 ->with($message, $messageDispatcher)
                 ->will($this->returnValue("response"));

        $listenerCollection->expects($this->once())
                           ->method("hasListeners")
                           ->with($messageName)
                           ->will($this->returnValue(true));
        $listenerCollection->expects($this->once())
                           ->method("getListeners")
                           ->with($messageName)
                           ->will($this->returnValue([$listener]));

        $this->assertSame($messageDispatcher, $messageDispatcher->dispatch($message));
    }

    /**
     * @covers Brickoo\Component\Messaging\MessageDispatcher::dispatch
     * @covers Brickoo\Component\Messaging\MessageDispatcher::processMessage
     * @covers Brickoo\Component\Messaging\Exception\MaxRecursionDepthReachedException
     * @expectedException \Brickoo\Component\Messaging\Exception\MaxRecursionDepthReachedException
     */
    public function testProcessRecursionDepthLimitIsDetected() {
        $messageName = "test.message.manager.infinite.loop";

        $message = $this->getMessageStub();
        $message->expects($this->any())
              ->method("getName")
              ->will($this->returnValue($messageName));
        $message->expects($this->any())
                ->method("getResponse")
                ->will($this->returnValue($this->getMessageResponseStub()));

        $messageRecursionDepthList = $this->getMessageRecursionDepthListStub();
        $messageRecursionDepthList->expects($this->once())
                                ->method("isDepthLimitReached")
                                ->with($messageName)
                                ->will($this->returnValue(true));
        $messageRecursionDepthList->expects($this->once())
                                ->method("getRecursionDepth")
                                ->with($messageName)
                                ->will($this->returnValue(10));

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->any())
                           ->method("hasListeners")
                           ->will($this->returnValue(true));

        $messageDispatcher = new MessageDispatcher(
            $listenerCollection,
            $messageRecursionDepthList
        );
        $messageDispatcher->dispatch($message);
    }

    /**
     * Returns an message dispatcher fixture configured with the arguments.
     * @param string $messageName the message name
     * @return \Brickoo\Component\Messaging\MessageDispatcher
     */
    private function getMessageDispatcherFixture($messageName) {
        $listener = $this->getListenerStub();

        $listenerCollection = $this->getListenerCollectionStub();
        $listenerCollection->expects($this->any())
                           ->method("hasListeners")
                           ->will($this->returnValue(true));
        $listenerCollection->expects($this->any())
                           ->method("getListeners")
                           ->will($this->returnValue([$listener]));

        $messageRecursionDepthList = $this->getMessageRecursionDepthListStub();
        $messageRecursionDepthList->expects($this->once())
                  ->method("isDepthLimitReached")
                  ->with($messageName)
                  ->will($this->returnValue(false));
        $messageRecursionDepthList->expects($this->once())
                  ->method("increaseDepth")
                  ->with($messageName)
                  ->will($this->returnSelf());
        $messageRecursionDepthList->expects($this->once())
                  ->method("decreaseDepth")
                  ->with($messageName)
                  ->will($this->returnSelf());

        return new MessageDispatcher($listenerCollection, $messageRecursionDepthList);
    }

    /**
     * Returns a listener collection stub.
     * @return \Brickoo\Component\Messaging\ListenerCollection
     */
    private function getListenerCollectionStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Messaging\\ListenerCollection")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a message listener stub.
     * @return \Brickoo\Component\Messaging\Listener
     */
    private function getListenerStub() {
        return $this->getMock("\\Brickoo\\Component\\Messaging\\Listener");
    }

    /**
     * Returns a message recursion depth list stub.
     * @return \Brickoo\Component\Messaging\MessageRecursionDepthList
     */
    private function getMessageRecursionDepthListStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Messaging\\MessageRecursionDepthList")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a message stub.
     * @return \Brickoo\Component\Messaging\Message
     */
        private function getMessageStub() {
        return $this->getMock("\\Brickoo\\Component\\Messaging\\Message");
    }

    /**
     * Returns a message response collection stub.
     * @return \Brickoo\Component\Messaging\MessageResponseCollection
     */
    private function getMessageResponseStub() {
        return $this->getMock("\\Brickoo\Component\Messaging\\MessageResponseCollection");
    }

}
