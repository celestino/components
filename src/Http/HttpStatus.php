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

use Brickoo\Component\Http\Exception\InvalidHttpStatusException;
use Brickoo\Component\Validation\Argument;

/**
 * HttpStatus
 *
 * Describes the http status.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpStatus extends HttpStatusCode {

    /** @var integer */
    private $status;

    /**
     * Class constructor.
     * @param integer $status the http status
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Http\Exception\InvalidHttpStatusException
     */
    public function __construct($status) {
        Argument::isInteger($status);

        if (! $this->isValid($status)) {
            throw new InvalidHttpStatusException($status);
        }

        $this->status = $status;
    }

    /**
     * Returns the status code.
     * @return integer the status code
     */
    public function getCode() {
        return $this->status;
    }

    /**
     * Returns the string representation of the http status.
     * @return string the status representation
     */
    public function toString() {
        return sprintf("%d %s", $this->status, $this->getPhrase($this->status));
    }

    /**
     * Checks if the status is valid.
     * @param string $status
     * @return boolean check result
     */
    private function isValid($status) {
        return $this->hasPhrase($status);
    }

}
