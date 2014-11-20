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

use Brickoo\Component\Common\Container;
use Brickoo\Component\Validation\Argument;

/**
 * UriQuery
 *
 * Implements a http query parameters container.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class UriQuery extends Container {

    /**
     * Converts the query parameters to a request query string.
     * The query string is encoded as of the RFC3986.
     * @return string the query string
     */
    public function toString() {
        return str_replace("+", "%20", http_build_query($this->toArray()));
    }

    /**
     * Imports the query parameters from the extracted key/value pairs.
     * @param string $query the query to extract the pairs from
     * @throws \InvalidArgumentException if the argument is not valid
     * @return \Brickoo\Component\Http\UriQuery
     */
    public function fromString($query) {
        Argument::isString($query);

        if (($position = strpos($query, "?")) !== false) {
            $query = substr($query, $position + 1);
        }

        parse_str(rawurldecode($query), $importedQueryParameters);

        if (is_array($importedQueryParameters)) {
            $this->fromArray($importedQueryParameters);
        }

        return $this;
    }

}
