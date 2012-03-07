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
     * Events class which is passed to an listener as argument.
     * Contains the event name, caller and parameters.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Event implements Interfaces\EventInterface
    {

        /**
        * Holds the class dependencies.
        * @var array
        */
        protected $dependencies;

        /**
         * Returns the dependency holded, created or overwritten.
         * @param string $name the name of the dependency
         * @param string $interface the interface which has to be implemented by the dependency
         * @param callback $callback the callback to create a new dependency
         * @param object $Dependency the dependecy to inject
         * @return object Request if overwritten otherwise the dependency
         */
        protected function getDependency($name, $interface, $callback, $Dependency = null)
        {
            if ($Dependency instanceof $interface) {
                $this->dependencies[$name] = $Dependency;
                return $this;
            }
            elseif ((! isset($this->dependencies[$name])) || (! $this->dependencies[$name] instanceof $interface)) {
                $this->dependencies[$name] = call_user_func($callback, $this);
            }
            return $this->dependencies[$name];
        }

        /**
         * Lazy initialization of the EventManager dependency
         * @param \Brickoo\Event\Interfaces\ManagerInterface $EventManager the EventManager dependency to inject
         * @return \Brickoo\Event\Interfaces\ManagerInterface
         */
        public function EventManager(\Brickoo\Event\Interfaces\ManagerInterface $EventManager = null)
        {
            return $this->getDependency(
                'EventManager',
                '\Brickoo\Event\Interfaces\ManagerInterface',
                function() {return new Manager();},
                $EventManager
            );
        }

        /**
         * Checks if the EventManager depndency is injected.
         * @return boolean check result
         */
        public function hasEventManager()
        {
            return isset($this->dependencies['EventManager']);
        }

        /**
         * Holds the stop flag.
         * @var boolean
         */
        protected $stopped;

        /**
         * Stops the event of been called by other listeners.
         * @retunr \Brickooo\Event\Event
         */
        public function stop()
        {
            $this->stopped = true;
            return $this;
        }

        /**
         * Checks if the event has been stopped.
         * @return boolean check result
         */
        public function isStopped()
        {
            return ($this->stopped === true);
        }

        /**
         * Holds the event name.
         * @var string
         */
        protected $name;

        /**
         * Returns the event name.
         * @return string the event name
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * Holds the event parameters.
         * @var array
         */
        protected $params;

        /**
         * Returns the event  parameters.
         * @return array the assigned event parameters
         */
        public function getParams()
        {
            return $this->params;
        }

        /**
         * Returns the parameter value of the identifier
         * @param string $identifier the identifier to return the value from
         * @return mixed the parmeter value or null if not set
         */
        public function getParam($identifier)
        {
            TypeValidator::IsString($identifier);

            if (! $this->hasParam($identifier)) {
                return null;
            }

            return $this->params[$identifier];
        }

        /**
         * Checks if the identifier is an parameter.
         * @param string $identifier the idetifier to check
         * @return boolean check result
         */
        public function hasParam($identifier)
        {
            TypeValidator::IsString($identifier);
            return isset($this->params[$identifier]);
        }

        /**
         * Holds the sender object reference notifing this event.
         * @var object reference to the sender
         */
        protected $Sender;

        /**
         * Returns the sender object reference.
         * @return object the sender object reference or null if not set
         */
        public function Sender()
        {
            return $this->Sender;
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @param string $name the event name
         * @param object $Sender the sender object
         * @param array $parameters the parameters to add to the event
         * @return void
         */
        public function __construct($name, $Sender = null, array $parameters = array())
        {
            TypeValidator::IsString($name);

            if($Sender !== null) {
                TypeValidator::isObject($Sender);
            }

            $this->name      = $name;
            $this->Sender    = $Sender;
            $this->params    = $parameters;
            $this->stopped   = false;
        }

    }