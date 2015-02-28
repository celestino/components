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
 * GenericHeaderField
 *
 * Implements a generic header field.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
trait CommonHeaderFieldStructure {

    /** @var string */
    protected $headerFieldName;

    /** @var string */
    protected $headerFieldValue;

    /**
     * Return the header field name.
     * @return string
     */
    public function getName() {
        return $this->headerFieldName;
    }

    /**
     * Set the header field name.
     * @param string $headerFieldName
     * @throws \InvalidArgumentException
     */
    public function setName($headerFieldName) {
        Argument::isString($headerFieldName);
        $this->headerFieldName = $headerFieldName;
    }

    /**
     * Return the header field value.
     * @return string
     */
    public function getValue() {
        return $this->headerFieldValue;
    }

    /**
     * Set the header field value.
     * @param string $headerFieldValue
     * @throws \InvalidArgumentException
     */
    public function setValue($headerFieldValue) {
        Argument::isString($headerFieldValue);
        $this->headerFieldValue = $headerFieldValue;
    }

    /**
     * Return a string representation of the header field.
     * @return string
     */
    public function toString() {
        return sprintf("%s: %s", ucfirst($this->getName()), $this->getValue());
    }

}
