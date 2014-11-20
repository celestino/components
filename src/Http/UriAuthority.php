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

use Brickoo\Component\Validation\Argument;

/**
 * UriAuthority
 *
 * Implements the http uri authority part.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class UriAuthority {

    /** @var string */
    private $hostname;

    /** @var integer */
    private $portNumber;

    /**
     * Class constructor.
     * @param string $hostname
     * @param integer $portNumber
     */
    public function __construct($hostname, $portNumber = 80) {
        Argument::isString($hostname);
        Argument::isInteger($portNumber);

        $this->hostname = $hostname;
        $this->portNumber = $portNumber;
    }

    /**
     * Returns the hostname.
     * @return string the hostname
     */
    public function getHostname() {
        return $this->hostname;
    }

    /**
     * Returns the port number.
     * @return integer port number
     */
    public function getPortNumber() {
        return $this->portNumber;
    }

    /**
     * Returns the authority string representation.
     * @return string the authority representation
     */
    public function toString() {
        return sprintf("%s:%d", $this->hostname, $this->portNumber);
    }

}
