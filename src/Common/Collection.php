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
use Brickoo\Component\Common\Exception\CollectionEmptyException;
use Brickoo\Component\Common\Exception\InvalidTypeException;

/**
 * Collection
 *
 * Implements a collection of equal typed members.
 * This class can be compared to a parametric typed class.
 * Once the collection has an item type attached,
 * the type of the collection items can not be revoked.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class Collection implements \IteratorAggregate, \Countable {

    /** @var string */
    protected $type;

    /** @var array */
    protected $items;

    /**
     * Class constructor.
     * @param array $items
     */
    public function __construct(array $items = []) {
        $this->type = null;
        $this->items = [];
        $this->fromArray($items);
    }

    /**
     * Return the collection items type.
     * @return null|string
     */
    public function getType() {
        if ($this->type === null && (! $this->isEmpty())) {
            $this->type = $this->getItemType(reset($this->items));
        }
        return $this->type;
    }

    /**
     * Returns the first item from the collection.
     * The item will be removed.
     * @throws \Brickoo\Component\Common\Exception\CollectionEmptyException
     * @return mixed
     */
    public function shift() {
        if ($this->isEmpty()) {
            throw new CollectionEmptyException();
        }
        return array_shift($this->items);
    }

    /**
     * Returns the last item from the collection.
     * The item will be removed.
     * @throws \Brickoo\Component\Common\Exception\CollectionEmptyException
     * @return mixed
     */
    public function pop() {
        if ($this->isEmpty()) {
            throw new CollectionEmptyException();
        }
        return array_pop($this->items);
    }

    /**
     * Add an item into the collection.
     * @param mixed $item
     * @throws \Brickoo\Component\Common\Exception\InvalidTypeException
     * @return Collection
     */
    public function add($item) {
        $this->checkItemType($item);
        array_push($this->items, $item);
        return $this;
    }

    /**
     * Return the collection items as an iterator.
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->items);
    }

    /**
     * Check if the collection has items.
     * @return boolean check result
     */
    public function isEmpty() {
        return empty($this->items);
    }

    /** {@inheritDoc} */
    public function count() {
        return count($this->items);
    }

    /**
     * Import the items from an array.
     * @param array $items
     * @return \Brickoo\Component\Common\Container
     */
    public function fromArray(array $items) {
        foreach ($items as $item) {
            $this->add($item);
        }
        return $this;
    }

    /**
     * Return the collection items as an array.
     * @return array the collection items
     */
    public function toArray() {
        return $this->items;
    }

    /**
     * Check if the item type is valid.
     * @param mixed $item
     * @throws \Brickoo\Component\Common\Exception\InvalidTypeException
     * @return void
     */
    private function checkItemType($item) {
        if (($type = $this->getType()) !== null
            && $type !== ($itemType = $this->getItemType($item))) {
            throw new InvalidTypeException($itemType);
        };
    }

    /**
     * Return the resolved item type.
     * @param mixed $item
     * @return string
     */
    private function getItemType($item) {
        if (is_object($item)) {
            return get_class($item);
        }
        return gettype($item);
    }

}
