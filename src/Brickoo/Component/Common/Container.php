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

namespace Brickoo\Component\Common;

use ArrayIterator;
use Brickoo\Component\Common\Exception\InvalidValueTypeException;
use Brickoo\Component\Validation\Argument;
use Brickoo\Component\Validation\Validator\Validator;

/**
 * Container
 *
 * Implements a simple array object to store any kind of values.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class Container implements \IteratorAggregate, \Countable {

    /** @var array */
    protected $container;

    /** @var null|\Brickoo\Component\Validation\Validator\Validator */
    protected $validator;

    /**
     * Class constructor.
     * @param array $container
     * @param null|Validator $validator
     */
    public function __construct(array $container = [], Validator $validator = null) {
        $this->container = [];
        $this->validator = $validator;
        $this->fromArray($container);
    }

    /**
     * Returns an external array iterator.
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->container);
    }

    /**
     * Returns the number of elements contained to iterate.
     * @link http://php.net/manual/en/countable.count.php
     * @return integer the number of elements available
     */
    public function count() {
        return count($this->container);
    }

    /**
     * Checks if the container contains the key.
     * @param string|integer $key
     * @throws \InvalidArgumentException
     * @return boolean check result
     */
    public function contains($key) {
        Argument::isStringOrInteger($key);
        return isset($this->container[$key]);
    }

    /**
     * Returns the value of the given key.
     * @param string|integer $key
     * @param mixed $defaultValue
     * @param string $key
     * @return mixed the key associated value otherwise the default value
     */
    public function get($key, $defaultValue = null) {
        Argument::isStringOrInteger($key);

        if ($this->contains($key)) {
            return $this->container[$key];
        }

        return $defaultValue;
    }

    /**
     * Sets a key-value pair into the container.
     * @param string|integer $key
     * @param mixed $value
     * @throws \Brickoo\Component\Common\Exception\InvalidValueTypeException
     * @return \Brickoo\Component\Common\Container
     */
    public function set($key, $value) {
        Argument::isStringOrInteger($key);

        if (! $this->isValueTypeValid($value)) {
            throw new InvalidValueTypeException($value);
        }

        $this->container[$key] = $value;
        return $this;
    }

    /**
     * Removes the container entry by its key.
     * @param string|integer $key
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Common\Container
     */
    public function remove($key) {
        Argument::isStringOrInteger($key);

        if ($this->contains($key)) {
            unset($this->container[$key]);
        }

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
     * Clear the container.
     * @return \Brickoo\Component\Common\Container
     */
    public function clear() {
        $this->container = [];
        return $this;
    }

    /**
     * Imports the container values from an array.
     * @param array $container the container to import
     * @throws \Brickoo\Component\Common\Exception\InvalidValueTypeException
     * @return \Brickoo\Component\Common\Container
     */
    public function fromArray(array $container) {
        foreach ($container as $key => $value) {
            if (!$this->isValueTypeValid($value)) {
                throw new InvalidValueTypeException($value);
            }
            $this->container[$key] = $value;
        }
        return $this;
    }

    /**
     * Returns the container entries as an array.
     * @return array the container entries
     */
    public function toArray() {
        return $this->container;
    }

    /**
     * Checks if the values type is valid.
     * @param mixed $value
     * @return boolean check result
     */
    protected function isValueTypeValid($value) {
        return ($this->validator === null || $this->validator->isValid($value));
    }

}
