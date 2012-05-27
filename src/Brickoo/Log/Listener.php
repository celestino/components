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

    namespace Brickoo\Log;

    use Brickoo\Event,
        Brickoo\Validator\TypeValidator;

    /**
     * Listener
     *
     * Implements the event listener for log purposes.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Listener implements Event\Interfaces\ListenerAggregate {

        /**
         * Holds an istance of the Logger class.
         * @var \Brickoo\Log\Interfaces\Logger
         */
        protected $Logger;

        /**
         * Holds the listener priority level for this listener.
         * @var integer
         */
        protected $listenerPriority;

        /**
         * Classs constructor.
         * Initializes the class properties.
         * @param \Brickoo\Log\Interfaces\Logger $Logger the Logger dependency to inject
         * @param integer $priority the priority level
         * @returnvoid
         */
        public function __construct(\Brickoo\Log\Interfaces\Logger $Logger, $priority = 0) {
            TypeValidator::IsInteger($priority);

            $this->Logger              = $Logger;
            $this->listenerPriority    = $priority;
        }

        /**
         * Aggreagtes the event listeners.
         * @param \Brickoo\Event\Interfaces\Manager $EventManager the Manager to register to
         * @return \Brickoo\Log\Listener
         */
        public function aggregateListeners(\Brickoo\Event\Interfaces\Manager $EventManager) {
            $EventManager->attachListener(Events::LOG, array($this->Logger, 'handleEvent'), $this->listenerPriority);

            return $this;
        }

    }