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

namespace Brickoo\Component\Http\Header\Aggregator\Strategy;

/**
 * PhpHeaderFieldsAggregatorStrategy
 *
 * Implements a http header fields aggregator strategy based on the global server values.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class PhpHeaderFieldsAggregatorStrategy implements HeaderFieldsAggregatorStrategy {

    /** @var array */
    private $serverVars;

    /** @param array $serverVars */
    public function __construct(array $serverVars) {
        $this->serverVars = $serverVars;
    }

    /** {@inheritDoc} */
    public function getHeaderFields() {
        $headerFields = $this->getPhpExtractedHttpHeaderFields();

        if (function_exists("apache_request_headers") && ($apacheHeaders = apache_request_headers())) {
            $headerFields = array_merge($headerFields, $apacheHeaders);
        }
        return $headerFields;
    }

    /**
     * Return the http header field list.
     * @return array header list
     */
    private function getPhpExtractedHttpHeaderFields() {
        $headerFields = [];
        foreach ($this->serverVars as $key => $value) {
            if (substr($key, 0, 5) == "HTTP_") {
                $headerFields[substr($key, 5)] = $value;
            }
        }
        return $headerFields;
    }

}
