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

namespace Brickoo\Component\IoC\Definition\Container;

use Brickoo\Component\IoC\Definition\Container\Exception\DefinitionNotAvailableException;
use Brickoo\Component\Validation\Argument;

/**
 * Implements an abstract definition container.
 */
abstract class DefinitionContainer implements \IteratorAggregate, \Countable {

    /** @var array */
    protected $entries = [];

    /**
     * Check if the definition container is empty.
     * @return boolean check result
     */
    public function isEmpty() {
        return empty($this->entries);
    }

    /**
     * Check if the definition contains an entry.
     * @param string $entryKey
     * @throws \InvalidArgumentException
     * @return boolean check result
     */
    public function contains($entryKey) {
        Argument::isString($entryKey);
        return isset($this->entries[$entryKey]);
    }

    /**
     * Add an entry to the list.
     * @param string $key
     * @param mixed $value
     * @return \Brickoo\Component\IoC\Definition\Container\DefinitionContainer
     */
    public function add($key, $value) {
        $this->entries[$key] = $value;
        return $this;
    }

    /**
     * Return an entry by its key..
     * @param string $entryKey
     * @throws \Brickoo\Component\IoC\Definition\Container\Exception\DefinitionNotAvailableException
     * @return \Brickoo\Component\IoC\Definition\ArgumentDefinition
     */
    public function get($entryKey) {
        Argument::isString($entryKey);

        if (! $this->contains($entryKey)) {
            throw new DefinitionNotAvailableException($entryKey);
        }

        return $this->entries[$entryKey];
    }

    /**
     * Remove an entry from container.
     * @param string $entryKey
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\IoC\Definition\Container\DefinitionContainer
     */
    public function remove($entryKey) {
        Argument::isString($entryKey);

        if ($this->contains($entryKey)) {
            unset($this->entries[$entryKey]);
        }
        return $this;
    }

    /**
     * Return the entries definitions as a list.
     * @return array
     */
    public function getAll() {
        return array_values($this->entries);
    }

    /**
     * Retrieve an array iterator containing the entries.
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new \ArrayIterator($this->getAll());
    }

    /**
     * Count the entries in the container.
     * @link http://php.net/manual/en/countable.count.php
     * @return integer the amount of injections
     */
    public function count() {
        return count($this->entries);
    }

}
