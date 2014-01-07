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

use Brickoo\Event\Event,
    Brickoo\Event\EventDispatcher,
    Brickoo\Event\Listener;

/**
 * EventProcessor
 *
 * Implements an event processor for validating listeners
 * and executing the responsible listeners callbacks.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class EventProcessor {

    /**
     * Handles the event by calling the corresponding listener.
     * @param \Brickoo\Event\EventDispatcher $eventDispatcher
     * @param \Brickoo\Event\Event $event the executed event
     * @param \Brickoo\Event\Listener $listener the listener to execute
     * @return mixed the event listener response or null if no response has been returned by the listener
     */
    public function handle(EventDispatcher $eventDispatcher, Event $event, Listener $listener) {
        if ($this->hasValidCondition($eventDispatcher, $event, $listener)) {
             return call_user_func_array($listener->getCallback(), array($event, $eventDispatcher));
        }
    }

    /**
     * Checks if event does match the spected condition of a listener.
     * @param \Brickoo\Event\EventDispatcher $eventDispatcher
     * @param \Brickoo\Event\Event $event the executed event
     * @param \Brickoo\Event\Listener $listener the listener to execute
     * @return boolean check result
     */
    private function hasValidCondition(EventDispatcher $eventManager, Event $event, Listener $listener) {
        if (($condition = $listener->getCondition()) === null) {
            return true;
        }
        return (boolean)call_user_func_array($condition, array($event, $eventManager));
    }

}