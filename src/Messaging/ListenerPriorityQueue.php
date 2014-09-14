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

/**
 * ListenerPriorityQueue
 *
 * Implements a listener priority queue.
 */
class ListenerPriorityQueue implements \IteratorAggregate, \Countable {

    /** @var array */
    private $items;

    public function __construct() {
        $this->items = array();
    }

    /**
     * Insert an entry to the queue.
     * @param string $listenerUID
     * @param integer $priority
     * @return \Brickoo\Component\Messaging\ListenerPriorityQueue
     */
    public function insert($listenerUID, $priority) {
        $this->items[$listenerUID] = $priority;
        arsort($this->items);
        return $this;
    }

    /**
     * Remove an entry from the queue.
     * @param string $listenerUID
     * @return \Brickoo\Component\Messaging\ListenerPriorityQueue
     */
    public function remove($listenerUID) {
        if (isset($this->items[$listenerUID])) {
            unset($this->items[$listenerUID]);
        }
        return $this;
    }

    /**
     * Check if the listener queue is empty.
     * @return boolean check result
     */
    public function isEmpty() {
        return empty($this->items);
    }

    /**
     * {@inheritdoc}
     * @return \ArrayIterator containing the listener uid`s.
     */
    public function getIterator() {
        return new \ArrayIterator(array_keys($this->items));
    }

    /** {@inheritdoc} */
    public function count() {
        return count($this->items);
    }

}
