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

namespace Brickoo\Messaging;

use Brickoo\Messaging\Listener,
    Brickoo\Messaging\ListenerAggregate,
    Brickoo\Messaging\ListenerCollection,
    Brickoo\Messaging\Message,
    Brickoo\Messaging\MessageListener,
    Brickoo\Messaging\MessageRecursionDepthList,
    Brickoo\Messaging\MessageResponseCollection,
    Brickoo\Messaging\Exception\MaxRecursionDepthReachedException,
    Brickoo\Validation\Argument;

/**
 * MessageDispatcher
 *
 * Implements methods for dispatching messages and handling message listeners.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageDispatcher {

    /** @var \Brickoo\Messaging\ListenerCollection */
    private $listenerCollection;

    /** @var \Brickoo\Messaging\MessageRecursionDepthList */
    private $messageRecursionDepthList;

    /**
     * Injects a listener collection for adding message listeners,
     * and a list to remember message recursion depth.
     * @param \Brickoo\Messaging\ListenerCollection $listenerCollection
     * @param \Brickoo\Messaging\MessageRecursionDepthList $messageRecursionDepthList
     * @return void
     */
    public function __construct(ListenerCollection $listenerCollection, MessageRecursionDepthList $messageRecursionDepthList) {
        $this->listenerCollection = $listenerCollection;
        $this->messageRecursionDepthList = $messageRecursionDepthList;
    }

    /**
     * Adds a listener to the collection.
     * @param \Brickoo\Messaging\Listener $listener
     * @return string the listener unique identifier
     */
    public function attach(Listener $listener) {
      return $this->listenerCollection->add($listener);
    }

    /**
     * Calls the listener with himself to attach the aggregated listeners.
     * @param \Brickoo\Messaging\ListenerAggregate $listener
     * @return \Brickoo\Messaging\MessageDispatcher
     */
    public function attachAggregatedListeners(ListenerAggregate $listener) {
        $listener->attachListeners($this);
        return $this;
    }

    /**
     * Removes the unqiue identified listener.
     * @param string $listenerUID the listener unique identifier
     * @throws \InvalidArgumentException
     * @return \Brickoo\Messaging\MessageDispatcher
     */
    public function detach($listenerUID) {
        Argument::IsString($listenerUID);
        $this->listenerCollection->remove($listenerUID);
        return $this;
    }

    /**
     * Dispatch the message to all corresponding listeners.
     * @param \Brickoo\Messaging\Message $message
     * @throws \Brickoo\Messaging\Exception\MaxRecursionDepthReachedException
     * @return \Brickoo\Messaging\MessageDispatcher
     */
    public function dispatch(Message $message) {
        $messageName = $message->getName();

        if (! $this->listenerCollection->hasListeners($messageName)) {
            return $this;
        }

        if ($this->messageRecursionDepthList->isDepthLimitReached($messageName)) {
            throw new MaxRecursionDepthReachedException($messageName, $this->messageRecursionDepthList->getRecursionDepth($messageName));
        }

        $this->messageRecursionDepthList->increaseDepth($messageName);
        $message->setResponse($this->processMessage($message, $this->listenerCollection->getListeners($messageName)));
        $this->messageRecursionDepthList->decreaseDepth($messageName);
        return $this;
    }

    /**
     * Process the message by calling the corresponding listener.
     * Creates the message response collection from the listener responses.
     * @param \Brickoo\Messaging\Message $message
     * @param array instancesOf \Brickoo\Messaging\Listener $listeners
     * @return void
     */
    private function processMessage(Message $message, array $listeners) {
        $responses = [];
        foreach ($listeners as $listener) {
            if($response = $listener->handleMessage($message, $this)) {
                $responses[] = $response;
            }
            if ($message->isStopped()) {
                break;
            }
        }
        return new MessageResponseCollection($responses);
    }

}