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

namespace Brickoo\Event;

use Brickoo\Event\Listener,
    Brickoo\Validation\Argument;

/**
 * GenericListener
 *
 * Implements a generic listener.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class GenericListener implements Listener {

    private $eventName;

    private $listenerPriority;

    private $callback;

    private $condition;

    /**
     * Class constructor.
     * Initializes the event listener.
     * @param string $eventName
     * @param integer $priority
     * @param callable $callback
     * @param callable|null $condition
     * @return void
     */
    public function __construct($eventName, $priority, callable $callback, callable $condition = null) {
        Argument::IsString($eventName);
        Argument::IsInteger($priority);

        $this->eventName = $eventName;
        $this->listenerPriority = $priority;
        $this->callback = $callback;
        $this->condition = $condition;
    }

    /** {@inheritDoc} */
    public function getEventName() {
        return $this->eventName;
    }

    /** {@inheritDoc} */
    public function getPriority() {
        return $this->listenerPriority;
    }

    /** {@inheritDoc} */
    public function getCallback() {
        return $this->callback;
    }

    /** {@inheritDoc} */
    public function getCondition() {
        return $this->condition;
    }

}