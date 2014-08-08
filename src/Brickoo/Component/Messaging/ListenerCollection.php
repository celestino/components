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

    /** @var array<ListenerPriorityQueue> */
    private $listenerQueues;

    /** @var array<Listener> */
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
        Argument::IsString($listenerUID);

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
        Argument::IsString($listenerUID);
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
        Argument::IsString($listenerUID);

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
        Argument::IsString($messageName);

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
        Argument::IsString($messageName);
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
