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

namespace Brickoo\Component\Http\Header;

use Brickoo\Component\Validation\Argument;

/**
 * GenericHeader
 *
 * Implements a generic header.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
trait CommonHeaderStructure {

    /** @var string */
    protected $headerName;

    /** @var string */
    protected $headerValue;

    /**
     * Return the header name.
     * @return string the header name
     */
    public function getName() {
        return $this->headerName;
    }

    /**
     * Set the header name.
     * @param string $headerName
     * @throws \InvalidArgumentException
     */
    public function setName($headerName) {
        Argument::isString($headerName);
        $this->headerName = $headerName;
    }

    /**
     * Return the header value.
     * @return string the header value
     */
    public function getValue() {
        return $this->headerValue;
    }

    /**
     * Set the header value.
     * @param string $headerValue
     * @throws \InvalidArgumentException
     */
    public function setValue($headerValue) {
        Argument::isString($headerValue);
        $this->headerValue = $headerValue;
    }

    /**
     * Return a string representation of the header.
     * @return string the string representation
     */
    public function toString() {
        return sprintf("%s: %s", ucfirst($this->getName()), $this->getValue());
    }

}
