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

    namespace Brickoo\Event\Listener\Interfaces;

    /**
     * Collection
     *
     * Defines a collection of event listeners.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface Collection {

        /**
         * Adds a listener to the memory.
         * @param \Brickoo\Event\Interfaces\Listener $Listener
         * @param integer $priority the queue priority of the listener, executed descending
         * @throws \InvalidArgumentException if an argument is not valid
         * @return \Brickoo\Event\Listener\Interfaces\Container
         */
        public function add(\Brickoo\Event\Interfaces\Listener $Listener, $priority);

        /**
         * Returns the listener matching the unqiue identifier.
         * @param string $listenerUID the listener unqiue identifier
         * @throws \InvalidArgumentException if an argument is not valid
         * @throws \Brickoo\Event\Listener\Exceptions\ListenerNotAvailable if the listener unique identifier is unknowed
         * @return \Brickoo\Event\Interfaces\Listener
         */
        public function get($listenerUID);

        /**
         * Checks if the listener with the unique idenfier is available.
         * @param string $listenerUID the listener unique identifer to check
         * @throws \InvalidArgumentException if an argument is not valid
         * @return boolean check result
         */
        public function has($listenerUID);

        /**
         * Removes the listener by its unique identifier.
         * @param string $listenerUID the listener unique identifier
         * @throws \InvalidArgumentException if an argument is not valid
         * @throws \Brickoo\Event\Listener\Exceptions\ListenerNotAvailable if the listener unique identifier is unknowed
         * @return \Brickoo\Event\Listener\Interfaces\Container
         */
        public function remove($listenerUID);

        /**
         * Returns the listeners responsible for an event.
         * @param string $eventName the event name to retrieve the queue from
         * @throws \InvalidArgumentException if an argument is not valid
         * @throws \Brickoo\Event\Listener\Exceptions\ListenersNotAvailable if the event has no listeners
         * @return \Brickoo\Event\Interfaces\ListenerQueue
         */
        public function getListeners($eventName);

        /**
         * Checks if the event has listeners listening.
         * @param string $eventName the event name to check
         * @throws \InvalidArgumentException if an argument is not valid
         * @return boolean check result
         */
        public function hasListeners($eventName);

    }