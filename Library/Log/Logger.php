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

    namespace Brickoo\Library\Log;

    use Brickoo\Library\Core;
    use Brickoo\Library\Log\Interfaces;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Logger
     *
     * The LogManager is used to store logs to a specific location.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     * @version $Id$
     */

    class Logger implements Interfaces\LoggerInterface
    {

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
         * @var Brickoo\Library\Log\Interfaces\LogHandlerInterface
         */
        protected $LogHandler;

        /**
         * Retrieve the log handler assigned.
         * @throws Core\Exceptions\DependencyNotAvailableException if no log handler instance is assigned
         * @return object log handler implementing the LogHandlerInterface
         */
        public function getLogHandler()
        {
            if (! $this->LogHandler instanceof Interfaces\LogHandlerInterface)
            {
                throw new Core\Exceptions\DependencyNotAvailableException('LogHandlerInterface');
            }

            return $this->LogHandler;
        }

        /**
         * Sets the log handler instance to use for logging.
         * @param LogHandlerInterface $LogHandler the log handler instance
         * @throws Core\Exceptions\DependencyOverwriteException if trying to override the log handler
         * @return object reference
         */
        public function injectLogHandler(\Brickoo\Library\Log\Interfaces\LogHandlerInterface $LogHandler)
        {
            if ($this->LogHandler instanceof Interfaces\LogHandlerInterface)
            {
                throw new Core\Exceptions\DependencyOverwriteException('LogHandlerInterface');
            }

            $this->LogHandler = $LogHandler;

            return $this;
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
        public function getDefaultSeverity()
        {
            return $this->defaultSeverity;
        }

        /**
         * Sets the default log severity to use.
         * @link http://www.php.net/manual/en/network.constants.php
         * @param integer $logSeverity the default log severity to set
         * @return object reference
         */
        public function setDefaultSeverity($severity)
        {
            TypeValidator::IsInteger($severity);

            $this->defaultSeverity = $severity;

            return $this;
        }

        /**
        * Class constructor.
        * Initializes the class properties.
        * @return void
        */
        public function __construct()
        {
            $this->reset();
        }

        /**
        * Clears the class properties.
        * @return object reference
        */
        public function reset()
        {
            $this->LogHandler         = null;
            $this->defaultSeverity    = self::SEVERITY_INFO;

            return $this;
        }

        /**
         * Sends the log messages using log handler assigned.
         * @param array|string $messages the messages to send
         * @return object reference
         */
        public function log($messages, $severity = null)
        {
            if ($severity !== null)
            {
                TypeValidator::IsInteger($severity);
            }
            else
            {
                $severity = $this->getDefaultSeverity();
            }

            if (! is_array($messages))
            {
                $messages = array($messages);
            }

            $this->getLogHandler()->log($messages, $severity);

            return $this;
        }

    }

?>