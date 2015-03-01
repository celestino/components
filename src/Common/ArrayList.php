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

namespace Brickoo\Component\Common;

use ArrayIterator;
use Brickoo\Component\Common\Exception\InvalidIndexException;
use Brickoo\Component\Common\Assert;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * ArrayList
 *
 * Implementation of an array based list.
 */
class ArrayList implements IteratorAggregate, Countable {

    /** @var array */
    private $items;

    /**
     * Class constructor.
     * @param array $items
     * @throws \InvalidArgumentException
     */
    public function __construct(array $items = []) {
        $this->items = $items;
    }

    /**
     * Return the list item by index.
     * @param integer $index
     * @throws \Brickoo\Component\Common\Exception\InvalidIndexException
     * @return string
     */
    public function get($index) {
        Assert::isInteger($index);
        if (! $this->has($index)) {
            throw new InvalidIndexException($index);
        }
        return $this->items[$index];
    }

    /**
     * Add an item to the list.
     * @param mixed $value
     * @return \Brickoo\Component\Common\ArrayList
     */
    public function add($value) {
        array_push($this->items, $value);
        return $this;
    }

    /**
     * @param integer $index
     * @return \Brickoo\Component\Common\ArrayList
     * @throws InvalidIndexException
     */
    public function remove($index) {
        if (! $this->has($index)) {
            throw new InvalidIndexException($index);
        }
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        return $this;
    }

    /**
     * Reverse the items order.
     * @return \Brickoo\Component\Common\ArrayList
     */
    public function reverse() {
        if (! $this->isEmpty()) {
            $this->items = array_reverse($this->items, false);
        }
        return $this;
    }

    /**
     * Shuffle the items order.
     * @return \Brickoo\Component\Common\ArrayList
     */
    public function shuffle() {
        shuffle($this->items);
        return $this;
    }

    /**
     * Remove duplicate items from list.
     * @return \Brickoo\Component\Common\ArrayList
     */
    public function uniquify() {
        $this->items = array_unique($this->items, SORT_REGULAR);
        return $this;
    }

    /**
     * Check if an item with the index is available.
     * @param integer $index
     * @return boolean
     */
    public function has($index) {
        Assert::isInteger($index);
        return array_key_exists($index, $this->items);
    }

    /**
     * Return the index of the first occurrence of the value.
     * Return -1 if the value is not in list.
     * @param mixed $value
     * @return integer
     */
    public function indexOf($value) {
        $index = array_search($value, $this->items, true);
        return ($index === false) ? -1 : intval($index);
    }

    /**
     * Check if list contains a value.
     * @param mixed $value
     * @return boolean
     */
    public function contains($value) {
        return $this->indexOf($value) !== -1;
    }

    /**
     * Return the first item in list or null if empty list.
     * @throws \Brickoo\Component\Common\Exception\InvalidIndexException
     * @return mixed
     */
    public function first() {
        if ($this->isEmpty()) {
            return null;
        }

        reset($this->items);
        return current($this->items);
    }

    /**
     * Return the last item in list or null if empty list.
     * @throws \Brickoo\Component\Common\Exception\InvalidIndexException
     * @return mixed
     */
    public function last() {
        if ($this->isEmpty()) {
            return null;
        }

        $last = end($this->items);
        reset($this->items);
        return $last;
    }

    /**
     * Check if the list is empty.
     * @return boolean
     */
    public function isEmpty() {
        return empty($this->items);
    }

    /**
     * Return the list items as an array.
     * @return array
     */
    public function toArray() {
        return $this->items;
    }

    /**
     * Retrieve an external iterator containing all items.
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->items);
    }

    /**
     * Count number of list items.
     * @return integer
     */
    public function count() {
        return count($this->items);
    }

    /**
     * Return a string representation of the list.
     * The values are linefeed separated.
     * @return string
     */
    public function toString() {
        return implode("\n" ,array_map(
            function($value) {
                if (! is_scalar($value)) {
                    return gettype($value);
                }
                return is_bool($value) ? ($value ? "true" : "false") : $value;
            },
            $this->items
        ));
    }

}
