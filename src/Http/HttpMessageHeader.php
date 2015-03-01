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

use Brickoo\Component\Http\Exception\HeaderNotFoundException;
use Brickoo\Component\Common\Container;
use Brickoo\Component\Common\Assert;
use Brickoo\Component\Validation\Constraint\IsInstanceOfConstraint;
use Brickoo\Component\Validation\Validator\ConstraintValidator;

/**
 * HttpMessageHeader
 *
 * Implements a http message header.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpMessageHeader extends Container {

    use HttpHeaderFieldNameNormalizer;

    /** @param array $headerFieldList */
    public function __construct(array $headerFieldList = []) {
        parent::__construct([], new ConstraintValidator(
            new IsInstanceOfConstraint("\\Brickoo\\Component\\Http\\HttpHeaderField")
        ));
        $this->importHeaderFieldList($headerFieldList);
    }

    /**
     * Add a header field using the field name as storage key.
     * @param \Brickoo\Component\Http\HttpHeaderField $headerField
     * @return \Brickoo\Component\Http\HttpMessageHeader
     */
    public function addField(HttpHeaderField $headerField) {
        $this->set($headerField->getName(), $headerField);
        return $this;
    }

    /**
     * Return the header field by its name.
     * @param string $headerFieldName
     * @throws \Brickoo\Component\Http\Exception\HeaderNotFoundException
     * @return null|\Brickoo\Component\Http\HttpHeaderField
     */
    public function getField($headerFieldName) {
        return $this->get($headerFieldName, null);
    }

    /**
     * Covert message header fields to a header string.
     * @return string
     */
    public function toString() {
        $headerString = "";

        $headerFields = $this->normalize($this->toArray());
        foreach($headerFields as $headerField) {
            $headerString .= $headerField->toString()."\r\n";
        }

        return $headerString;
    }

    /**
     * Import the header fields from a list.
     * @param array $headerFieldList
     * @return \Brickoo\Component\Http\HttpMessageHeader
     */
    private function importHeaderFieldList(array $headerFieldList) {
        foreach ($headerFieldList as $headerField) {
            $this->addField($headerField);
        }
        return $this;
    }

}
