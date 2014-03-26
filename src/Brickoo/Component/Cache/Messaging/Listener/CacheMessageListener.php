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

namespace Brickoo\Component\Cache\Messaging\Listener;

use Brickoo\Component\Cache\CacheProxy,
    Brickoo\Component\Cache\Messaging\Messages,
    Brickoo\Component\Cache\Messaging\Message\DeleteMessage,
    Brickoo\Component\Cache\Messaging\Message\FlushMessage,
    Brickoo\Component\Cache\Messaging\Message\RetrieveByCallbackMessage,
    Brickoo\Component\Cache\Messaging\Message\RetrieveMessage,
    Brickoo\Component\Cache\Messaging\Message\StoreMessage,
    Brickoo\Component\Messaging\ListenerAggregate,
    Brickoo\Component\Messaging\Message,
    Brickoo\Component\Messaging\MessageDispatcher,
    Brickoo\Component\Messaging\MessageListener,
    Brickoo\Component\Validation\Argument;

/**
 * CacheMessageListener
 *
 * Implements the handling of cache message listeners.
 * having a cache proxy as dependency for message processing cache operations.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class CacheMessageListener implements ListenerAggregate {

    /** @var \Brickoo\Component\Cache\CacheProxy */
    private $cacheProxy;

    /** @var integer */
    private $listenerPriority;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Cache\CacheProxy $cacheProxy
     * @param integer $listenerPriority the listener priority
     */
    public function __construct(CacheProxy $cacheProxy, $listenerPriority = 0) {
        Argument::IsInteger($listenerPriority);
        $this->cacheProxy = $cacheProxy;
        $this->listenerPriority = $listenerPriority;
    }

    /** {@inheritDoc} */
    public function attachListeners(MessageDispatcher $dispatcher) {
        $dispatcher->attach(new MessageListener(
            Messages::GET,
            $this->listenerPriority,
            [$this, "handleRetrieveMessage"]
        ));
        $dispatcher->attach(new MessageListener(
            Messages::CALLBACK,
            $this->listenerPriority,
            [$this, "handleRetrieveByCallbackMessage"]
        ));
        $dispatcher->attach(new MessageListener(
            Messages::SET,
            $this->listenerPriority,
            [$this, "handleStoreMessage"]
        ));
        $dispatcher->attach(new MessageListener(
            Messages::DELETE,
            $this->listenerPriority,
            [$this, "handleDeleteMessage"]
        ));
        $dispatcher->attach(new MessageListener(
            Messages::FLUSH,
            $this->listenerPriority,
            [$this, "handleFlushMessage"]
        ));
    }

    /**
     * Handle the message to retrieve the cached content from the injected cache proxy.
     * @param \Brickoo\Component\Messaging\Message $message
     * @return mixed the cached content otherwise null
     */
    public function handleRetrieveMessage(Message $message) {
        if ($message instanceof RetrieveMessage) {
            return $this->cacheProxy->get($message->getIdentifier());
        }
        return null;
    }

    /**
     * Handle the message to retrieve the cached content from the injected cache proxy
     * with a callback used as a fallback.
     *@param \Brickoo\Component\Messaging\Message $message
     * @return mixed the cached content otherwise null
     */
    public function handleRetrieveByCallbackMessage(Message $message) {
        if ($message instanceof RetrieveByCallbackMessage) {
            return $this->cacheProxy->getByCallback(
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
            $this->cacheProxy->set($message->getIdentifier(), $message->getContent(), $message->getLifetime());
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
            $this->cacheProxy->delete($message->getIdentifier());
        }
    }

    /**
     * Handle to flush the cache content through the injected cache proxy.
     * @param \Brickoo\Component\Messaging\Message $message
     * @return void
     */
    public function handleFlushMessage(Message $message) {
        if ($message instanceof FlushMessage) {
            $this->cacheProxy->flush();
        }
    }

}