<?php

    /*
     * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
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

    namespace Brickoo\Event;

    /**
     * Event
     *
     * Defines an event holding corresponding parameters and sender reference.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface Event {

        /**
         * Returns the sender object reference which triggered the event.
         * @return object the sender object reference or null if not set
         */
        public function getSender();

        /**
         * Stops the event of been called by other listeners.
         * @return \Brickooo\Event\Event
         */
        public function stop();

        /**
         * Checks if the event has been stopped.
         * @return boolean check result
         */
        public function isStopped();

        /**
         * Returns the event name.
         * @return string the event name
         */
        public function getName();

        /**
         * Returns the event parameters.
         * @return array the assigned event parameters
         */
        public function getParams();

        /**
         * Returns the parameter value of the identifier.
         * If the parameter does not exist, the default value is returned.
         * @param string $identifier the identifier to return the value from
         * @return mixed the parmeter value or null if not set
         */
        public function getParam($identifier, $defaultValue = null);

        /**
         * Checks if the identifier is a available event parameter.
         * @param string $identifier the identifier to check the avaibility
         * @return boolean check result
         */
        public function hasParam($identifier);

        /**
         * Checks if the arguments are available event parameters.
         * @param string any number of arguments to check
         * @return boolean check result
         */
        public function hasParams();

    }