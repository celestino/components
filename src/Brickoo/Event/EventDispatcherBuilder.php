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

use Brickoo\Memory\Container,
    Brickoo\Validator\Argument,
    Brickoo\Validator\Constraint\TraversableContainsInstancesOf;

/**
 * EventDispatcherBuilder
 *
 * Builds an event dipatcher with the dependecies configured.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class EventDispatcherBuilder {

    /** @var \Brickoo\Event\EventeventProcessor */
    private $eventProcessor;

    /** @var \Brickoo\Event\ListenerCollection */
    private $listenerCollection;

    /** @var \Brickoo\Memory\Interfaces\Container */
    private $eventList;

    /** @var \Traversable|array */
    private $listeners;

    /**
     * Class constructor.
     * @return void
     */
    public function __construct() {
        $this->listeners = array();
    }

    /**
     * Sets the event processor dependency.
     * @param \Brickoo\Event\EventProcessor $eventProcessor
     * @return \Brickoo\Event\EventDispatcherBuilder
     */
    public function setEventeventProcessor(\Brickoo\Event\EventProcessor $eventProcessor) {
        $this->eventProcessor = $eventProcessor;
        return $this;
    }

    /**
     * Sets the listener collection dependency.
     * @param \Brickoo\Event\ListenerCollection $listenerCollection
     * @return \Brickoo\Event\EventDispatcherBuilder
     */
    public function setListenerCollection(\Brickoo\Event\ListenerCollection $listenerCollection) {
        $this->listenerCollection = $listenerCollection;
        return $this;
    }

    /**
     * Sets the event memory list dependency.
     * @param \Brickoo\Memory\Interfaces\Container $eventList
     * @return \Brickoo\Event\EventDispatcherBuilder
     */
    public function setEventList(\Brickoo\Memory\Interfaces\Container $eventList) {
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

        $Constraint = new TraversableContainsInstancesOf('Brickoo\Event\Interfaces\Listener');
        if (! $Constraint->assert($listeners)) {
            throw new \InvalidArgumentException("The traversable must contain Event\Listeners only.");
        }

        $this->listeners = $listeners;
        return $this;
    }

    /**
     * Builds the event dispatcher based on the configuration or
     * default implementations.
     * @return \Brickoo\Event\EventDispatcher
     */
    public function build() {
        $EventDispatcher = new EventDispatcher(
            $this->getEventeventProcessor(), $this->getListenerCollection(), $this->getEventList()
        );
        $this->attachListeners($EventDispatcher);
        return $EventDispatcher;
    }

    /**
     * Returns the configured event processor dependency.
     * If it does not exists it will be created using the framework implementation.
     * @return \Brickoo\Event\EventProcessor
     */
    private function getEventeventProcessor() {
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
     * @param \Brickoo\Event\EventDispatcher $EventDispatcher
     * @return \Brickoo\Event\EventDispatcherBuilder
     */
    private function attachListeners(EventDispatcher $EventDispatcher) {
        foreach ($this->listeners as $Listener) {
            $EventDispatcher->attach($Listener);
        }
        return $this;
    }

}