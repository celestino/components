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

namespace Brickoo\Component\Http;

use ArrayIterator;
use Brickoo\Component\Http\Exception\HeaderListElementNotAvailableException;
use Brickoo\Component\Validation\Argument;
use Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * HttpHeaderList
 *
 * Implementation of a http header list.
 */
class HttpHeaderList implements IteratorAggregate, Countable {

    /** @var array */
    private $elements;

    /**
     * Class constructor.
     * @param array $elements
     * @throws \InvalidArgumentException
     */
    public function __construct( array $elements = []) {
        if (! (new ContainsInstancesOfConstraint("\\Brickoo\\Component\\Http\\HttpHeader"))
            ->matches($elements)) {
            throw new InvalidArgumentException("HttpHeaderList must contain only HttpHeader elements.");
        }
        $this->elements = $elements;
    }

    /**
     * Add a http header to the list.
     * @param \Brickoo\Component\Http\HttpHeader $header
     * @return \Brickoo\Component\Http\HttpHeaderList
     */
    public function add(HttpHeader $header) {
        $this->elements[] = $header;
        return $this;
    }

    /**
     * Return the list element by list position.
     * @param integer $position
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Http\Exception\HeaderListElementNotAvailableException
     * @return \Brickoo\Component\Http\HttpHeader
     */
    public function get($position) {
        Argument::isInteger($position);
        if (! $this->has($position)) {
            throw new HeaderListElementNotAvailableException($position);
        }
        return $this->elements[$position];
    }

    /**
     * Check if an element is available
     * on a position.
     * @param integer $position
     * @return boolean check result
     */
    public function has($position) {
        Argument::isInteger($position);
        return isset($this->elements[$position]);
    }

    /**
     * Remove a element by its position from the list.
     * @param integer $position
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Http\HttpHeaderList
     */
    public function remove($position) {
        Argument::isInteger($position);
        if ($this->has($position)) {
            unset($this->elements[$position]);
        }
        return $this;
    }

    /**
     * Return the first element in list.
     * @throws \Brickoo\Component\Http\Exception\HeaderListElementNotAvailableException
     * @return HttpHeader
     */
    public function first() {
        return $this->get(0);
    }

    /**
     * Return the last element in list.
     * @throws \Brickoo\Component\Http\Exception\HeaderListElementNotAvailableException
     * @return HttpHeader
     */
    public function last() {
        return $this->get(count($this) - 1);
    }

    /**
     * Check if the list ist empty.
     * @return boolean check result
     */
    public function isEmpty() {
        return empty($this->elements);
    }

    /**
     * Return the list elements.
     * @return array list elements
     */
    public function toArray() {
        return $this->elements;
    }

    /**
     * Retrieve an external iterator
     * containing all elements.
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->elements);
    }

    /**
     * Count number of list elements.
     * @return integer the number of list elements.
     */
    public function count() {
        return count($this->elements);
    }

    /**
     * Return a string representation of the header list.
     * @return string the header in list
     */
    public function toString() {
        $headerRepresentation = "";

        foreach ($this as $header) {
            $headerRepresentation .= sprintf("%s\r\n", $header->toString());
        }

        return $headerRepresentation;
    }

}
