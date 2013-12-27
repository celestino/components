<?php

/*
 * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

namespace Brickoo\Event;

use Brickoo\Event\Listener,
    Brickoo\Event\ListenerQueue,
    Brickoo\Event\Exception\ListenerNotAvailableException,
    Brickoo\Validator\Argument;

/**
 * ListenerCollection
 *
 * Implements an event listener collection.
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
     * Adds a listener to the memory.
     * @param \Brickoo\Event\Listener $Listener
     * @return string the listener queue unique identifier
     */
    public function add(Listener $Listener) {
        if (! $this->hasListeners(($eventName = $Listener->getEventName()))) {
            $this->listenerQueues[$eventName] = new ListenerQueue();
        }

        $listenerUID = spl_object_hash($Listener);
        $this->listeners[$listenerUID] = $Listener;
        $this->listenerQueues[$eventName]->insert($listenerUID, $Listener->getPriority());

        return $listenerUID;
    }

    /**
     * Returns the listener matching the unqiue identifier.
     * @param string $listenerUID the listener unqiue identifier
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Event\Exception\ListenerNotAvailableException
     * @return \Brickoo\Event\Listener
     */
    public function get($listenerUID) {
        Argument::IsString($listenerUID);

        if (! $this->has($listenerUID)) {
            throw new ListenerNotAvailableException($listenerUID);
        }

        return $this->listeners[$listenerUID];
    }

    /**
     * Checks if the listener with the unique idenfier is available.
     * @param string $listenerUID the listener unique identifer to check
     * @throws \InvalidArgumentException
     * @return boolean check result
     */
    public function has($listenerUID) {
        Argument::IsString($listenerUID);
        return isset($this->listeners[$listenerUID]);
    }

    /**
     * Removes the listener by its unique identifier.
     * @param string $listenerUID the listener unique identifier
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Event\Exception\ListenerNotAvailableException
     * @return \Brickoo\Event\ListenerCollection
     */
    public function remove($listenerUID) {
        Argument::IsString($listenerUID);

        if (! $this->has($listenerUID)) {
            throw new ListenerNotAvailableException($listenerUID);
        }

        $eventName = $this->get($listenerUID)->getEventName();
        unset($this->listeners[$listenerUID]);
        $this->removeListenerFromQueue($eventName, $listenerUID);

        return $this;
    }

    /**
     * Returns the listeners responsible for an event.
     * @param string $eventName the event name to retrieve the queue from
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Event\Exception\ListenersNotAvailableException
     * @return \Brickoo\Event\ListenerQueue
     */
    public function getListeners($eventName) {
        Argument::IsString($eventName);

        if (! $this->hasListeners($eventName)) {
            throw new ListenersNotAvailableException($eventName);
        }

        return $this->collectEventListeners($eventName);
    }

    /**
     * Checks if the event has listeners listening.
     * @param string $eventName the event name to check
     * @throws \InvalidArgumentException if an argument is not valid
     * @return boolean check result
     */
    public function hasListeners($eventName) {
        Argument::IsString($eventName);
        return (isset($this->listenerQueues[$eventName]));
    }

    /**
     * Removes the listener from the vent listener queue.
     * @param string $eventName the event name of the queue
     * @param string $listenerUID the listener unique identifier
     * @return \Brickoo\Event\ListenerCollection
     */
    private function removeListenerFromQueue($eventName, $listenerUID) {
        $ListenerQueue = $this->listenerQueues[$eventName];
        $ListenerQueue->setExtractFlags(Queue::EXTR_BOTH);

        $CleanedListenerQueue = new Queue();
        while ($ListenerQueue->valid()) {
            $listener = $ListenerQueue->extract();
            if ($listener["data"] != $listenerUID) {
                $CleanedListenerQueue->insert($listener["data"], $listener["priority"]);
            }
        }

        $this->listenerQueues[$eventName] = $CleanedListenerQueue;
        return $this;
    }

    /**
     * Collects the event listeners ordered by priority.
     * @param string $eventName the event to collect the listeners for
     * @return array the collected event listeners ordered by priority.
     */
    private function collectEventListeners($eventName) {
        $listeners = [];
        $ListenersQueue = clone $this->listenerQueues[$eventName];

        foreach ($ListenersQueue as $listenerUID) {
            $listeners[] = $this->get($listenerUID);
        }

        return $listeners;
    }

}