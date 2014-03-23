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

namespace Brickoo\Component\Error\Listener;

use Brickoo\Component\Error\Messages,
    Brickoo\Component\Error\Message\ExceptionMessage,
    Brickoo\Component\Log\Logger,
    Brickoo\Component\Log\LogMessage,
    Brickoo\Component\Messaging\Listener,
    Brickoo\Component\Messaging\Message,
    Brickoo\Component\Messaging\MessageDispatcher;

/**
 * ExceptionLogMessageListener
 *
 * Implements a listener for exception log messages.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ExceptionLogMessageListener implements Listener {

    /** {@inheritDoc} */
    public function getMessageName() {
        return Messages::EXCEPTION;
    }

    /** {@inheritDoc} */
    public function getPriority() {
        return 0;
    }

    /** {@inheritDoc} */
    public function handleMessage(Message $message, MessageDispatcher $messageDispatcher) {
        if ($message instanceof ExceptionMessage) {
            $messageDispatcher->dispatch(new LogMessage($this->getExceptionMessage($message->getException()), Logger::SEVERITY_CRITICAL));
        }
    }

    /**
     * Returns the exceptions messages.
     * @param \Exception $exception
     * @return array the exception messages
     */
    private function getExceptionMessage(\Exception $exception) {
        $messages = [$this->generateLogMessage($exception)];
        if ($previousException = $exception->getPrevious()) {
            $messages = array_merge($messages, $this->getExceptionMessage($previousException));
        }
        return $messages;
    }

    /**
     * Returns a well formed exception message.
     * @param \Exception $exception
     * @return string the formed exception message
     */
    private function generateLogMessage(\Exception $exception) {
        return sprintf(
            "[#%s] Uncaught Exception: %s -=- Message: %s",
            $exception->getCode(), get_class($exception), $exception->getMessage()
        );
    }

}