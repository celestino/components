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

namespace Brickoo\Component\Memory;

use ArrayIterator,
    Brickoo\Component\Validation\Argument;

/**
 * Container
 *
 * Implements a simple array object to store any kind of values.
 * This class can be used for example as a class properties container.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class Container implements \ArrayAccess, \IteratorAggregate, \Countable {

    /**
     * Holds the assigned values.
     * @var array
     */
    protected $container;

    /**
     * Class constructor.
     * Initializes the class properties.
     */
    public function __construct(array $container = []) {
        $this->container = $container;
    }

    /**
     * Sets the value with relation to the offset.
     * @see ArrayAccess::offsetSet()
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value) {
        $this->container[$offset] = $value;
    }

    /**
     * Checks if the offset exists.
     * @see ArrayAccess::offsetExists()
     * @param mixed $offset
     * @return boolean check result
     */
    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    /**
     * Removes the offset and its value from container.
     * @see ArrayAccess::offsetUnset()
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    /**
     * Returns the vlaue from the given offset.
     * @see ArrayAccess::offsetGet()
     * @param mixed $offset
     * @return mixed the offset value
     */
    public function offsetGet($offset) {
        if (! isset($this->container[$offset])) {
            return null;
        }

        return $this->container[$offset];
    }

    /**
     * Returns an external iterator.
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->container);
    }

    /**
     * Returns the number of elements contained to iterate.
     * @see Countable::count()
     * @return integer the number of elements available
     */
    public function count() {
        return count($this->container);
    }

    /**
     * Returns the value of the given offset.
     * @param string|integer $offset the offset to retrieve the value from
     * @param mixed $defaultValue the default value if the offset does not exist
     * @return mixed the offset contained value or the default value passed
     */
    public function get($offset, $defaultValue = null) {
        Argument::IsStringOrInteger($offset);

        if (($value = $this->offsetGet($offset))) {
            return $value;
        }

        return $defaultValue;
    }

    /**
     * Sets an offset-value pair to the array object.
     * @param string|integer $offset the offset to add
     * @param mixed $value the value of the offset
     * @return \Brickoo\Component\Memory\Container
     */
    public function set($offset, $value) {
        Argument::IsStringOrInteger($offset);

        $this->offsetSet($offset, $value);

        return $this;
    }

    /**
     * Checks if the element is available.
     * @param string|integer $offset the element to check
     * @return boolean check result
     */
    public function has($offset) {
        Argument::IsStringOrInteger($offset);
        return $this->offsetExists($offset);
    }

    /**
     * Deletes the element and returns his value.
     * @param string|integer $offset the offset to delete
     * @return \Brickoo\Component\Memory\Container
     */
    public function delete($offset) {
        Argument::IsStringOrInteger($offset);

        if ($this->has($offset)) {
            $this->offsetUnset($offset);
        }

        return $this;
    }

    /**
     * Merges the passed container with the currently hold.
     * @param array $container the container to merge
     * @return \Brickoo\Component\Memory\Container
     */
    public function merge(array $container) {
        $this->container = array_merge($this->container, $container);

        return $this;
    }

    /**
     * Checks if the container is empty.
     * @return boolean check result
     */
    public function isEmpty() {
        return empty($this->container);
    }

    /**
     * Flushes all content of the container.
     * @return \Brickoo\Component\Memory\Container
     */
    public function flush() {
        $this->container = [];
        return $this;
    }

    /**
     * Imports the container values from an array.
     * @param array $container the container to import
     * @return \Brickoo\Component\Memory\Container
     */
    public function fromArray(array $container) {
        $this->container = $container;
        return $this;
    }

    /**
     * Returns the hold key/value pairs as an array.
     * @return array the hold key/value pairs
     */
    public function toArray() {
        return $this->container;
    }

}