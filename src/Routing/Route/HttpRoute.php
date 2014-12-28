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

namespace Brickoo\Component\Routing\Route;

use Brickoo\Component\Validation\Argument;

/**
 * HttpRoute
 *
 * Implements a http route which can be configured to match http requests.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpRoute extends GenericRoute {

    /** @var string */
    private $method;

    /** @var string */
    private $scheme;

    /** @var string */
    private $hostname;

    /**
     * Returns the http method(s) allowed listening to.
     * @return string the http methods allowed as a regular expression
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Sets the route method listening to.
     * @param string $method
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Routing\Route\HttpRoute
     */
    public function setMethod($method) {
        Argument::isString($method);
        $this->method = strtoupper($method);
        return $this;
    }

    /**
     * Returns the scheme allowed listening to.
     * @return string the scheme allowed as a regular expression
     */
    public function getScheme() {
        return $this->scheme;
    }

    /**
     * Sets the scheme required.
     * @param string $scheme
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Routing\Route\HttpRoute
     */
    public function setScheme($scheme) {
        Argument::isString($scheme);
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * Returns the hostname(s) allowed listening to.
     * @return string the host names allowed as a regular expression
     */
    public function getHostname() {
        return $this->hostname;
    }

    /**
     * Sets the hostname listening to.
     * @param string $hostname
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Routing\Route\HttpRoute
     */
    public function setHostname($hostname) {
        Argument::isString($hostname);
        $this->hostname = $hostname;
        return $this;
    }

}
