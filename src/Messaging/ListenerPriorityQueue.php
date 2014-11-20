<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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
