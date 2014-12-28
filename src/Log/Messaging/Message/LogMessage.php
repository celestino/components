<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Component\Log\Messaging\Message;

use Brickoo\Component\Log\Messaging\Messages;
use Brickoo\Component\Messaging\GenericMessage;
use Brickoo\Component\Validation\Argument;

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
        Argument::isInteger($severity);
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
