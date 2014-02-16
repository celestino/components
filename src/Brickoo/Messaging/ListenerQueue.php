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

namespace Brickoo\Messaging;

use SplPriorityQueue;

/**
 * ListenerQueue
 *
 * Implements a priority oriented queue for listeners.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ListenerQueue extends SplPriorityQueue {

    /**
     * Holds the priority extra serial to keep the
     * order of listeners registration with the same priority.
     * @var integer
     */
    private $serial;

    /**
     * Class constructor.
     * @return void
     */
    public function __construct() {
        $this->serial = PHP_INT_MAX;
    }

    /**
     * Inserts a listener identifier with his priority into the queue.
     * @param string $listenerUID the listener unique idenitifier
     * @param integer $priority the priority of the listener
     * @return \Brickoo\Messaging\ListenerQueue
     */
    public function insert($listenerUID, $priority) {
        parent::insert($listenerUID, array($priority, $this->serial--));
        return $this;
    }

}