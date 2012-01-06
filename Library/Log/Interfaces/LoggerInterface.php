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

    namespace Brickoo\Library\Log\Interfaces;

    /**
     * LoggerInterface
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     * @version $Id$
     */

    interface LoggerInterface
    {

        /**
         * Retrieve the log handler assigned.
         * @throws Core\Exceptions\DependencyNotAvailableException if no log handler instance is assigned
         * @return object log handler implementing the LogHandlerInterface
         */
        public function getLogHandler();

        /**
         * Sets the log handler instance to use for logging.
         * @param LogHandlerInterface $LogHandler the log handler instance
         * @throws Core\Exceptions\DependencyOverwriteException if trying to override the log handler
         * @return object reference
         */
        public function injectLogHandler(\Brickoo\Library\Log\Interfaces\LogHandlerInterface $LogHandler);

        /**
         * Returns the current default log severity.
         * @return integer the default log severity
         */
        public function getDefaultSeverity();

        /**
         * Sets the default log severity to use.
         * @link http://www.php.net/manual/en/network.constants.php
         * @param integer $logSeverity the default log severity to set
         * @return object reference
         */
        public function setDefaultSeverity($severity);

        /**
        * Clears the class properties.
        * @return object reference
        */
        public function reset();

        /**
         * Sends the log messages using log handler assigned.
         * @param array|string $messages the messages to send
         * @return void
         */
        public function log($messages, $severity = null);

    }

?>