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

namespace Brickoo\Component\Messaging;

use Brickoo\Component\Messaging\Exception\MaxRecursionDepthReachedException;
use Brickoo\Component\Common\Assert;

/**
 * MessageDispatcher
 *
 * Implements methods for dispatching messages and handling message listeners.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class MessageDispatcher {

    /** @var \Brickoo\Component\Messaging\ListenerCollection */
    private $listenerCollection;

    /** @var \Brickoo\Component\Messaging\MessageRecursionDepthList */
    private $messageRecursionDepthList;

    /**
     * Injects a listener collection for adding message listeners,
     * and a list to remember message recursion depth.
     * @param \Brickoo\Component\Messaging\ListenerCollection $listenerCollection
     * @param \Brickoo\Component\Messaging\MessageRecursionDepthList $messageRecursionDepthList
     */
    public function __construct(ListenerCollection $listenerCollection, MessageRecursionDepthList $messageRecursionDepthList) {
        $this->listenerCollection = $listenerCollection;
        $this->messageRecursionDepthList = $messageRecursionDepthList;
    }

    /**
     * Adds a listener to the collection.
     * @param \Brickoo\Component\Messaging\Listener $listener
     * @return string the listener unique identifier
     */
    public function attach(Listener $listener) {
        return $this->listenerCollection->add($listener);
    }

    /**
     * Calls the listener with himself to attach the aggregated listeners.
     * @param \Brickoo\Component\Messaging\ListenerAggregate $listener
     * @return \Brickoo\Component\Messaging\MessageDispatcher
     */
    public function attachAggregatedListeners(ListenerAggregate $listener) {
        $listener->attachListeners($this);
        return $this;
    }

    /**
     * Removes the unique identified listener.
     * @param string $listenerUID the listener unique identifier
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Messaging\MessageDispatcher
     */
    public function detach($listenerUID) {
        Assert::isString($listenerUID);
        $this->listenerCollection->remove($listenerUID);
        return $this;
    }

    /**
     * Dispatch the message to all corresponding listeners.
     * @param \Brickoo\Component\Messaging\Message $message
     * @throws \Brickoo\Component\Messaging\Exception\MaxRecursionDepthReachedException
     * @return \Brickoo\Component\Messaging\MessageDispatcher
     */
    public function dispatch(Message $message) {
        $messageName = $message->getName();

        if (!$this->listenerCollection->hasListeners($messageName)) {
            return $this;
        }

        if ($this->messageRecursionDepthList->isDepthLimitReached($messageName)) {
            throw new MaxRecursionDepthReachedException(
                $messageName,
                $this->messageRecursionDepthList->getRecursionDepth($messageName)
            );
        }

        $this->messageRecursionDepthList->increaseDepth($messageName);
        $this->processMessage($message, $this->listenerCollection->getListeners($messageName));
        $this->messageRecursionDepthList->decreaseDepth($messageName);
        return $this;
    }

    /**
     * Process the message by calling the corresponding listener.
     * Creates the message response collection from the listener responses.
     * @param \Brickoo\Component\Messaging\Message $message
     * @param array $listeners \Brickoo\Component\Messaging\Listener
     * @return void
     */
    private function processMessage(Message $message, array $listeners) {
        foreach ($listeners as $listener) {
            $listener->handleMessage($message, $this);
            if ($message->isStopped()) {
                break;
            }
        }
    }

}
