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

namespace Brickoo\Log;

use Brickoo\Messaging\GenericMessage,
    Brickoo\Log\Messages,
    Brickoo\Validation\Argument;

/**
 * LogMessage
 *
 * Implementation of a log message which holds logs messages and their severity.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class LogMessage extends GenericMessage {

    /**
     * Message parameters.
     * @var string
     */
    const PARAM_LOG_MESSAGES = "messages";
    const PARAM_LOG_SEVERITY = "severity";

    /**
     * @param array $messages
     * @param integer $severity
     */
    public function __construct(array $messages, $severity) {
        Argument::IsInteger($severity);
        parent::__construct(Messages::LOG, null, [self::PARAM_LOG_MESSAGES => $messages, self::PARAM_LOG_SEVERITY => $severity]);
    }

    /**
     * Returns the messages to log.
     * @return array the log messages
     */
    public function getMessages() {
        return $this->getParam(self::PARAM_LOG_MESSAGES);
    }

    /**
     * Returns the severity level.
     * @return integer the severity level
     */
    public function getSeverity() {
        return $this->getParam(self::PARAM_LOG_SEVERITY);
    }

}