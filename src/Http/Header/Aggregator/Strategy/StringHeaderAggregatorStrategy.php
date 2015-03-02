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

use Brickoo\Component\Common\Assert;

/**
 * StringHeaderAggregatorStrategy
 *
 * Implements a http header fields aggregator Strategy based on a header string.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class StringHeaderAggregatorStrategy implements HeaderFieldsAggregatorStrategy {

    /** @var string */
    private $headerFields;

    /**
     * Class constructor.
     * @param string $headerFields the message headerFields as string
     */
    public function __construct($headerFields) {
        Assert::isString($headerFields);
        $this->headerFields = $headerFields;
    }

    /** {@inheritDoc} */
    public function getHeaderFields() {
        $extractedHeaders = [];
        $fields = explode("\r\n", preg_replace("/\x0D\x0A[\x09\x20]+/", " ", $this->headerFields));

        foreach ($fields as $field) {
            $matches = [];
            if (preg_match("/(?<name>[^:]+): (?<value>.+)/m", $field, $matches) == 1) {
                $extractedHeaders[$matches["name"]] = trim($matches["value"]);
            }
        }
        return $extractedHeaders;
    }

}
