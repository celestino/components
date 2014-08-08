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

namespace Brickoo\Component\Error\Messaging\Listener;

use Brickoo\Component\Error\Messaging\Messages;
use Brickoo\Component\Error\Messaging\Message\ErrorMessage;
use Brickoo\Component\Messaging\Listener;
use Brickoo\Component\Messaging\Message;
use Brickoo\Component\Messaging\MessageDispatcher;
use Brickoo\Component\Log\Logger;
use Brickoo\Component\Log\Messaging\Message\LogMessage;

/**
 * ErrorLogMessageListener
 *
 * Implements a listener for error log messages.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ErrorLogMessageListener implements Listener {

    /** {@inheritDoc} */
    public function getMessageName() {
        return Messages::ERROR;
    }

    /** {@inheritDoc} */
    public function getPriority() {
        return 0;
    }

    /** {@inheritDoc} */
    public function handleMessage(Message $message, MessageDispatcher $messageDispatcher) {
        if ($message instanceof ErrorMessage) {
            $messageDispatcher->dispatch(new LogMessage([$message->getErrorMessage()], Logger::SEVERITY_ERROR));
        }
    }

}