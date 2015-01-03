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

namespace Brickoo\Component\Storage\Messaging\Listener;

use Brickoo\Component\Storage\StorageProxy;
use Brickoo\Component\Storage\Messaging\Messages;
use Brickoo\Component\Storage\Messaging\Message\DeleteMessage;
use Brickoo\Component\Storage\Messaging\Message\FlushMessage;
use Brickoo\Component\Storage\Messaging\Message\RetrieveByCallbackMessage;
use Brickoo\Component\Storage\Messaging\Message\RetrieveMessage;
use Brickoo\Component\Storage\Messaging\Message\StoreMessage;
use Brickoo\Component\Messaging\ListenerAggregate;
use Brickoo\Component\Messaging\Message;
use Brickoo\Component\Messaging\MessageDispatcher;
use Brickoo\Component\Messaging\MessageListener;
use Brickoo\Component\Validation\Argument;

/**
 * StorageMessageListener
 *
 * Implements the handling of cache message listeners.
 * having a cache proxy as dependency for message processing cache operations.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class StorageMessageListener implements ListenerAggregate {

    /** @var \Brickoo\Component\Storage\StorageProxy */
    private $storageProxy;

    /** @var integer */
    private $listenerPriority;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Storage\StorageProxy $storageProxy
     * @param integer $listenerPriority the listener priority
     */
    public function __construct(StorageProxy $storageProxy, $listenerPriority = 0) {
        Argument::isInteger($listenerPriority);
        $this->storageProxy = $storageProxy;
        $this->listenerPriority = $listenerPriority;
    }

    /** {@inheritDoc} */
    public function attachListeners(MessageDispatcher $dispatcher) {
        $this->attachRetrieveMessageListener($dispatcher);
        $this->attachRetrieveByCallbackMessageListener($dispatcher);
        $this->attachStoreMessageListener($dispatcher);
        $this->attachDeleteMessageListener($dispatcher);
        $this->attachFlushMessageListener($dispatcher);
    }

    /**
     * Handle the message to retrieve the cached content from the injected cache proxy.
     * @param \Brickoo\Component\Messaging\Message $message
     * @return mixed the cached content otherwise null
     */
    public function handleRetrieveMessage(Message $message) {
        if ($message instanceof RetrieveMessage) {
            return $this->storageProxy->get($message->getIdentifier());
        }
        return null;
    }

    /**
     * Handle the message to retrieve the cached content from the injected cache proxy
     * with a callback used as a fallback.
     * @param \Brickoo\Component\Messaging\Message $message
     * @return mixed the cached content otherwise null
     */
    public function handleRetrieveByCallbackMessage(Message $message) {
        if ($message instanceof RetrieveByCallbackMessage) {
            return $this->storageProxy->getByCallback(
                $message->getIdentifier(),
                $message->getCallback(),
                $message->getCallbackArguments(),
                $message->getLifetime()
            );
        }
        return null;
    }

    /**
     * Handle the message to cache content.
     * @param \Brickoo\Component\Messaging\Message $message
     * @return void
     */
    public function handleStoreMessage(Message $message) {
        if ($message instanceof StoreMessage) {
            $this->storageProxy->set($message->getIdentifier(), $message->getContent(), $message->getLifetime());
        }
    }

    /**
     * Handle the message to delete the cached content hold by the identifier
     * through the injected cache proxy.
     * @param \Brickoo\Component\Messaging\Message $message
     * @return void
     */
    public function handleDeleteMessage(Message $message) {
        if ($message instanceof DeleteMessage) {
            $this->storageProxy->delete($message->getIdentifier());
        }
    }

    /**
     * Handle to flush the cache content through the injected cache proxy.
     * @param \Brickoo\Component\Messaging\Message $message
     * @return void
     */
    public function handleFlushMessage(Message $message) {
        if ($message instanceof FlushMessage) {
            $this->storageProxy->flush();
        }
    }

    /**
     * Attach the listener for retrieve messages.
     * @param MessageDispatcher $dispatcher
     * @return void
     */
    private function attachRetrieveMessageListener(MessageDispatcher $dispatcher) {
        $dispatcher->attach(new MessageListener(
            Messages::GET,
            $this->listenerPriority,
            [$this, "handleRetrieveMessage"]
        ));
    }

    /**
     * Attach the listener for callback based messages.
     * @param MessageDispatcher $dispatcher
     * @return void
     */
    private function attachRetrieveByCallbackMessageListener(MessageDispatcher $dispatcher) {
        $dispatcher->attach(new MessageListener(
            Messages::CALLBACK,
            $this->listenerPriority,
            [$this, "handleRetrieveByCallbackMessage"]
        ));
    }

    /**
     * Attach the listener for storing messages.
     * @param MessageDispatcher $dispatcher
     * @return void
     */
    private function attachStoreMessageListener(MessageDispatcher $dispatcher) {
        $dispatcher->attach(new MessageListener(
            Messages::SET,
            $this->listenerPriority,
            [$this, "handleStoreMessage"]
        ));
    }

    /**
     * Attach the listener for delete messages.
     * @param MessageDispatcher $dispatcher
     * @return void
     */
    private function attachDeleteMessageListener(MessageDispatcher $dispatcher) {
        $dispatcher->attach(new MessageListener(
            Messages::DELETE,
            $this->listenerPriority,
            [$this, "handleDeleteMessage"]
        ));
    }

    /**
     * Attach the listener for flushing messages.
     * @param MessageDispatcher $dispatcher
     * @return void
     */
    private function attachFlushMessageListener(MessageDispatcher $dispatcher) {
        $dispatcher->attach(new MessageListener(
            Messages::FLUSH,
            $this->listenerPriority,
            [$this, "handleFlushMessage"]
        ));
    }

}
