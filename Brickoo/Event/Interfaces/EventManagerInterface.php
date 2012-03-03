<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    namespace Brickoo\Event\Interfaces;

    /**
     * EventManagerInterface
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface EventManagerInterface
    {

        /**
        * Checks if an event is currenty processing.
        * @param string $eventName the event to check
        * @return boolean check result
        */
        public function isEventProcessing($eventName);

        /**
         * Checks if the event has listeners.
         * @param string $eventName the event to check
         * @return boolean check result
         */
        public function hasEventListeners($eventName);

        /**
         * Returns the event listener queue listening to the event.
         * @param string $eventName the event to return the listener queue from
         * @return \Brickoo\Event\EventListenerQueue
         */
        public function getEventListenerQueue($eventName);

        /**
        * Adds a listener to an event.
        * @param string $eventName the event to listen to
        * @param callback $callback the callback to execute
        * @param integer $priority the listener priority factor
        * @param array|null $expectedParams the expected event parameters
        * @return string the listener unique identifier
        */
        public function attachListener($eventName, $callback, $priority = 0, array $expectedParams = null);

        /**
         * Removes the event listener.
         * @param string $listenerUID the listener unique identifier
         * @return \Brickoo\Event\Interfaces\EventManagerInterface
         */
        public function detachListener($listenerUID);

        /**
         * Notify all event listeners.
         * @param \Brickoo\Event\Interfaces\EventInterface $Event the executed event
         * @return void
         */
        public function notify(\Brickoo\Event\Interfaces\EventInterface $Event);

        /**
         * Asks all event listeners until one listener returns a response.
         * @param \Brickoo\Event\Interfaces\EventInterface $Event the exectued
         * @return mixed the listener response or null if no response has been returned
         */
        public function ask(\Brickoo\Event\Interfaces\EventInterface $Event);

        /**
         * Calls the event listener.
         * @param string $listenerUID the unique identiier of the listener
         * @param \Brickoo\Event\Interfaces\EventInterface $Event the event
         * @return mixed the listener response
         */
        public function call($listenerUID, \Brickoo\Event\Interfaces\EventInterface $Event);

    }