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

use Brickoo\Event\EventDispatcher,
    Brickoo\Event\EventProcessor,
    Brickoo\Event\ListenerCollection,
    Brickoo\Memory\Container,
    Brickoo\Validation\Argument,
    Brickoo\Validation\Constraint\ContainsInstancesOfConstraint;

/**
 * EventDispatcherBuilder
 *
 * Builds an event dipatcher with the dependecies configured.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class EventDispatcherBuilder {

    /** @var \Brickoo\Event\EventProcessor */
    private $eventProcessor;

    /** @var \Brickoo\Event\ListenerCollection */
    private $listenerCollection;

    /** @var \Brickoo\Memory\Container */
    private $eventList;

    /** @var \Traversable|array */
    private $listeners;

    /**
     * Class constructor.
     * @return void
     */
    public function __construct() {
        $this->listeners = [];
    }

    /**
     * Sets the event processor dependency.
     * @param \Brickoo\Event\EventProcessor $eventProcessor
     * @return \Brickoo\Event\EventDispatcherBuilder
     */
    public function setEventProcessor(EventProcessor $eventProcessor) {
        $this->eventProcessor = $eventProcessor;
        return $this;
    }

    /**
     * Sets the listener collection dependency.
     * @param \Brickoo\Event\ListenerCollection $listenerCollection
     * @return \Brickoo\Event\EventDispatcherBuilder
     */
    public function setListenerCollection(ListenerCollection $listenerCollection) {
        $this->listenerCollection = $listenerCollection;
        return $this;
    }

    /**
     * Sets the event memory list dependency.
     * @param \Brickoo\Memory\Container $eventList
     * @return \Brickoo\Event\EventDispatcherBuilder
     */
    public function setEventList(Container $eventList) {
        $this->eventList = $eventList;
        return $this;
    }

    /**
     * Sets the event listeners of the event manager.
     * @param \Traversable|array $listeners
     * @throws \InvalidArgumentException
     * @return \Brickoo\Event\EventDispatcherBuilder
     */
    public function setListeners($listeners) {
        Argument::IsTraversable($listeners);

        if (! (new ContainsInstancesOfConstraint("Brickoo\\Event\\Interfaces\\Listener"))->assert($listeners)) {
            throw new \InvalidArgumentException("The traversable must contain Event\\Listeners only.");
        }

        $this->listeners = $listeners;
        return $this;
    }

    /**
     * Builds the event dispatcher based on the configuration or default implementations.
     * @return \Brickoo\Event\EventDispatcher
     */
    public function build() {
        $EventDispatcher = new EventDispatcher(
            $this->getEventProcessor(), $this->getListenerCollection(), $this->getEventList()
        );
        $this->attachListeners($EventDispatcher);
        return $EventDispatcher;
    }

    /**
     * Returns the configured event processor dependency.
     * If it does not exists it will be created using the framework implementation.
     * @return \Brickoo\Event\EventProcessor
     */
    private function getEventProcessor() {
        if ($this->eventProcessor === null) {
            $this->eventProcessor = new EventProcessor();
        }
        return $this->eventProcessor;
    }

    /**
     * Returns the configured listener collection dependency.
     * If it does not exists it will be created using the framework implementation.
     * @return \Brickoo\Event\ListenerCollection
     */
    private function getListenerCollection() {
        if ($this->listenerCollection === null) {
            $this->listenerCollection = new ListenerCollection();
        }
        return $this->listenerCollection;
    }

    /**
     * Returns the configured memory event list dependency.
     * If it does not exists it will be created using the framework implementation.
     * @return \Brickoo\Memory\Container
     */
    private function getEventList() {
        if ($this->eventList === null) {
            $this->eventList = new Container();
        }
        return $this->eventList;
    }

    /**
     * Attach the configured event listeners to the event manager.
     * @param \Brickoo\Event\EventDispatcher $eventDispatcher
     * @return \Brickoo\Event\EventDispatcherBuilder
     */
    private function attachListeners(EventDispatcher $eventDispatcher) {
        foreach ($this->listeners as $Listener) {
            $eventDispatcher->attach($Listener);
        }
        return $this;
    }

}