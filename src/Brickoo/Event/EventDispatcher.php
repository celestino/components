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

namespace Brickoo\Event;

use Brickoo\Event\Event,
    Brickoo\Event\EventProcessor,
    Brickoo\Event\EventRecursionDepthList,
    Brickoo\Event\Listener,
    Brickoo\Event\ListenerAggregate,
    Brickoo\Event\ListenerCollection,
    Brickoo\Event\ResponseCollection,
    Brickoo\Event\Exception\MaxRecursionDepthReachedException,
    Brickoo\Validation\Argument;

/**
 * EventDispatcher
 *
 * Implements methods for dispatching events and handling event listeners.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class EventDispatcher {

    /** @var \Brickoo\Event\EventProcessor */
    private $processor;

    /** @var \Brickoo\Event\ListenerCollection */
    private $listenerCollection;

    /** @var \Brickoo\Event\EventRecursionDepthList */
    private $eventRecursionDepthList;

    /**
     * Class constructor.
     * Injects a listener collection for adding event listeners,
     * a processor to process the event triggered and a list to remember running events.
     * @param \Brickoo\Event\EventProcessor $processor
     * @param \Brickoo\Event\ListenerCollection $listenerCollection
     * @param \Brickoo\Event\EventRecursionDepthList $eventRecursionDepthList
     * @return void
     */
    public function __construct(EventProcessor $processor, ListenerCollection $listenerCollection, EventRecursionDepthList $eventRecursionDepthList) {
        $this->processor  = $processor;
        $this->listenerCollection = $listenerCollection;
        $this->eventRecursionDepthList = $eventRecursionDepthList;
    }

    /**
     * Adds a listener to an event.
     * @param \Brickoo\Event\Listener $listener
     * @return string the listener unique identifier
     */
    public function attach(Listener $listener) {
      return $this->listenerCollection->add($listener, $listener->getPriority());
    }

    /**
     * Calls the listener with himself to attach the aggregated listeners.
     * @param \Brickoo\Event\ListenerAggregate $listener
     * @return \Brickoo\Event\EventDispatcher
     */
    public function attachAggregatedListeners(ListenerAggregate $listener) {
        $listener->attachListeners($this);
        return $this;
    }

    /**
     * Removes the event listener.
     * @param string $listenerUID the listener unique identifier
     * @throws \InvalidArgumentException
     * @return \Brickoo\Event\EventDispatcher
     */
    public function detach($listenerUID) {
        Argument::IsString($listenerUID);
        $this->listenerCollection->remove($listenerUID);
        return $this;
    }

    /**
     * Notify all event listeners.
     * @param \Brickoo\Event\Event $event the executed event
     * @throws \Brickoo\Event\Exception\MaxRecursionDepthReachedException
     * @return \Brickoo\Event\EventDispatcher
     */
    public function notify(Event $event) {
        $this->dispatch($event);
        return $this;
    }

    /**
     * Collects all responses returned by the event listeners.
     * If recursion is made the returned responses will be a
     * response collection, the responses should be merged into one response collection.
     * @param \Brickoo\Event\Event $event
     * @throws \Brickoo\Event\Exception\MaxRecursionDepthReachedException
     * @return \Brickoo\Event\ResponseCollection containing the collected responses
     */
    public function collect(Event $event) {
        return new ResponseCollection($this->dispatch($event));
    }

    /**
     * Process the event by calling the event listeners with the requested event.
     * @param \Brickoo\Event\Event $event the event to processed
     * @param callable $condition the condition to execute after each listener response
     * @throws \Brickoo\Event\Exception\MaxRecursionDepthReachedException
     * @return array the listener responses
     */
    private function dispatch(Event $event) {
        $eventName = $event->getName();

        if (! $this->listenerCollection->hasListeners($eventName)) {
            return [];
        }

        if ($this->eventRecursionDepthList->isDepthLimitReached($eventName)) {
            throw new MaxRecursionDepthReachedException($eventName, $this->eventRecursionDepthList->getRecursionDepth($eventName));
        }

        $this->eventRecursionDepthList->increaseDepth($eventName);
        $responses = $this->processor->process($this, $event, $this->listenerCollection->getListeners($eventName));
        $this->eventRecursionDepthList->decreaseDepth($eventName);
        return $responses;
    }

}