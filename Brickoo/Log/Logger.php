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

    use Brickoo\Validator\TypeValidator;

    /**
     * Logger
     *
     * The Logger provides methods to store log entries to a specific location using a handler.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Logger implements Interfaces\LoggerInterface {

        /**
        * Holds the severity levels since Windows does have issues.
        * @var integer
        */
        const SEVERITY_EMERGENCY    = 0;
        const SEVERITY_ALERT        = 1;
        const SEVERITY_CRITICAL     = 2;
        const SEVERITY_ERROR        = 3;
        const SEVERITY_WARNING      = 4;
        const SEVERITY_NOTICE       = 5;
        const SEVERITY_INFO         = 6;
        const SEVERITY_DEBUG        = 7;

        /**
         * Holds the log handler which implements
         * the LogHandlerInterface used for logging.
         * @var \Brickoo\Log\Handler\Interfaces\LogHandlerInterface
         */
        protected $_LogHandler;

        /**
         * Retrieve the log handler dependency.
         * @return \Brickoo\Log\Handler\Interface\LogHandlerInterface
         */
        public function LogHandler() {
            return $this->_LogHandler;
        }

        /**
         * Holds the default log severity to use.
         * @var integer
         */
        protected $defaultSeverity;

        /**
         * Returns the current default log severity.
         * @return integer the default log severity
         */
        public function getDefaultSeverity() {
            return $this->defaultSeverity;
        }

        /**
         * Sets the default log severity to use.
         * @link http://www.php.net/manual/en/network.constants.php
         * @param integer $logSeverity the default log severity to set
         * @return \Brickoo\Log\Logger
         */
        public function setDefaultSeverity($severity) {
            TypeValidator::IsInteger($severity);

            $this->defaultSeverity = $severity;

            return $this;
        }

        /**
        * Class constructor.
        * Injection of the LogHandler dependency.
        * Initializes the class properties.
        * @param \Brickoo\Log\Handler\Interfaces\LogHandlerInterface $LogHandler the LogHandler dependency
        * @return void
        */
        public function __construct(\Brickoo\Log\Handler\Interfaces\LogHandlerInterface $LogHandler) {
            $this->_LogHandler           = $LogHandler;
            $this->defaultSeverity       = self::SEVERITY_INFO;
        }

        /**
         * Sends the log messages using log handler assigned.
         * @param array|string $messages the messages to send
         * @return \Brickoo\Log\Logger
         */
        public function log($messages, $severity = null) {
            if ($severity !== null) {
                TypeValidator::IsInteger($severity);
            }
            else {
                $severity = $this->getDefaultSeverity();
            }

            if (! is_array($messages)) {
                $messages = array($messages);
            }

            $this->LogHandler()->log($messages, $severity);

            return $this;
        }

        /**
         * Logs the messages of an Event.
         * @param \Brickoo\Event\Interfaces\EventInterface $Event the event executed
         * @return void
         */
        public function logEvent(\Brickoo\Event\Interfaces\EventInterface $Event) {
            if (($messages = $Event->getParam('messages')) === null) {
                return null;
            }

            if (($severity = $Event->getParam('severity')) === null) {
                $severity = $this->getDefaultSeverity();
            }

            $this->LogHandler()->log($messages, $severity);
        }

    }