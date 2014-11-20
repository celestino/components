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

namespace Brickoo\Component\Http\Aggregator;

/**
 * UriAggregator
 *
 * Defines an URI aggregator.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

interface UriAggregator {

    /**
     * Returns the request scheme.
     * @return string the request scheme
     */
    public function getScheme();

    /**
     * Returns the host name or address.
     * @return string the host name or address
     */
    public function getHostname();

    /**
     * Returns the uri port.
     * @return integer the uri port
     */
    public function getPort();

    /**
     * Returns the uri path.
     * @return string the uri path
     */
    public function getPath();

    /**
     * Returns the uri query string.
     * @return string the query string
     */
    public function getQueryString();

    /**
     * Returns the uri fragment.
     * @return string the uri fragment
     */
    public function getFragment();

}
