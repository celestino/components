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

    use Brickoo\Validator\Argument;

    /**
     * Logger
     *
     * The Logger provides methods to store log entries to a specific location using a handler.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Logger implements Interfaces\Logger {

        /** @var \Brickoo\Log\Handler\Interfaces\Handler */
        protected $Handler;

        /** @var integer */
        protected $defaultSeverityLevel;

        /**
        * Class constructor.
        * @param \Brickoo\Log\Handler\Interfaces\Handler $LogHandler
        * @param integer $defaultSeverityLevel the default severity to use
        * @throws \InvalidArgumentException if an argument is not valid
        * @return void
        */
        public function __construct(\Brickoo\Log\Handler\Interfaces\Handler $Handler, $defaultSeverityLevel = self::SEVERITY_INFO) {
            Argument::IsInteger($defaultSeverityLevel);

            $this->Handler = $Handler;
            $this->defaultSeverityLevel = $defaultSeverityLevel;
        }

        /** {@inheritDoc} */
        public function log($messages, $severity = null) {
            if ($severity === null) {
                $severity = $this->defaultSeverityLevel;
            }

            Argument::IsInteger($severity);

            if (! is_array($messages)) {
                $messages = array($messages);
            }

            $Constraint = new \Brickoo\Validator\Constraint\TraversableContainsType("string");
            if (! $Constraint->assert($messages)) {
                return Argument::ThrowInvalidArgumentException($messages, "The error messages must be of type string.");
            }

            $this->Handler->log($messages, $severity);
        }

    }