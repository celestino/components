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

    namespace Brickoo\Event\Interfaces;

    /**
     * Manager
     *
     * Describes an event manager which implements methods to
     * handle the attachment of listeners and their notifications.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface Manager {
        /**
         * Adds a listener to an event.
         * @param \Brickoo\Event\Interfaces\Listener $Listener the listener to attach
         * @return string the listener unique identifier
         */
        public function attach(\Brickoo\Event\Interfaces\Listener $Listener);

        /**
         * Calls the listener with himself to attach the aggregated listeners.
         * @param \Brickoo\Event\Interfaces\ListenerAggregate $Listener
         * @return \Brickoo\Event\Interfaces\Manager
         */
        public function attachAggregatedListeners(\Brickoo\Event\Interfaces\ListenerAggregate $Listener);

        /**
         * Removes the event listener.
         * @param string $listenerUID the listener unique identifier
         * @throws \InvalidArgumentException if an argument is not valid
         * @return \Brickoo\Event\Interfaces\Manager
         */
        public function detach($listenerUID);

        /**
         * Notify all event listeners.
         * @param \Brickoo\Event\Interfaces\Event $Event the executed event
         * @throws \Brickoo\Event\Exceptions\InfiniteEventLoop if an infinite loop is detected
         * @return \Brickoo\Event\Interfaces\Manager
         */
        public function notify(\Brickoo\Event\Interfaces\Event $Event);

        /**
         * Notifies the event with the highest priority.
         * @param \Brickoo\Event\Interfaces\Event $Event the executed event
         * @throws \Brickoo\Event\Exceptions\InfiniteEventLoop if an infinite loop is detected
         * @return \Brickoo\Event\Interfaces\Manager
         */
        public function notifyOnce(\Brickoo\Event\Interfaces\Event $Event);

        /**
         * Asks all event listeners until one listener returns a response.
         * @param \Brickoo\Event\Interfaces\Event $Event the exectued
         * @throws \Brickoo\Event\Exceptions\InfiniteEventLoop if an infinite loop is detected
         * @return \Brickoo\Event\Response\Interfaces\Collection containing the response
         */
        public function ask(\Brickoo\Event\Interfaces\Event $Event);

        /**
         * Collects all responses returned by the event listeners.
         * @param \Brickoo\Event\Interfaces\Event $Event
         * @throws \Brickoo\Event\Exceptions\InfiniteEventLoop if an infinite loop is detected
         * @return \Brickoo\Event\Response\Interfaces\Collection containing the collected responses
         */
        public function collect(\Brickoo\Event\Interfaces\Event $Event);

    }