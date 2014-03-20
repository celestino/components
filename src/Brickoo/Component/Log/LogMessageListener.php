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

namespace Brickoo\Component\Log;

use Brickoo\Component\Messaging\Listener,
    Brickoo\Component\Messaging\Message,
    Brickoo\Component\Messaging\MessageDispatcher,
    Brickoo\Component\Validation\Argument;

/**
 * LogMessageListener
 *
 * Implements a log message listener.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class LogMessageListener implements Listener {

    /** @var \Brickoo\Component\Log\Logger */
    protected $logger;

    /** @var integer */
    protected $listenerPriority;

    /**
     * @param \Brickoo\Component\Log\Logger $logger
     * @param integer $priority the priority level
     */
    public function __construct(Logger $logger, $priority = 0) {
        Argument::IsInteger($priority);
        $this->logger = $logger;
        $this->listenerPriority = $priority;
    }

    /** {@inheritDoc} */
    public function getMessageName() {
        return Messages::LOG;
    }

    /** {@inheritDoc} */
    public function getPriority() {
        return $this->listenerPriority;
    }

    /** {@inheritDoc} */
    public function handleMessage(Message $message, MessageDispatcher $messageDispatcher) {
        if ($message instanceof LogMessage) {
            $this->logger->log($message->getMessages(), $message->getSeverity());
        }
    }

}