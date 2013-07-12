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

    namespace Brickoo\Log\Interfaces;

    /**
     * Logger
     *
     * Describes an object to store log messages.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface Logger {

        const SEVERITY_EMERGENCY    = 0;
        const SEVERITY_ALERT        = 1;
        const SEVERITY_CRITICAL     = 2;
        const SEVERITY_ERROR        = 3;
        const SEVERITY_WARNING      = 4;
        const SEVERITY_NOTICE       = 5;
        const SEVERITY_INFO         = 6;
        const SEVERITY_DEBUG        = 7;

        /**
         * Sends the log messages using log handler assigned.
         * @param array|string $messages the messages to store
         * @param integer|null $severity the severity level,
         * if null is passed the default severity of the implementation is used
         * @throws \InvalidArgumentException if an argument is not valid
         * @return void
         */
        public function log($messages, $severity = null);

    }