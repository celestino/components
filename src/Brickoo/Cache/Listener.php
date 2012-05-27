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

    namespace Brickoo\Cache;

    use Brickoo\Event,
        Brickoo\Validator\TypeValidator;

    /**
     * Listener
     *
     * Implements the event listeners for caching purposes.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Listener implements Event\Interfaces\ListenerAggregate {

        /**
         * Holds an instance of the Manager class.
         * @var \Brickoo\Cache\Interfaces\Manager
         */
        protected $Manager;

        /**
         * Holds the listener priority.
         * @var integer
         */
        protected $listenerPriority;

        /**
         * Class constructor.
         * Initializes the class properties.
         * @param \Brickoo\Cache\Interfaces\Manager $Manager the Manager to inject
         * @param integer $priority the listener priority
         * @return void
         */
        public function __construct(\Brickoo\Cache\Interfaces\Manager $Manager, $priority = 0) {
            TypeValidator::IsInteger($priority);

            $this->Manager             = $Manager;
            $this->listenerPriority    = $priority;
        }

        /**
         * Aggregates the event listeners.
         * @param \Brickoo\Event\Interfaces\Manager $EventManager
         * @return void
         */
        public function aggregateListeners(\Brickoo\Event\Interfaces\Manager $EventManager) {
            $EventManager->attachListener(Events::GET,
                array($this->Manager, 'get'), $this->listenerPriority, array('id')
            );
            $EventManager->attachListener(Events::CALLBACK,
                array($this->Manager, 'getByCallback'), $this->listenerPriority, array('id', 'callback', 'arguments', 'lifetime')
            );
            $EventManager->attachListener(Events::SET,
                array($this->Manager, 'set'), $this->listenerPriority, array('id', 'content', 'lifetime')
            );
            $EventManager->attachListener(Events::DELETE,
                array($this->Manager, 'delete'), $this->listenerPriority, array('id')
            );
            $EventManager->attachListener(Events::FLUSH,
                array($this->Manager, 'flush'), $this->listenerPriority
            );
        }

    }