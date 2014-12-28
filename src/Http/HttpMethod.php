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

use Brickoo\Component\Http\Exception\InvalidHttpMethodException;
use Brickoo\Component\Validation\Argument;

/**
 * HttpMethod
 *
 * Describes a http method.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpMethod {

    /** http methods */
    const HEAD = "HEAD";
    const OPTIONS = "OPTIONS";
    const TRACE = "TRACE";
    const CONNECT = "CONNECT";
    const GET = "GET";
    const POST = "POST";
    const PUT = "PUT";
    const PATCH = "PATCH";
    const DELETE = "DELETE";


    /** @var string */
    private $method;

    /**
     * Class constructor
     * @param string $method the http method
     * @throws \Brickoo\Component\Http\Exception\InvalidHttpMethodException
     */
    public function __construct($method) {
        Argument::isString($method);

        if (! $this->isValid($method)) {
            throw new InvalidHttpMethodException($method);
        }

        $this->method = $method;
    }

    /**
     * Returns the method string representation in uppercase.
     * return string the method representation
     */
    public function toString() {
        return $this->method;
    }

    /**
     * Checks if the method is valid.
     * @param string $method
     * @return boolean result
     */
    private function isValid($method) {
        return defined("static::".$method);
    }

}
