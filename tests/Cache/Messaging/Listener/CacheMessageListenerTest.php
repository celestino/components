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

namespace Brickoo\Tests\Component\Cache\Messaging\Listener;

use Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener;
use Brickoo\Component\Cache\Messaging\Message\DeleteMessage;
use Brickoo\Component\Cache\Messaging\Message\FlushMessage;
use Brickoo\Component\Cache\Messaging\Message\RetrieveByCallbackMessage;
use Brickoo\Component\Cache\Messaging\Message\RetrieveMessage;
use Brickoo\Component\Cache\Messaging\Message\StoreMessage;
use PHPUnit_Framework_TestCase;

/**
 * CacheMessageListenerTest
 *
 * Test suite for the CacheMessageListener class.
 * @see Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class CacheMessageListenerTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::__construct
     * @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::attachListeners
     * @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::attachRetrieveMessageListener
     * @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::attachRetrieveByCallbackMessageListener
     * @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::attachStoreMessageListener
     * @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::attachDeleteMessageListener
     * @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::attachFlushMessageListener
     */
    public function testAttachCacheMessageListenersToMessageDispatcher() {
        $messageDispatcher = $this->getMessageDispatcherStub();
        $messageDispatcher->expects($this->exactly(5))
                          ->method("attach")
                          ->with($this->isInstanceOf("\\Brickoo\\Component\\Messaging\\Listener"));

        $cacheMessageListener = new CacheMessageListener($this->getCacheProxyStub());
        $cacheMessageListener->attachListeners($messageDispatcher);
        $this->assertInstanceOf("\\Brickoo\\Component\\Cache\\Messaging\\Listener\\CacheMessageListener", $cacheMessageListener);
    }

    /** @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::handleRetrieveMessage */
    public function testHandleRetrieveMessage() {
        $cacheIdentifier = "test";
        $cachedResponse  = "cached test response";

        $cacheProxy = $this->getCacheProxyStub();
        $cacheProxy->expects($this->once())
                   ->method("get")
                   ->with($cacheIdentifier)
                   ->will($this->returnValue($cachedResponse));

        $message = new RetrieveMessage($cacheIdentifier);
        $cacheMessageListener = new CacheMessageListener($cacheProxy);
        $this->assertEquals(
            $cachedResponse,
            $cacheMessageListener->handleRetrieveMessage($message, $this->getMessageDispatcherStub())
         );
    }

    /** @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::handleRetrieveMessage */
    public function testHandleRetrieveMessageWithInvalidMessage() {
        $message = new FlushMessage();
        $cacheMessageListener = new CacheMessageListener($this->getCacheProxyStub());
        $this->assertNull($cacheMessageListener->handleRetrieveMessage($message, $this->getMessageDispatcherStub()));
    }

    /** @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::handleRetrieveByCallbackMessage */
    public function testHandleRetrieveByCallbackMessage() {
        $cacheIdentifier = "test";
        $cacheCallback = function(){ /*do nothing */ };
        $callbackArguments = [];
        $lifetime = 60;
        $cachedResponse = "cached test callback response";

        $cacheProxy = $this->getCacheProxyStub();
        $cacheProxy->expects($this->once())
                   ->method("getByCallback")
                   ->with($cacheIdentifier, $cacheCallback, $callbackArguments, $lifetime)
                   ->will($this->returnValue($cachedResponse));

        $message = new RetrieveByCallbackMessage(
            $cacheIdentifier,
            $cacheCallback,
            $callbackArguments,
            $lifetime
        );

        $cacheMessageListener = new CacheMessageListener($cacheProxy);
        $this->assertEquals(
            $cachedResponse,
            $cacheMessageListener->handleRetrieveByCallbackMessage($message, $this->getMessageDispatcherStub())
        );
    }

    /** @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::handleRetrieveByCallbackMessage */
    public function testHandleRetrieveByCallbackMessageWithInvalidMessage() {
        $message = new FlushMessage();
        $cacheMessageListener = new CacheMessageListener($this->getCacheProxyStub());
        $this->assertNull($cacheMessageListener->handleRetrieveByCallbackMessage($message, $this->getMessageDispatcherStub()));
    }

    /** @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::handleStoreMessage */
    public function testHandleStoreMessage() {
        $cacheIdentifier = "test";
        $content = "content to cache";
        $cacheLifetime = 60;

        $cacheProxy = $this->getCacheProxyStub();
        $cacheProxy->expects($this->once())
                   ->method("set")
                   ->with($cacheIdentifier, $content, $cacheLifetime)
                   ->will($this->returnSelf());

        $message = new StoreMessage(
            $cacheIdentifier,
            $content,
            $cacheLifetime
        );

        $cacheMessageListener = new CacheMessageListener($cacheProxy);
        $cacheMessageListener->handleStoreMessage($message, $this->getMessageDispatcherStub());
        $this->assertInstanceOf("\\Brickoo\\Component\\Cache\\Messaging\\Listener\\CacheMessageListener", $cacheMessageListener);
    }

    /** @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::handleDeleteMessage */
    public function testHandleDeleteMessage() {
        $cacheIdentifier = "test";

        $cacheProxy = $this->getCacheProxyStub();
        $cacheProxy->expects($this->once())
                   ->method("delete")
                   ->with($cacheIdentifier)
                   ->will($this->returnSelf());

        $message = new DeleteMessage($cacheIdentifier);
        $cacheMessageListener = new CacheMessageListener($cacheProxy);
        $cacheMessageListener->handleDeleteMessage($message, $this->getMessageDispatcherStub());
        $this->assertInstanceOf("\\Brickoo\\Component\\Cache\\Messaging\\Listener\\CacheMessageListener", $cacheMessageListener);
    }

    /** @covers Brickoo\Component\Cache\Messaging\Listener\CacheMessageListener::handleFlushMessage */
    public function testHandleFlushMessage() {
        $cacheProxy = $this->getCacheProxyStub();
        $cacheProxy->expects($this->once())
                   ->method("flush")
                   ->will($this->returnSelf());

        $message = new FlushMessage();
        $cacheMessageListener = new CacheMessageListener($cacheProxy);
        $cacheMessageListener->handleFlushMessage($message, $this->getMessageDispatcherStub());
        $this->assertInstanceOf("\\Brickoo\\Component\\Cache\\Messaging\\Listener\\CacheMessageListener", $cacheMessageListener);
    }

    /**
     * Returns a cache proxy stub.
     * @return \Brickoo\Component\Cache\CacheProxy
     */
    private function getCacheProxyStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Cache\\CacheProxy")
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
