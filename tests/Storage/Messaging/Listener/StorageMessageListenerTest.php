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

namespace Brickoo\Tests\Component\Storage\Messaging\Listener;

use Brickoo\Component\Storage\Messaging\Listener\StorageMessageListener;
use Brickoo\Component\Storage\Messaging\Message\DeleteMessage;
use Brickoo\Component\Storage\Messaging\Message\FlushMessage;
use Brickoo\Component\Storage\Messaging\Message\RetrieveByCallbackMessage;
use Brickoo\Component\Storage\Messaging\Message\RetrieveMessage;
use Brickoo\Component\Storage\Messaging\Message\StoreMessage;
use PHPUnit_Framework_TestCase;

/**
 * StorageMessageListenerTest
 *
 * Test suite for the StorageMessageListener class.
 * @see Brickoo\Component\Storage\Messaging\Listener\StorageMessageListener
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class StorageMessageListenerTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Storage\Messaging\Listener\StorageMessageListener::__construct
     * @covers Brickoo\Component\Storage\Messaging\Listener\StorageMessageListener::attachListeners
     * @covers Brickoo\Component\Storage\Messaging\Listener\StorageMessageListener::attachRetrieveMessageListener
     * @covers Brickoo\Component\Storage\Messaging\Listener\StorageMessageListener::attachRetrieveByCallbackMessageListener
     * @covers Brickoo\Component\Storage\Messaging\Listener\StorageMessageListener::attachStoreMessageListener
     * @covers Brickoo\Component\Storage\Messaging\Listener\StorageMessageListener::attachDeleteMessageListener
     * @covers Brickoo\Component\Storage\Messaging\Listener\StorageMessageListener::attachFlushMessageListener
     */
    public function testAttachStorageMessageListenersToMessageDispatcher() {
        $messageDispatcher = $this->getMessageDispatcherStub();
        $messageDispatcher->expects($this->exactly(5))
                          ->method("attach")
                          ->with($this->isInstanceOf("\\Brickoo\\Component\\Messaging\\Listener"));

        $cacheMessageListener = new StorageMessageListener($this->getStorageProxyStub());
        $cacheMessageListener->attachListeners($messageDispatcher);
        $this->assertInstanceOf("\\Brickoo\\Component\\Storage\\Messaging\\Listener\\StorageMessageListener", $cacheMessageListener);
    }

    /** @covers Brickoo\Component\Storage\Messaging\Listener\StorageMessageListener::handleRetrieveMessage */
    public function testHandleRetrieveMessage() {
        $cacheIdentifier = "test";
        $cachedResponse  = "cached test response";

        $storageProxy = $this->getStorageProxyStub();
        $storageProxy->expects($this->once())
                   ->method("get")
                   ->with($cacheIdentifier)
                   ->will($this->returnValue($cachedResponse));

        $message = new RetrieveMessage($cacheIdentifier);
        $cacheMessageListener = new StorageMessageListener($storageProxy);
        $this->assertEquals(
            $cachedResponse,
            $cacheMessageListener->handleRetrieveMessage($message, $this->getMessageDispatcherStub())
         );
    }

    /** @covers Brickoo\Component\Storage\Messaging\Listener\StorageMessageListener::handleRetrieveMessage */
    public function testHandleRetrieveMessageWithInvalidMessage() {
        $message = new FlushMessage();
        $cacheMessageListener = new StorageMessageListener($this->getStorageProxyStub());
        $this->assertNull($cacheMessageListener->handleRetrieveMessage($message, $this->getMessageDispatcherStub()));
    }

    /** @covers Brickoo\Component\Storage\Messaging\Listener\StorageMessageListener::handleRetrieveByCallbackMessage */
    public function testHandleRetrieveByCallbackMessage() {
        $cacheIdentifier = "test";
        $cacheCallback = function(){ /*do nothing */ };
        $callbackArguments = [];
        $lifetime = 60;
        $cachedResponse = "cached test callback response";

        $storageProxy = $this->getStorageProxyStub();
        $storageProxy->expects($this->once())
                   ->method("getByCallback")
                   ->with($cacheIdentifier, $cacheCallback, $callbackArguments, $lifetime)
                   ->will($this->returnValue($cachedResponse));

        $message = new RetrieveByCallbackMessage(
            $cacheIdentifier,
            $cacheCallback,
            $callbackArguments,
            $lifetime
        );

        $cacheMessageListener = new StorageMessageListener($storageProxy);
        $this->assertEquals(
            $cachedResponse,
            $cacheMessageListener->handleRetrieveByCallbackMessage($message, $this->getMessageDispatcherStub())
        );
    }

    /** @covers Brickoo\Component\Storage\Messaging\Listener\StorageMessageListener::handleRetrieveByCallbackMessage */
    public function testHandleRetrieveByCallbackMessageWithInvalidMessage() {
        $message = new FlushMessage();
        $cacheMessageListener = new StorageMessageListener($this->getStorageProxyStub());
        $this->assertNull($cacheMessageListener->handleRetrieveByCallbackMessage($message, $this->getMessageDispatcherStub()));
    }

    /** @covers Brickoo\Component\Storage\Messaging\Listener\StorageMessageListener::handleStoreMessage */
    public function testHandleStoreMessage() {
        $cacheIdentifier = "test";
        $content = "content to cache";
        $cacheLifetime = 60;

        $storageProxy = $this->getStorageProxyStub();
        $storageProxy->expects($this->once())
                   ->method("set")
                   ->with($cacheIdentifier, $content, $cacheLifetime)
                   ->will($this->returnSelf());

        $message = new StoreMessage(
            $cacheIdentifier,
            $content,
            $cacheLifetime
        );

        $cacheMessageListener = new StorageMessageListener($storageProxy);
        $cacheMessageListener->handleStoreMessage($message, $this->getMessageDispatcherStub());
        $this->assertInstanceOf("\\Brickoo\\Component\\Storage\\Messaging\\Listener\\StorageMessageListener", $cacheMessageListener);
    }

    /** @covers Brickoo\Component\Storage\Messaging\Listener\StorageMessageListener::handleDeleteMessage */
    public function testHandleDeleteMessage() {
        $cacheIdentifier = "test";

        $storageProxy = $this->getStorageProxyStub();
        $storageProxy->expects($this->once())
                   ->method("delete")
                   ->with($cacheIdentifier)
                   ->will($this->returnSelf());

        $message = new DeleteMessage($cacheIdentifier);
        $cacheMessageListener = new StorageMessageListener($storageProxy);
        $cacheMessageListener->handleDeleteMessage($message, $this->getMessageDispatcherStub());
        $this->assertInstanceOf("\\Brickoo\\Component\\Storage\\Messaging\\Listener\\StorageMessageListener", $cacheMessageListener);
    }

    /** @covers Brickoo\Component\Storage\Messaging\Listener\StorageMessageListener::handleFlushMessage */
    public function testHandleFlushMessage() {
        $storageProxy = $this->getStorageProxyStub();
        $storageProxy->expects($this->once())
                   ->method("flush")
                   ->will($this->returnSelf());

        $message = new FlushMessage();
        $cacheMessageListener = new StorageMessageListener($storageProxy);
        $cacheMessageListener->handleFlushMessage($message, $this->getMessageDispatcherStub());
        $this->assertInstanceOf("\\Brickoo\\Component\\Storage\\Messaging\\Listener\\StorageMessageListener", $cacheMessageListener);
    }

    /**
     * Returns a cache proxy stub.
     * @return \Brickoo\Component\Storage\StorageProxy
     */
    private function getStorageProxyStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Storage\\StorageProxy")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns an message dispatcher stub.
     * @return \Brickoo\Component\Messaging\MessageDispatcher
     */
    private function getMessageDispatcherStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Messaging\\MessageDispatcher")
            ->disableOriginalConstructor()
            ->getMock();
    }

}
