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

    namespace Brickoo\Event;

    use Brickoo\Validator\TypeValidator;

    /**
     * EventManager
     *
     * EventManager event handling with registered listeners.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class EventManager implements Interfaces\EventManagerInterface
    {

        /**
         * Holds a list of currently processing events.
         * @var array
         */
        protected $processingEvents;

        /**
         * Checks if an event is currenty processing.
         * @param string $eventName the event to check
         * @return boolean check result
         */
        public function isEventProcessing($eventName)
        {
            $eventName = $this->getUniformEventName($eventName);

            return (in_array($eventName, $this->processingEvents));
        }

        /**
         * Adds an event to the processing list.
         * @param string $eventName the event to add
         * @return \Brickoo\Event\EventManager
         */
        protected function addEventProcessing($eventName)
        {
            $eventName = $this->getUniformEventName($eventName);

            $this->processingEvents[] = $eventName;

            return $this;
        }

        /**
         * Removes an event from the processing list.
         * @param string $eventName the vent to remove
         * @return \Brickoo\Event\EventManager
         */
        protected function removeProcessedEvent($eventName)
        {
            $eventName = $this->getUniformEventName($eventName);

            if (false !== ($key = array_search($eventName, $this->processingEvents))) {
                unset($this->processingEvents[$key]);
            }

            return $this;
        }

        /**
         * Holds the registered listeners.
         * @var array
         */
        protected $listeners;

        /**
         * Check if the listener unique indetifier is registered.
         * @param strign $listenerUID the listener unique identifier
         * @return boolean check result
         */
        protected function isListener($listenerUID)
        {
            TypeValidator::IsString($listenerUID);

            return isset($this->listeners[$listenerUID]);
        }

        /**
         * Adds an event listener.
         * @param string $eventName the event to listen to
         * @param callback $callback the callback to execute
         * @param integer $priority the listener priority factor
         * @param array|null $expectedParams the expected event parameters
         * @return string the listener unique identifier
         */
        public function attachListener($eventName, $callback, $priority = 0, array $expectedParams = null)
        {
            TypeValidator::IsInteger($priority);
            $eventName = $this->getUniformEventName($eventName);

            if (! is_callable($callback)) {
                throw new \InvalidArgumentException('The callback argument is not callable.');
            }

            $listenerUID = uniqid();

            $this->getEventListenerQueue($eventName)->insert($listenerUID, $priority);

            $this->listeners[$listenerUID] = array(
                'callback'    => $callback,
                'params'      => $expectedParams
            );

            return $listenerUID;
        }

        /**
         * Removes the event listener.
         * @param string $listenerUID the listener unique identifier
         * @return \Brickoo\Event\EventManager
         */
        public function detachListener($listenerUID)
        {
            TypeValidator::IsString($listenerUID);

            if ($this->isListener($listenerUID)) {
                unset($this->listeners[$listenerUID]);
            }

            return $this;
        }

        /**
         * Holds the events which have been registered by name and their listeners.
         * @var array
         */
        protected $events;

        /**
         * Returns a uniform event.
         * @param string $eventName the event
         * @return string the uniform event
         */
        protected function getUniformEventName($eventName)
        {
            TypeValidator::IsString($eventName);

            return strtolower(trim($eventName));
        }

        /**
         * Checks if the event has listeners.
         * @param string $eventName the event to check
         * @return boolean check result
         */
        public function hasEventListeners($eventName)
        {
            $eventName = $this->getUniformEventName($eventName);

            return (isset($this->events[$eventName]) && (count($this->events[$eventName]) > 0));
        }

        /**
         * Returns the event listener queue listening to the event.
         * @param string $eventName the event to return the listener queue from
         * @return \Brickoo\Event\EventListenerQueue
         */
        public function getEventListenerQueue($eventName)
        {
            $eventName = $this->getUniformEventName($eventName);

            if (! $this->hasEventListeners($eventName)) {
                $this->events[$eventName] = new EventListenerQueue();
            }

            return $this->events[$eventName];
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct()
        {
            $this->listeners           = array();
            $this->events              = array();
            $this->processingEvents    = array();
        }

        /**
         * Notifies all event listeners.
         * @param \Brickoo\Event\Interfaces\EventInterface $Event the executed event
         * @return void
         */
        public function notify(\Brickoo\Event\Interfaces\EventInterface $Event)
        {
            $eventName = $this->getUniformEventName($Event->getName());

            if ($this->isEventProcessing($eventName)) {
                throw new Exceptions\InfiniteEventLoopException($eventName);
            }
            if ($this->hasEventListeners($eventName)) {
                if (! $Event->hasEventManager()) {
                    $Event->EventManager($this);
                }
                $this->addEventProcessing($eventName);
                $ListenerQueue = clone $this->getEventListenerQueue($eventName);
                foreach ($ListenerQueue as $listenerUID) {
                    $this->call($listenerUID, $Event);
                }
                $this->removeProcessedEvent($eventName);
            }
        }

        /**
         * Asks all event listeners until one listener returns a response.
         * @param \Brickoo\Event\Interfaces\EventInterface $Event the exectued
         * @return mixed the listener response or null if no response has been returned
         */
        public function ask(\Brickoo\Event\Interfaces\EventInterface $Event)
        {
            $response    = null;
            $eventName   = $this->getUniformEventName($Event->getName());

            if ($this->isEventProcessing($eventName)) {
                throw new Exceptions\InfiniteEventLoopException($eventName);
            }
            if ($this->hasEventListeners($eventName)) {
                if (! $Event->hasEventManager()) {
                    $Event->EventManager($this);
                }
                $this->addEventProcessing($eventName);
                $ListenerQueue = clone $this->getEventListenerQueue($eventName);
                foreach ($ListenerQueue as $listenerUID) {
                    $response = $this->call($listenerUID, $Event);
                    if ($response !== null) {
                        break;
                    }
                }
                $this->removeProcessedEvent($eventName);
            }

            return $response;
        }

        /**
         * Calls the event listener.
         * @param string $listenerUID the unique identiier of the listener
         * @param \Brickoo\Event\Interfaces\EventInterface $Event the event
         * @return mixed the listener response
         */
        public function call($listenerUID, \Brickoo\Event\Interfaces\EventInterface $Event)
        {
            if ($this->isListener($listenerUID)) {
                return call_user_func_array($this->listeners[$listenerUID]['callback'],
                    $this->getCallbackArguments($this->listeners[$listenerUID]['params'], $Event)
                );
            }
        }

        /**
         * Returns the callback arguments.
         * @param array|null $expectedParams the listener expected parameters
         * @param \Brickoo\Event\Interfaces\EventInterface $Event the Event executed
         * @return array the callback arguments
         */
        protected function getCallbackArguments($expectedParams, \Brickoo\Event\Interfaces\EventInterface $Event)
        {
            if (is_array($expectedParams) &&
                array_diff($expectedParams, array_keys($Event->getParams())) === array()
            ){
                $arguments = array();
                foreach ($expectedParams as $param) {
                    $arguments[] = $Event->getParam($param);
                }
            }
            else {
                $arguments = array($Event);
            }

            return $arguments;
        }

    }