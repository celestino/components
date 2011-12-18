<?php

    /*
     * Copyright (c) 2008-2011, Celestino Diaz Teran <celestino@users.sourceforge.net>.
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
     * The LoggerInterface for implementing the storage of messages to a location.
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id$
     */

    interface LoggerInterface
    {

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
         * Logs the passed message to the location.
         * @param string $message the message to log
         * @param integer $severity the serverity level
         * @return object reference
         */
        public function log($message, $severity = null);

    }

?>