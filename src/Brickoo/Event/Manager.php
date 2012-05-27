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
     * Event Manager
     *
     * Event Manager for handling registered event listeners.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Manager implements Interfaces\Manager {

        /**
         * Holds an sttic instance of the EventManager class.
         * @var \Brickoo\Event\Interfaces\Manager
         */
        protected static $staticManager;

        /**
         * Returns the static EventManager instance.
         * @param \Brickoo\Event\Manager $Manager the Manager to inject
         * @return \Brickoo\Event\Interfaces\Manager
         */
        public static function Instance(\Brickoo\Event\Interfaces\Manager $Manager = null) {
            if (static::$staticManager === null) {
                if ($Manager !== null) {
                    static::$staticManager = $Manager;
                }
                else {
                    static::$staticManager = new self();
                }
            }

            return static::$staticManager;
        }

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
        public function isEventProcessing($eventName) {
            $eventName = $this->getUniformEventName($eventName);

            return (in_array($eventName, $this->processingEvents));
        }

        /**
         * Adds an event to the processing list.
         * @param string $eventName the event to add
         * @return \Brickoo\Event\Manager
         */
        protected function addEventProcessing($eventName) {
            $eventName = $this->getUniformEventName($eventName);

            $this->processingEvents[] = $eventName;

            return $this;
        }

        /**
         * Removes an event from the processing list.
         * @param string $eventName the vent to remove
         * @return \Brickoo\Event\Manager
         */
        protected function removeProcessedEvent($eventName) {
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
        public function isListener($listenerUID) {
            TypeValidator::IsStringAndNotEmpty($listenerUID);

            return isset($this->listeners[$listenerUID]);
        }

        /**
         * Adds an event listener.
         * @param string $eventName the event to listen to
         * @param callback $callback the callback to execute
         * @param integer $priority the listener priority factor
         * @param array|null $expectedParams the expected event parameters
         * @param callback $condition the condition which has to be true
         * @return string the listener unique identifier
         */
        public function attachListener($eventName, $callback, $priority = 0, array $expectedParams = null, $condition = null) {
            TypeValidator::IsInteger($priority);
            $eventName = $this->getUniformEventName($eventName);

            if (! is_callable($callback)) {
                throw new \InvalidArgumentException('The callback argument is not callable.');
            }

            if (($condition !== null) && (! is_callable($condition))) {
                throw new \InvalidArgumentException('The condition argument is not callable.');
            }

            $listenerUID = uniqid();

            $this->getEventListenerQueue($eventName)->insert($listenerUID, $priority);

            $this->listeners[$listenerUID] = array(
                'callback'    => $callback,
                'params'      => $expectedParams,
                'condition'   => $condition
            );

            return $listenerUID;
        }

        /**
         * Removes the event listener.
         * @param string $listenerUID the listener unique identifier
         * @return \Brickoo\Event\Manager
         */
        public function detachListener($listenerUID) {
            TypeValidator::IsStringAndNotEmpty($listenerUID);

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
        protected function getUniformEventName($eventName) {
            TypeValidator::IsStringAndNotEmpty($eventName);

            return strtolower(trim($eventName));
        }

        /**
         * Checks if the event has listeners.
         * @param string $eventName the event to check
         * @return boolean check result
         */
        public function hasEventListeners($eventName) {
            $eventName = $this->getUniformEventName($eventName);

            return (isset($this->events[$eventName]) && (count($this->events[$eventName]) > 0));
        }

        /**
         * Returns the event listener queue listening to the event.
         * @param string $eventName the event to return the listener queue from
         * @return \Brickoo\Event\ListenerQueue
         */
        public function getEventListenerQueue($eventName) {
            $eventName = $this->getUniformEventName($eventName);

            if (! $this->hasEventListeners($eventName)) {
                $this->events[$eventName] = new ListenerQueue();
            }

            return $this->events[$eventName];
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct() {
            $this->listeners           = array();
            $this->events              = array();
            $this->processingEvents    = array();
        }

        /**
         * Calls the listener to attach the aggregates event listeners.
         * @param \Brickoo\Event\Interfaces\ListenerAggregate $Listener
         * @return \Brickoo\Event\Manager
         */
        public function attachAggregatedListeners(\Brickoo\Event\Interfaces\ListenerAggregate $Listener) {
            $Listener->aggregateListeners($this);

            return $this;
        }

        /**
         * Notifies all event listeners.
         * @param \Brickoo\Event\Interfaces\Event $Event the executed event
         * @return void
         */
        public function notify(\Brickoo\Event\Interfaces\Event $Event) {
            $this->processEvent($Event);
        }

        /**
         * Notifies the event listener with the highest priority.
         * @param \Brickoo\Event\Event $Event the executed event
         * @return void
         */
        public function notifyOnce(\Brickoo\Event\Event $Event) {
            $this->processEvent($Event, true);
        }

        /**
         * Asks all event listeners until one listener returns a response.
         * @param \Brickoo\Event\Interfaces\Event $Event the executed event
         * @return mixed the event listener response or null if no response has been returned
         */
        public function ask(\Brickoo\Event\Interfaces\Event $Event) {
            return $this->processEvent($Event, true, true);
        }

        /**
         * Process the Event and returns the response if needed.
         * @param \Brickoo\Event\Interfaces\Event $Event the executed event
         * @param boolean $once flag if just the listener with the highest priority should be notified
         * @param boolean $responseNeeded flag to break the queue and return the response
         * @throws Exceptions\InfiniteEventLoopException throwed if an infinite lopp is detected
         * @return mixed the event listener response
         */
        protected function processEvent(\Brickoo\Event\Interfaces\Event $Event,  $once = false, $responseNeeded = false) {
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
                    if ($Event->isStopped() || $once === true || ($responseNeeded && $response !== null)) {
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
         * @param \Brickoo\Event\Interfaces\Event $Event the event
         * @return mixed the listener response or null if the event does not contain expected params
         */
        public function call($listenerUID, \Brickoo\Event\Interfaces\Event $Event) {
            if (! $this->isListener($listenerUID)) {
                return null;
            }

            if ($this->listeners[$listenerUID]['condition'] !== null &&
                (($success = call_user_func_array($this->listeners[$listenerUID]['condition'], array($Event))) !== true)
            ){
                return null;
            }

            if (! is_array($arguments = $this->getCallbackArguments($this->listeners[$listenerUID]['params'], $Event))) {
                return null;
            }

            return call_user_func_array($this->listeners[$listenerUID]['callback'], $arguments);
        }

        /**
         * Returns the callback arguments.
         * @param array|null $expectedParams the listener expected parameters
         * @param \Brickoo\Event\Interfaces\Event $Event the Event executed
         * @return array the callback arguments or null if the requires arguments are not available
         */
        protected function getCallbackArguments($expectedParams, \Brickoo\Event\Interfaces\Event $Event) {
            if (is_array($expectedParams) &&
                array_diff($expectedParams, array_keys($Event->getParams())) !== array()
            ){
                return null;
            }

            $arguments = array();

            if (is_array($expectedParams)) {
                foreach ($expectedParams as $param) {
                    $arguments[] = $Event->getParam($param);
                }
            }
            else {
                $arguments[] = $Event;
            }

            return $arguments;
        }

    }