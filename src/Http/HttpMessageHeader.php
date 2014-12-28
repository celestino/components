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
use Brickoo\Component\Validation\Argument;
use Brickoo\Component\Validation\Constraint\IsInstanceOfConstraint;
use Brickoo\Component\Validation\Validator\ConstraintValidator;

/**
 * HttpMessageHeader
 *
 * Implements a http message header.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpMessageHeader extends Container {

    use HttpHeaderNormalizer;

    /** @param array $headerLists */
    public function __construct(array $headerLists = []) {
        parent::__construct($headerLists, new ConstraintValidator(
            new IsInstanceOfConstraint("\\Brickoo\\Component\\Http\\HttpHeaderList")
        ));
    }

    /**
     * Add a header using the header name as storage key.
     * @param \Brickoo\Component\Http\HttpHeader $header
     * @return \Brickoo\Component\Http\HttpMessageHeader
     */
    public function addHeader(HttpHeader $header) {
        $headerName = $header->getName();

        if (! $this->contains($headerName)) {
            $this->set($headerName, new HttpHeaderList());
        }

        $this->get($headerName)->add($header);
        return $this;
    }

    /**
     * Return the header by its name.
     * The first header from the concrete header list will be returned.
     * @param string $headerName
     * @throws \Brickoo\Component\Http\Exception\HeaderNotFoundException
     * @return \Brickoo\Component\Http\HttpHeader
     */
    public function getHeader($headerName) {
        return $this->getHeaderList($headerName)->first();
    }

    /**
     * Return the header list of a header.
     * @param string $headerName
     * @throws \Brickoo\Component\Http\Exception\HeaderNotFoundException
     * @return \Brickoo\Component\Http\HttpHeaderList
     */
    public function getHeaderList($headerName) {
        Argument::isString($headerName);
        if (! $this->contains($headerName)) {
            throw new HeaderNotFoundException($headerName);
        }
        return $this->get($headerName);
    }

    /**
     * Covert message headers to a header string.
     * @return string the representation of the message headers
     */
    public function toString() {
        $headerString = "";

        $headerLists = $this->normalizeHeaders($this->toArray());
        foreach($headerLists as $headerList) {
            $headerString .= $headerList->toString();
        }

        return $headerString;
    }

}
