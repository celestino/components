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

namespace Brickoo\Component\Http\Aggregator;

use Brickoo\Component\Http\Header\GenericHeader;
use Brickoo\Component\Http\HttpHeaderList;
use Brickoo\Component\Http\HttpHeaderNormalizer;
use Brickoo\Component\Http\Aggregator\Exception\HeaderClassNotFoundException;
use Brickoo\Component\Http\Aggregator\Strategy\HeaderAggregatorStrategy;

/**
 * HeaderAggregator
 *
 * Implements a http header solver.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HeaderAggregator {

    use HttpHeaderNormalizer;

    /** @var array */
    private $headerMap;

    /** @var \Brickoo\Component\Http\Aggregator\Strategy\HeaderAggregatorStrategy */
    private $resolverStrategy;

    /** @var array */
    private $loadedHeaders;

    /**
     * Class constructor.
     * @param array $headerMap a map array containing the header nam as key and target class as value
     * @param \Brickoo\Component\Http\Aggregator\Strategy\HeaderAggregatorStrategy $resolverStrategy
     */
    public function __construct(array $headerMap, HeaderAggregatorStrategy $resolverStrategy) {
        $this->headerMap = $headerMap;
        $this->resolverStrategy = $resolverStrategy;
        $this->loadedHeaders = [];
    }

    /**
     * Return a collection of http headers.
     * Duplicate headers will be overridden.
     * @return array
     */
    public function getHeaders() {
        $this->loadHeaders();

        $headers = [];
        foreach ($this->loadedHeaders as $headerName => $headerValue) {
            $headers[] = $this->getHeader($headerName, $headerValue);
        }
        return $headers;
    }

    /**
     * Return a collection of http header lists containing
     * the http header. Does support header duplicates.
     * @return array
     */
    public function getHeaderLists() {
        $this->loadHeaders();

        $headerLists = [];
        foreach ($this->loadedHeaders as $headerName => $headerValue) {
            if (! isset($headerLists[$headerName])) {
                $headerLists[$headerName] = new HttpHeaderList();
            }
            $headerLists[$headerName]->add($this->getHeader($headerName, $headerValue));
        }
        return $headerLists;
    }

    /**
     * Load the headers into local cache.
     * @return \Brickoo\Component\Http\Aggregator\HeaderAggregator
     */
    private function loadHeaders() {
        if (empty($this->loadedHeaders)) {
            $this->loadedHeaders = $this->normalizeHeaders($this->resolverStrategy->getHeaders());
        }
        return $this;
    }

    /**
     * Check if the header has a mapping class.
     * @param string $headerName
     * @return boolean check result
     */
    private function hasMappingHeaderClass($headerName) {
        return isset($this->headerMap[$headerName]);
    }

    /**
     * Return the corresponding header instance.
     * @param string $headerName
     * @param string $headerValue
     * @return \Brickoo\Component\Http\HttpHeader
     */
    private function getHeader($headerName, $headerValue) {
        if ($this->hasMappingHeaderClass($headerName)) {
            return $this->createMappingHeader($this->headerMap[$headerName], $headerValue);
        }
        return $this->createGenericHeader($headerName, $headerValue);
    }

    /**
     * Create a header instance from a mapping class.
     * @param string $headerClass
     * @param string $headerValue
     * @throws \Brickoo\Component\Http\Aggregator\Exception\HeaderClassNotFoundException
     * @return \Brickoo\Component\Http\HttpHeader
     */
    private function createMappingHeader($headerClass, $headerValue) {
        try {
            if (! class_exists($headerClass)) {
                throw new Exception("Unable to load mapping header class.");
            }
        }
        catch (\Exception $exception) {
            throw new HeaderClassNotFoundException($headerClass, $exception);
        }
        return new $headerClass($headerValue);
    }

    /**
     * Create a generic header with the header name and value.
     * @param string $headerName
     * @param string $headerValue
     * @return \Brickoo\Component\Http\Header\GenericHeader
     */
    private function createGenericHeader($headerName, $headerValue) {
        return new GenericHeader($headerName, $headerValue);
    }

}
