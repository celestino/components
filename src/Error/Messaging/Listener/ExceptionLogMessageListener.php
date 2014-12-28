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

namespace Brickoo\Component\Error\Messaging\Listener;

use Brickoo\Component\Error\Messaging\Messages;
use Brickoo\Component\Error\Messaging\Message\ExceptionMessage;
use Brickoo\Component\Log\Logger;
use Brickoo\Component\Log\Messaging\Message\LogMessage;
use Brickoo\Component\Messaging\Listener;
use Brickoo\Component\Messaging\Message;
use Brickoo\Component\Messaging\MessageDispatcher;

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
