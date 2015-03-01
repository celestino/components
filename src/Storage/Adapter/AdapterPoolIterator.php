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

namespace Brickoo\Component\Storage\Adapter;

use Brickoo\Component\Storage\Adapter\Exception\PoolIdentifierDoesNotExistException;
use Brickoo\Component\Storage\Adapter\Exception\PoolIsEmptyException;
use Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint;
use Brickoo\Component\Common\Assert;

/**
 * AdapterPoolIterator
 *
 * Implementation of an iterable caching adapter pool.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AdapterPoolIterator implements \Iterator, \Countable, AdapterPool {

    /** @var array containing entries of type \Brickoo\Component\Storage\Adapter\Adapter */
    private $poolEntries;

    /** @var array */
    private $mappingKeys;

    /** @var integer */
    private $currentPointerPosition;

    /**
     * Class constructor.
     * @param array $poolEntries
     * @throws \InvalidArgumentException if a pool entry does not match expected type
     */
    public function __construct(array $poolEntries) {
        if ((! empty($poolEntries))
            && (! (new ContainsInstancesOfConstraint("\\Brickoo\\Component\\Storage\\Adapter\\Adapter"))->matches($poolEntries))) {
                throw new \InvalidArgumentException(sprintf("%s: The pool entries must implement the Adapter interface.", __CLASS__));
        }

        $this->poolEntries = array_values($poolEntries);
        $this->mappingKeys = array_keys($poolEntries);
        $this->currentPointerPosition = 0;
    }

    /**
     * Checks if the current pool adapter entry is ready.
     * @throws \Brickoo\Component\Storage\Adapter\Exception\PoolIsEmptyException
     * @return boolean check result
     */
    public function isCurrentReady() {
        return $this->current()->isReady();
    }

    /**
     * {@inheritDoc}
     * @throws \Brickoo\Component\Storage\Adapter\Exception\PoolIsEmptyException
     * @return \Brickoo\Component\Storage\Adapter\Adapter
     */
    public function current() {
        if ($this->isEmpty() || (! isset($this->poolEntries[$this->currentPointerPosition]))) {
            throw new PoolIsEmptyException();
        }
        return $this->poolEntries[$this->currentPointerPosition];
    }

    /**
     * {@inheritDoc}
     * @return string the current pool key
     */
    public function key() {
        if (isset($this->mappingKeys[$this->currentPointerPosition])) {
            return $this->mappingKeys[$this->currentPointerPosition];
        }
        return (string)$this->currentPointerPosition;
    }

    /** {@inheritDoc} */
    public function next() {
        $this->currentPointerPosition++;
    }

    /** {@inheritDoc} */
    public function rewind() {
        $this->currentPointerPosition = 0;
    }

    /** {@inheritDoc} */
    public function valid() {
        return isset($this->poolEntries[$this->currentPointerPosition]);
    }

    /** {@inheritDoc} */
    public function select($adapterIdentifier) {
        Assert::isStringOrInteger($adapterIdentifier);

        if (! $this->has($adapterIdentifier)) {
            throw new PoolIdentifierDoesNotExistException($adapterIdentifier);
        }

        $this->currentPointerPosition = $this->getMappingPosition($adapterIdentifier);
        return $this;
    }

    /** {@inheritDoc} */
    public function remove($adapterIdentifier) {
        if (! $this->has($adapterIdentifier)) {
            throw new PoolIdentifierDoesNotExistException($adapterIdentifier);
        }

        $mappingPosition = $this->getMappingPosition($adapterIdentifier);
        unset($this->poolEntries[$mappingPosition]);
        unset($this->mappingKeys[$mappingPosition]);

        $this->poolEntries = array_values($this->poolEntries);
        $this->mappingKeys = array_values($this->mappingKeys);

        if ($this->currentPointerPosition > 0 && $this->currentPointerPosition >= $mappingPosition) {
            --$this->currentPointerPosition;
        }

        return $this;
    }

    /** {@inheritDoc} */
    public function has($adapterIdentifier) {
        Assert::isStringOrInteger($adapterIdentifier);
        return in_array($adapterIdentifier, $this->mappingKeys, false);
    }

    /** {@inheritDoc} */
    public function isEmpty() {
        return empty($this->poolEntries);
    }

    /** {@inheritDoc} */
    public function count() {
        return count($this->poolEntries);
    }

    /**
     * Returns the position inside the adapter pool.
     * @param string $adapterIdentifier
     * @return integer the position
     */
    private function getMappingPosition($adapterIdentifier) {
        return intval(array_search($adapterIdentifier, $this->mappingKeys, false));
    }

}
