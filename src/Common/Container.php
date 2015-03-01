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
use Brickoo\Component\Common\Exception\InvalidValueTypeException;
use Brickoo\Component\Common\Assert;
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
        Assert::isStringOrInteger($key);
        return array_key_exists($key, $this->container);
    }

    /**
     * Returns the value of the given key.
     * @param string|integer $key
     * @param mixed $defaultValue
     * @return mixed the key associated value otherwise the default value
     */
    public function get($key, $defaultValue = null) {
        Assert::isStringOrInteger($key);

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
        Assert::isStringOrInteger($key);

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
        Assert::isStringOrInteger($key);

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
     * Imports the elements from an array.
     * @param array $container
     * @throws \Brickoo\Component\Common\Exception\InvalidValueTypeException
     * @return \Brickoo\Component\Common\Container
     */
    public function fromArray(array $container) {
        foreach ($container as $key => $value) {
            if (! $this->isValueTypeValid($value)) {
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
