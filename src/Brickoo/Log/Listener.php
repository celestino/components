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

    namespace Brickoo\Log;

    use Brickoo\Event,
        Brickoo\Validator\Argument;

    /**
     * Listener
     *
     * Implements an event listener for listening on log events.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Listener implements Event\Interfaces\ListenerAggregate {

        /** @var \Brickoo\Log\Interfaces\Logger */
        protected $Logger;

        /** @var integer */
        protected $listenerPriority;

        /**
         * Classs constructor.
         * @param \Brickoo\Log\Interfaces\Logger $Logger
         * @param integer $priority the priority level
         * @return void
         */
        public function __construct(\Brickoo\Log\Interfaces\Logger $Logger, $priority = 0) {
            Argument::IsInteger($priority);

            $this->Logger = $Logger;
            $this->listenerPriority = $priority;
        }

        /**
         * Attaches the aggregated event listeners.
         * @param \Brickoo\Event\Interfaces\Manager $EventManager
         * @return void
         */
        public function attachListeners(\Brickoo\Event\Interfaces\Manager $EventManager) {
            $EventManager->attach(new Event\Listener(Events::LOG, array($this, 'handleLogEvent'), $this->listenerPriority));
        }

        /**
         * Handle the event to log messages.
         * @param \Brickoo\Log\Event\Interfaces\LogEvent $LogEvent
         * @param \Brickoo\Event\Interfaces\Manager $Manager
         * @return void
         */
        public function handleLogEvent(\Brickoo\Log\Event\Interfaces\LogEvent $LogEvent, \Brickoo\Event\Interfaces\Manager $Manager) {
            $this->Logger->log($LogEvent->getMessages(), $LogEvent->getSeverity());
        }

    }