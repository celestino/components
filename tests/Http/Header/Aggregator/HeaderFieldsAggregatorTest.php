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

namespace Brickoo\Tests\Component\Http\Header\Aggregator;

use Brickoo\Component\Http\Header\Aggregator\HeaderFieldClassMap;
use Brickoo\Component\Http\Header\Aggregator\HeaderFieldsAggregator;
use Brickoo\Component\Http\Header\Aggregator\Strategy\StringHeaderAggregatorStrategy;
use Brickoo\Tests\Component\Http\Header\Aggregator\Assets\BrokenHeaderFieldClassMap;
use PHPUnit_Framework_TestCase;

/**
 * HeaderFieldsAggregator
 *
 * Test suite for the HeaderFieldsAggregator class.
 * @see Brickoo\Component\Http\Header\Aggregator\HeaderFieldsAggregator
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HeaderFieldsAggregatorTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\Header\Aggregator\HeaderFieldsAggregator::__construct
     * @covers Brickoo\Component\Http\Header\Aggregator\HeaderFieldsAggregator::getHeaderFields
     * @covers Brickoo\Component\Http\Header\Aggregator\HeaderFieldsAggregator::getHeaderField
     * @covers Brickoo\Component\Http\Header\Aggregator\HeaderFieldsAggregator::createMappingHeaderField
     * @covers Brickoo\Component\Http\Header\Aggregator\HeaderFieldsAggregator::createGenericHeaderField
     * @covers Brickoo\Component\Http\HttpHeaderFieldNameNormalizer::normalize
     */
    public function testGetHeaderFieldsInstances() {
        $headerMap = new HeaderFieldClassMap();
        $aggregatorStrategy = new StringHeaderAggregatorStrategy(
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/web,*/*;q=0.8\r\n".
            "Connection: keep-alive"
        );
        $headerAggregator = new HeaderFieldsAggregator($headerMap, $aggregatorStrategy);
        $headerFields = $headerAggregator->getHeaderFields();
        $this->assertCount(2, $headerFields);

        $this->assertContainsOnlyInstancesOf(
            "\\Brickoo\\Component\\Http\\HttpHeaderField",
            $headerFields
        );
        $this->assertInstanceOf(
            "\\Brickoo\\Component\\Http\\Header\\AcceptHeaderField",
            $headerFields[0]
        );
    }

    /**
     * @covers Brickoo\Component\Http\Header\Aggregator\HeaderFieldsAggregator::__construct
     * @covers Brickoo\Component\Http\Header\Aggregator\HeaderFieldsAggregator::getHeaderFields
     * @covers Brickoo\Component\Http\Header\Aggregator\HeaderFieldsAggregator::getHeaderField
     * @covers Brickoo\Component\Http\Header\Aggregator\HeaderFieldsAggregator::createMappingHeaderField
     * @covers Brickoo\Component\Http\Header\Aggregator\HeaderFieldsAggregator::createGenericHeaderField
     * @covers Brickoo\Component\Http\HttpHeaderFieldNameNormalizer::normalize
     * @covers Brickoo\Component\Http\Header\Aggregator\Exception\HeaderFieldClassNotFoundException
     * @expectedException \Brickoo\Component\Http\Header\Aggregator\Exception\HeaderFieldClassNotFoundException
     */
    public function testGetHeadersWithNotExistingHeaderThrowsException() {
        $headerMap = new BrokenHeaderFieldClassMap();
        $aggregatorStrategy = new StringHeaderAggregatorStrategy(
            "X-Broken-Field: some/value"
        );;
        $headerAggregator = new HeaderFieldsAggregator($headerMap, $aggregatorStrategy);
        $headerAggregator->getHeaderFields();
    }

}
