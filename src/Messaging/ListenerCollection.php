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

use Brickoo\Component\Messaging\Exception\ListenerNotAvailableException;
use Brickoo\Component\Messaging\Exception\ListenersNotAvailableException;
use Brickoo\Component\Validation\Argument;

/**
 * ListenerCollection
 *
 * Implements a listener collection.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ListenerCollection {

    /** @var array */
    private $listenerQueues;

    /** @var array */
    private $listeners;

    public function __construct() {
        $this->listenerQueues = [];
        $this->listeners = [];
    }

    /**
     * Add a listener to the memory.
     * @param \Brickoo\Component\Messaging\Listener $listener
     * @return string the listener queue unique identifier
     */
    public function add(Listener $listener) {
        if (! $this->hasListeners(($messageName = $listener->getMessageName()))) {
            $this->listenerQueues[$messageName] = new ListenerPriorityQueue();
        }

        $listenerUID = spl_object_hash($listener);
        $this->listeners[$listenerUID] = $listener;
        $this->listenerQueues[$messageName]->insert($listenerUID, $listener->getPriority());

        return $listenerUID;
    }

    /**
     * Return the listener matching the unique identifier.
     * @param string $listenerUID the listener unique identifier
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Messaging\Exception\ListenerNotAvailableException
     * @return \Brickoo\Component\Messaging\MessageListener
     */
    public function get($listenerUID) {
        Argument::isString($listenerUID);

        if (! $this->has($listenerUID)) {
            throw new ListenerNotAvailableException($listenerUID);
        }

        return $this->listeners[$listenerUID];
    }

    /**
     * Check if the listener with the unique identifier is available.
     * @param string $listenerUID the listener unique identifier to check
     * @throws \InvalidArgumentException
     * @return boolean check result
     */
    public function has($listenerUID) {
        Argument::isString($listenerUID);
        return isset($this->listeners[$listenerUID]);
    }

    /**
     * Remove the listener by its unique identifier.
     * @param string $listenerUID the listener unique identifier
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Messaging\Exception\ListenerNotAvailableException
     * @return \Brickoo\Component\Messaging\ListenerCollection
     */
    public function remove($listenerUID) {
        Argument::isString($listenerUID);

        if (! $this->has($listenerUID)) {
            throw new ListenerNotAvailableException($listenerUID);
        }

        $messageName = $this->get($listenerUID)->getMessageName();
        unset($this->listeners[$listenerUID]);
        $this->getListenerPriorityQueue($messageName)->remove($listenerUID);

        return $this;
    }

    /**
     * Return the message responsible listeners.
     * @param string $messageName the message name to retrieve the queue from
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Messaging\Exception\ListenersNotAvailableException
     * @return array the collected message listeners ordered by priority.
     */
    public function getListeners($messageName) {
        Argument::isString($messageName);

        if (! $this->hasListeners($messageName)) {
            throw new ListenersNotAvailableException($messageName);
        }

        return $this->collectMessageListeners($messageName);
    }

    /**
     * Check if the message has listeners listening.
     * @param string $messageName the message name to check
     * @throws \InvalidArgumentException if an argument is not valid
     * @return boolean check result
     */
    public function hasListeners($messageName) {
        Argument::isString($messageName);
        return (isset($this->listenerQueues[$messageName]));
    }

    /**
     * Collect the message corresponding listeners ordered by priority.
     * @param string $messageName the message to collect the listeners for
     * @return array the collected message listeners ordered by priority.
     */
    private function collectMessageListeners($messageName) {
        $listeners = [];

        foreach ($this->getListenerPriorityQueue($messageName) as $listenerUID) {
            $listeners[] = $this->get($listenerUID);
        }

        return $listeners;
    }

    /**
     * Return the message corresponding listener queue.
     * @param string $messageName
     * @return \Brickoo\Component\Messaging\ListenerPriorityQueue
     */
    private function getListenerPriorityQueue($messageName) {
        return $this->listenerQueues[$messageName];
    }

}
