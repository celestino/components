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
