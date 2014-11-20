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

namespace Brickoo\Component\Http;

use Brickoo\Component\Http\Exception\InvalidHttpVersionException;
use Brickoo\Component\Validation\Argument;

/**
 * HttpVersion
 *
 * Describes the http version.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpVersion {

    /** http versions */
    const HTTP_1_0 = "HTTP/1.0";
    const HTTP_1_1 = "HTTP/1.1";
    const HTTP_2_0 = "HTTP/2";
    const HTTP_3_0 = "HTTP/3";

    /** @var string */
    private $version;

    /**
     * Class constructor.
     * @param string $version the http version
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Http\Exception\InvalidHttpVersionException
     */
    public function __construct($version) {
        Argument::isString($version);

        if (! $this->isValid($version)) {
            throw new InvalidHttpVersionException($version);
        }

        $this->version = $version;
    }

    /**
     * Returns the string representation of the http version.
     * @return string the version representation
     */
    public function toString() {
        return $this->version;
    }

    /**
     * Checks if the version is valid.
     * @param string $version
     * @return boolean check result
     */
    private function isValid($version) {
        return in_array($version, [self::HTTP_1_0, self::HTTP_1_1, self::HTTP_2_0]);
    }

}
