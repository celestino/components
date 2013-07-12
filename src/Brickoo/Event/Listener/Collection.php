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

    namespace Brickoo\Event\Listener;

    use Brickoo\Validator\Argument;

    /**
     * Collection
     *
     * Implementes an event listener collection.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Collection implements Interfaces\Collection {

        /** @var array */
        private $listenerQueues;

        /** @var array */
        private $listeners;

        public function __construct() {
            $this->listenerQueues = array();
            $this->listeners = array();
        }

        /** {@inheritDoc} */
        public function add(\Brickoo\Event\Interfaces\Listener $Listener, $priority) {
            Argument::IsInteger($priority);

            if (! $this->hasListeners(($eventName = $Listener->getEventName()))) {
                $this->listenerQueues[$eventName] = new Queue();
            }

            $listenerUID = spl_object_hash($Listener);
            $this->listeners[$listenerUID] = $Listener;
            $this->listenerQueues[$eventName]->insert($listenerUID, $priority);

            return $listenerUID;
        }

        /** {@inheritDoc} */
        public function get($listenerUID) {
            Argument::IsString($listenerUID);

            if (! $this->has($listenerUID)) {
                throw new Exceptions\ListenerNotAvailable($listenerUID);
            }

            return $this->listeners[$listenerUID];
        }

        /** {@inheritDoc} */
        public function has($listenerUID) {
            Argument::IsString($listenerUID);
            return isset($this->listeners[$listenerUID]);
        }

        /** {@inheritDoc} */
        public function remove($listenerUID) {
            Argument::IsString($listenerUID);

            if (! $this->has($listenerUID)) {
                throw new Exceptions\ListenerNotAvailable($listenerUID);
            }

            $eventName = $this->get($listenerUID)->getEventName();
            unset($this->listeners[$listenerUID]);
            $this->removeListenerFromQueue($eventName, $listenerUID);

            return $this;
        }

        /**
         * Removes the listener from the vent listener queue.
         * @param string $eventName the event name of the queue
         * @param string $listenerUID the listener unique identifier
         * @return void
         */
        private function removeListenerFromQueue($eventName, $listenerUID) {
            $ListenerQueue = $this->listenerQueues[$eventName];
            $ListenerQueue->setExtractFlags(Queue::EXTR_BOTH);

            $CleanedListenerQueue = new Queue();
            while ($ListenerQueue->valid()) {
                $listener = $ListenerQueue->extract();
                if ($listener['data'] != $listenerUID) {
                    $CleanedListenerQueue->insert($listener['data'], $listener['priority']);
                }
            }

            $this->listenerQueues[$eventName] = $CleanedListenerQueue;
        }

        /** {@inheritDoc} */
        public function getListeners($eventName) {
            Argument::IsString($eventName);

            if (! $this->hasListeners($eventName)) {
                throw new Exceptions\ListenersNotAvailable($eventName);
            }

            return $this->collectEventListeners($eventName);
        }

        /**
         * Collects the event listeners ordered by priority.
         * @param string $eventName the event to collect the listeners for
         * @return array the collected event listeners ordered by priority.
         */
        private function collectEventListeners($eventName) {
            $listeners = array();
            $ListenersQueue = clone $this->listenerQueues[$eventName];

            foreach ($ListenersQueue as $listenerUID) {
                $listeners[] = $this->get($listenerUID);
            }

            return $listeners;
        }

        /** {@inheritDoc} */
        public function hasListeners($eventName) {
            Argument::IsString($eventName);
            return (isset($this->listenerQueues[$eventName]));
        }

    }