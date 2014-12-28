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

namespace Brickoo\Component\Messaging;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Brickoo\Component\Messaging\Exception\ResponseNotAvailableException;

/**
 * MessageResponseCollection
 *
 * Implements a collection of messaging listeners responses.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageResponseCollection implements Countable, IteratorAggregate {

    /** @var array */
    private $responsesContainer;

    public function __construct() {
        $this->responsesContainer = [];
    }

    /**
     * Returns the first response from the collection stack.
     * The response will be removed from the list.
     * @throws \Brickoo\Component\Messaging\Exception\ResponseNotAvailableException
     * @return mixed the first collected response
     */
    public function shift() {
        if ($this->isEmpty()) {
            throw new ResponseNotAvailableException();
        }
        return array_shift($this->responsesContainer);
    }

    /**
     * Returns the last response from the collection stack.
     * The response will be removed from the list.
     * @throws \Brickoo\Component\Messaging\Exception\ResponseNotAvailableException
     * @return mixed the last collected response
     */
    public function pop() {
        if ($this->isEmpty()) {
            throw new ResponseNotAvailableException();
        }
        return array_pop($this->responsesContainer);
    }

    /**
     * Push a response into the local container.
     * @param mixed $response
     * @return \Brickoo\Component\Messaging\MessageResponseCollection
     */
    public function push($response) {
        if ($response !== null) {
            $this->responsesContainer[] = $response;
        }
        return $this;
    }

    /**
     * Returns all listened responses as an iterator.
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->responsesContainer);
    }

    /**
     * Checks if the collection has responses.
     * @return boolean check result
     */
    public function isEmpty() {
        return empty($this->responsesContainer);
    }

    /** {@inheritDoc} */
    public function count() {
        return count($this->responsesContainer);
    }

}
