<?php

    /*
     * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    namespace Brickoo\Memory;

    use Brickoo\Validator\Argument;

    /**
     * Container
     *
     * Implements a simple array object to store any kind of values.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Container implements Interfaces\Container {

        /**
         * Holds the assigned values.
         * @var array
         */
        protected $container;

        /**
         * Class constructor.
         * Intializes the class properties.
         * @return void
         */
        public function __construct(array $container = array()) {
            $this->container = $container;
        }

        /**
         * Sets the value with relation to the offset.
         * @see ArrayAccess::offsetSet()
         * @return void
         */
        public function offsetSet($offset, $value) {
            $this->container[$offset] = $value;
        }

        /**
         * Checks if the offset exists.
         * @see ArrayAccess::offsetExists()
         * @return boolean check result
         */
        public function offsetExists($offset) {
            return isset($this->container[$offset]);
        }

        /**
         * Removes the offset and its value from container.
         * @see ArrayAccess::offsetUnset()
         * @return void
         */
        public function offsetUnset($offset) {
            unset($this->container[$offset]);
        }

        /**
         * Returns the vlaue from the given offset.
         * @see ArrayAccess::offsetGet()
         * @return mixed the offset value
         */
        public function offsetGet($offset) {
            if (! isset($this->container[$offset])) {
                return null;
            }

            return $this->container[$offset];
        }

        /**
         * Resets the pointer of the iteratable array.
         * @see Iterator::rewind()
         * @return mixed the value of the first element or
         * boolean false if the arary is empty
         */
        public function rewind() {
            return reset($this->container);
        }

        /**
         * Returns the value of the current element.
         * @see Iterator::current()
         * @return mixed the value of the current element
         */
        public function current() {
            return current($this->container);
        }

        /**
         * Returns the current element offset.
         * @see Iterator::key()
         * @return string|integer the current element offset
         */
        public function key() {
            return key($this->container);
        }

        /**
         * Moves the array pointer to the next entry.
         * @see Iterator::next()
         * @return mixed the value of the next element or
         * boolean false if the end of the array has been reached
         */
        public function next() {
            return next($this->container);
        }

        /**
         * Checks if the current element value is not false.
         * This could be critical if an element contains an
         * boolean false value for any reason.
         * @see Iterator::valid()
         * @return boolean check result
         */
        public function valid() {
            return ($this->current() !== false);
        }

        /**
         * Returns the number of elements contained to iterate.
         * @see Countable::count()
         * @return integer the number of elements available
         */
        public function count() {
            return count($this->container);
        }

        /** {@inheritDoc} */
        public function get($offset, $defaultValue = null) {
            Argument::IsStringOrInteger($offset);

            if (($value = $this->offsetGet($offset))) {
                return $value;
            }

            return $defaultValue;
        }

        /** {@inheritDoc} */
        public function set($offset, $value) {
            Argument::IsStringOrInteger($offset);

            $this->offsetSet($offset, $value);

            return $this;
        }

        /** {@inheritDoc} */
        public function has($offset) {
            Argument::IsStringOrInteger($offset);

            return isset($this->container[$offset]);
        }

        /** {@inheritDoc} */
        public function delete($offset) {
            Argument::IsStringOrInteger($offset);

            if ($this->has($offset)) {
                unset($this->container[$offset]);
            }

            return $this;
        }

        /** {@inheritDoc} */
        public function merge(array $container) {
            $this->container = array_merge($this->container, $container);

            return $this;
        }

        /**
         * Checks if any value are assigned.
         * @return boolean check result
         */
        public function isEmpty() {
            return empty($this->container);
        }

        /** {@inheritDoc} */
        public function flush() {
            $this->container = array();
            return $this;
        }

        /** {@inheritDoc} */
        public function fromArray(array $container) {
            $this->container = $container;

            return $this;
        }

        /** {@inheritDoc} */
        public function toArray() {
            return $this->container;
        }

        /**
         * Magic function to retrieve a value from offset.
         * @param string|integer $offset the offset to retrieve the value from
         * @return mixed the offset value
         */
        public function __get($offset) {
            return $this->offsetGet($offset);
        }

        /**
         * Magic function to set an ofset-value pair.
         * @param string|integer $offset the offset to set
         * @param mixed $value the value of the offset
         * @return void
         */
        public function __set($offset, $value) {
            $this->offsetSet($offset, $value);
        }

    }