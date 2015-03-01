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

namespace Brickoo\Component\Http\Header\Aggregator;

use Brickoo\Component\Http\Header\GenericHeaderField;
use Brickoo\Component\Http\HttpHeaderFieldNameNormalizer;
use Brickoo\Component\Http\Header\Aggregator\Exception\HeaderFieldClassNotFoundException;
use Brickoo\Component\Http\Header\Aggregator\Strategy\HeaderFieldsAggregatorStrategy;

/**
 * HeaderFieldsAggregator
 *
 * Implements a http header fields aggregator.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HeaderFieldsAggregator {

    use HttpHeaderFieldNameNormalizer;

    /** @var \Brickoo\Component\Http\Header\Aggregator\HeaderFieldClassMap */
    private $headerFieldClassMap;

    /** @var \Brickoo\Component\Http\Header\Aggregator\Strategy\HeaderFieldsAggregatorStrategy */
    private $resolverStrategy;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Http\Header\Aggregator\HeaderFieldClassMap $headerFieldClassMap
     * @param \Brickoo\Component\Http\Header\Aggregator\Strategy\HeaderFieldsAggregatorStrategy $resolverStrategy
     */
    public function __construct(HeaderFieldClassMap $headerFieldClassMap, HeaderFieldsAggregatorStrategy $resolverStrategy) {
        $this->headerFieldClassMap = $headerFieldClassMap;
        $this->resolverStrategy = $resolverStrategy;
    }

    /**
     * Return a collection of http headers.
     * Duplicate header fields will be overridden.
     * @throws \Brickoo\Component\Http\Header\Aggregator\Exception\HeaderFieldClassNotFoundException
     * @return array
     */
    public function getHeaderFields() {
        $aggregatedHeaderFields = $this->normalize($this->resolverStrategy->getHeaderFields());

        $headerFields = [];
        foreach ($aggregatedHeaderFields as $headerFieldName => $headerFieldValue) {
            $headerFields[] = $this->getHeaderField($headerFieldName, $headerFieldValue);
        }
        return $headerFields;
    }

    /**
     * Return the corresponding header field instance.
     * @param string $headerFieldName
     * @param string $headerFieldValue
     * @return \Brickoo\Component\Http\HttpHeaderField
     */
    private function getHeaderField($headerFieldName, $headerFieldValue) {
        if ($this->headerFieldClassMap->hasClass($headerFieldName)) {
            return $this->createMappingHeaderField($headerFieldName, $headerFieldValue);
        }
        return $this->createGenericHeaderField($headerFieldName, $headerFieldValue);
    }

    /**
     * Create a header field instance from a mapping class.
     * @param string $headerFieldName
     * @param string $headerFieldValue
     * @throws \Brickoo\Component\Http\Header\Aggregator\Exception\HeaderFieldClassNotFoundException
     * @return \Brickoo\Component\Http\HttpHeaderField
     */
    private function createMappingHeaderField($headerFieldName, $headerFieldValue) {
        try {
            $headerFieldClass = $this->headerFieldClassMap->getClass($headerFieldName);
            if (!class_exists($headerFieldClass)) {
                throw new \Exception("Unable to load mapping header class.");
            }
        }
        catch (\Exception $exception) {
            throw new HeaderFieldClassNotFoundException($headerFieldName, $exception);
        }
        return new $headerFieldClass($headerFieldValue);
    }

    /**
     * Create a generic header field.
     * @param string $headerFieldName
     * @param string $headerFieldValue
     * @return \Brickoo\Component\Http\Header\GenericHeaderField
     */
    private function createGenericHeaderField($headerFieldName, $headerFieldValue) {
        return new GenericHeaderField($headerFieldName, $headerFieldValue);
    }

}
