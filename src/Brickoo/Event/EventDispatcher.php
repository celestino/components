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

use Brickoo\Event\Event,
    Brickoo\Event\EventProcessor,
    Brickoo\Event\Listener,
    Brickoo\Event\ListenerAggregate,
    Brickoo\Event\ListenerCollection,
    Brickoo\Event\ResponseCollection,
    Brickoo\Event\Exception\InfiniteEventLoopException,
    Brickoo\Memory\Container,
    Brickoo\Validation\Argument;

/**
 * EventDispatcher
 *
 * Implements methods for handling events and their listeners.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class EventDispatcher {

    /**
     * Flag to call all event listeners.
     * @var integer
     */
    const BEHAVIOUR_CALL_ALL_LISTENERS = 0;

    /**
     * Flag to call only the listener with the highest priority.
     * @var integer
     */
    const BEHAVIOUR_CALL_ONLY_HIGHEST_PRIORITY_LISTENER = 1;

    /**
     * Flag to call listeners until a response (!null) is returned.
     * @var integer
     */
    const BEHAVIOUR_CALL_UNTIL_LISTENER_RESPONSE = 2;

    /**
     * Flag to call all event listeners and collect responses (!null).
     * @var integer
     */
    const BEHAVIOUR_CALL_ALL_LISTENERS_COLLECT_RESPONSES = 4;

    /** @var \Brickoo\Event\EventProcessor */
    private $processor;

    /** @var \Brickoo\Event\ListenerCollection */
    private $listenerCollection;

    /** @var \Brickoo\Memory\Container */
    private $eventList;

    /**
     * Class constructor.
     * Injects a listener collection for adding event listeners,
     * a processor to process the event triggered and a list to remember running events.
     * @param \Brickoo\Event\EventProcessor $processor
     * @param \Brickoo\Event\ListenerCollection $listenerCollection
     * @param \Brickoo\Memory\Container $eventList
     * @return void
     */
    public function __construct(EventProcessor $processor, ListenerCollection $listenerCollection, Container $eventList) {
        $this->processor  = $processor;
        $this->listenerCollection = $listenerCollection;
        $this->eventList = $eventList;
    }

    /**
     * Adds a listener to an event.
     * @param \Brickoo\Event\Listener $listener the listener to attach
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
     * @throws \Brickoo\Event\Exception\InfiniteEventLoopException
     * @return \Brickoo\Event\EventDispatcher
     */
    public function notify(Event $event) {
        $this->process($event, self::BEHAVIOUR_CALL_ALL_LISTENERS);
        return $this;
    }

    /**
     * Notifies the event with the highest priority.
     * @param \Brickoo\Event\Event $event the executed event
     * @throws \Brickoo\Event\Exception\InfiniteEventLoopException
     * @return \Brickoo\Event\EventDispatcher
     */
    public function notifyOnce(Event $event) {
        $this->process($event, self::BEHAVIOUR_CALL_ONLY_HIGHEST_PRIORITY_LISTENER);
        return $this;
    }

    /**
     * Asks all event listeners until one listener returns a response.
     * @param \Brickoo\Event\Event $event the exectued
     * @throws \Brickoo\Event\Exception\InfiniteEventLoopException
     * @return \Brickoo\Event\ResponseCollection containing the response
     */
    public function ask(Event $event) {
        return new ResponseCollection(
            $this->process($event, self::BEHAVIOUR_CALL_UNTIL_LISTENER_RESPONSE)
        );
    }

    /**
     * Collects all responses returned by the event listeners.
     * @param \Brickoo\Event\Event $event
     * @throws \Brickoo\Event\Exception\InfiniteEventLoopException
     * @return \Brickoo\Event\ResponseCollection containing the collected responses
     */
    public function collect(Event $event) {
        return new ResponseCollection(
            $this->process($event, self::BEHAVIOUR_CALL_ALL_LISTENERS_COLLECT_RESPONSES)
        );
    }

    /**
     * Process the event by calling the event listeners with the requested behaviour.
     * @param \Brickoo\Event\Event $event the event to processed
     * @param integer $behaviourControlFlag the behaviour control flag
     * @throws \Brickoo\Event\Exception\InfiniteEventLoopException
     * @return array the listener responses otherwise
     */
    private function process(Event $event, $behaviourControlFlag) {
        $eventName = $event->getName();

        if (! $this->listenerCollection->hasListeners($eventName)) {
            return [];
        }

        if ($this->eventList->has($eventName)) {
            throw new InfiniteEventLoopException($eventName);
        }

        $this->eventList->set($eventName, time());
        $responses = $this->getEventListenersResponses($event, $behaviourControlFlag);
        $this->eventList->delete($eventName);

        return $responses;
    }

    /**
     * Returns the event listeners responses.
     * @param \Brickoo\Event\Event $event the event to processed
     * @param integer $behaviourControlFlag the behaviour control flag
     * @return mixed the returned response or array the collected responses
     */
    private function getEventListenersResponses(Event $event, $behaviourControlFlag) {
        $collectedResponses = [];

        foreach ($this->listenerCollection->getListeners($event->getName()) as $listener) {
            $response = $this->processor->handle($this, $event, $listener);

            if ((($behaviourControlFlag & self::BEHAVIOUR_CALL_UNTIL_LISTENER_RESPONSE) == $behaviourControlFlag)
                && ($response !== null)
            ){
                $collectedResponses[] = $response;
                break;
            }

            if ($event->isStopped() || (($behaviourControlFlag & self::BEHAVIOUR_CALL_ONLY_HIGHEST_PRIORITY_LISTENER) == $behaviourControlFlag)) {
                break;
            }

            if (($behaviourControlFlag & self::BEHAVIOUR_CALL_ALL_LISTENERS_COLLECT_RESPONSES) == $behaviourControlFlag
                && ($response !== null)
            ){
                $collectedResponses[] = $response;
            }
        }

        return $collectedResponses;
    }

}