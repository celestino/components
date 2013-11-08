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

use Brickoo\Validator\Argument;

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

    /** @var \Brickoo\Event\Process\Interfaces\Processor */
    private $Processor;

    /** @var \Brickoo\Event\Listener\Interfaces\Collection */
    private $ListenerCollection;

    /** @var \Brickoo\Memory\Interfaces\Container */
    private $EventList;

    /**
     * Class constructor.
     * Injects a listener collection for adding event listeners,
     * a processor to process the event triggered and a list to remember running events.
     * @param \Brickoo\Event\Interfaces\Processor $Processor
     * @param \Brickoo\Event\Listener\Interfaces\Collection $ListenerCollection
     * @param \Brickoo\Memory\Interfaces\Container $EventList
     * @return void
     */
    public function __construct(
        \Brickoo\Event\Process\Interfaces\Processor $Processor,
        \Brickoo\Event\Listener\Interfaces\Collection $ListenerCollection,
        \Brickoo\Memory\Interfaces\Container $EventList
    ){
        $this->Processor  = $Processor;
        $this->ListenerCollection = $ListenerCollection;
        $this->EventList = $EventList;
    }

    /**
     * Adds a listener to an event.
     * @param \Brickoo\Event\Interfaces\Listener $Listener the listener to attach
     * @return string the listener unique identifier
     */
    public function attach(\Brickoo\Event\Interfaces\Listener $Listener) {
      return $this->ListenerCollection->add($Listener, $Listener->getPriority());
    }

    /**
     * Calls the listener with himself to attach the aggregated listeners.
     * @param \Brickoo\Event\Interfaces\ListenerAggregate $Listener
     * @return \Brickoo\Event\Interfaces\Manager
     */
    public function attachAggregatedListeners(\Brickoo\Event\Interfaces\ListenerAggregate $Listener) {
        $Listener->attachListeners($this);
        return $this;
    }

    /**
         * Removes the event listener.
         * @param string $listenerUID the listener unique identifier
         * @throws \InvalidArgumentException if an argument is not valid
         * @return \Brickoo\Event\Interfaces\Manager
         */
    public function detach($listenerUID) {
        Argument::IsString($listenerUID);
        $this->ListenerCollection->remove($listenerUID);
        return $this;
    }

    /**
         * Notify all event listeners.
         * @param \Brickoo\Event\Interfaces\Event $Event the executed event
         * @throws \Brickoo\Event\Exceptions\InfiniteEventLoop if an infinite loop is detected
         * @return \Brickoo\Event\Interfaces\Manager
         */
    public function notify(\Brickoo\Event\Interfaces\Event $Event) {
        $this->process($Event, self::BEHAVIOUR_CALL_ALL_LISTENERS);
        return $this;
    }

    /**
         * Notifies the event with the highest priority.
         * @param \Brickoo\Event\Interfaces\Event $Event the executed event
         * @throws \Brickoo\Event\Exceptions\InfiniteEventLoop if an infinite loop is detected
         * @return \Brickoo\Event\Interfaces\Manager
         */
    public function notifyOnce(\Brickoo\Event\Interfaces\Event $Event) {
        $this->process($Event, self::BEHAVIOUR_CALL_ONLY_HIGHEST_PRIORITY_LISTENER);
        return $this;
    }

    /**
         * Asks all event listeners until one listener returns a response.
         * @param \Brickoo\Event\Interfaces\Event $Event the exectued
         * @throws \Brickoo\Event\Exceptions\InfiniteEventLoop if an infinite loop is detected
         * @return \Brickoo\Event\Response\Interfaces\Collection containing the response
         */
    public function ask(\Brickoo\Event\Interfaces\Event $Event) {
        return new Response\ResponseCollection(
            $this->process($Event, self::BEHAVIOUR_CALL_UNTIL_LISTENER_RESPONSE)
        );
    }

    /**
         * Collects all responses returned by the event listeners.
         * @param \Brickoo\Event\Interfaces\Event $Event
         * @throws \Brickoo\Event\Exceptions\InfiniteEventLoop if an infinite loop is detected
         * @return \Brickoo\Event\Response\Interfaces\Collection containing the collected responses
         */
    public function collect(\Brickoo\Event\Interfaces\Event $Event) {
        return new Response\ResponseCollection(
            $this->process($Event, self::BEHAVIOUR_CALL_ALL_LISTENERS_COLLECT_RESPONSES)
        );
    }

    /**
     * Process the event by calling the event listeners with the requested behaviour.
     * @param \Brickoo\Event\Interfaces\Event $Event the event to processed
     * @param integer $behaviourControlFlag the behaviour control flag
     * @throws \Brickoo\Event\Exceptions\InfiniteEventLoop if an infinite loop is detected
     * @return array the listener responses otherwise null on failure
     */
    private function process(\Brickoo\Event\Interfaces\Event $Event, $behaviourControlFlag) {
        $response = array();
        $eventName = $Event->getName();

        if (! $this->ListenerCollection->hasListeners($eventName)) {
            return $response;
        }

        if ($this->EventList->has($eventName)) {
            throw new Exceptions\InfiniteEventLoop($eventName);
        }

        $this->EventList->set($eventName, time());
        $response = $this->getEventListenersResponse($Event, $behaviourControlFlag);
        $this->EventList->delete($eventName);

        return $response;
    }

    /**
     * Returns the event listeners response(s).
     * @param \Brickoo\Event\Interfaces\Event $Event the event to processed
     * @param integer $behaviourControlFlag the behaviour control flag
     * @return mixed the returned response or array the collected responses
     */
    private function getEventListenersResponse(\Brickoo\Event\Interfaces\Event $Event, $behaviourControlFlag) {
        $collectedResponses = array();

        foreach ($this->ListenerCollection->getListeners($Event->getName()) as $Listener) {
            $response = $this->Processor->handle($this, $Event, $Listener);

            if ((($behaviourControlFlag & self::BEHAVIOUR_CALL_UNTIL_LISTENER_RESPONSE) == $behaviourControlFlag)
                && ($response !== null)
            ){
                $collectedResponses[] = $response;
                break;
            }

            if ($Event->isStopped() || (($behaviourControlFlag & self::BEHAVIOUR_CALL_ONLY_HIGHEST_PRIORITY_LISTENER) == $behaviourControlFlag)) {
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