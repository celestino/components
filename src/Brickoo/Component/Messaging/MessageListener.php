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

namespace Brickoo\Component\Messaging;

use Brickoo\Component\Messaging\Listener,
    Brickoo\Component\Messaging\Message,
    Brickoo\Component\Messaging\MessageDispatcher,
    Brickoo\Component\Validation\Argument;

/**
 * MessageListener
 *
 * Implements a generic message listener.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageListener implements Listener {

    /** @var string */
    private $messageName;

    /** @var integer */
    private $priority;

    /** @var callable */
    private $callback;

    /**
     * @param string $messageName
     * @param integer $priority
     * @param callable $callback
     * @return void
     */
    public function __construct($messageName, $priority, callable $callback) {
        Argument::IsString($messageName);
        Argument::IsInteger($priority);

        $this->messageName = $messageName;
        $this->priority = $priority;
        $this->callback = $callback;
    }

    /** {@inheritDoc} */
    public function getMessageName() {
        return $this->messageName;
    }

    /** {@inheritDoc} */
    public function getPriority() {
        return $this->priority;
    }

    /** {@inheritDoc} */
    public function handleMessage(Message $message, MessageDispatcher $dispatcher) {
        call_user_func_array($this->callback, [$message, $dispatcher]);
    }

}