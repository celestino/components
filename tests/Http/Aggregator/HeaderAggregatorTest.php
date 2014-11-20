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

namespace Brickoo\Tests\Component\Http\Aggregator;

use Brickoo\Component\Http\Aggregator\HeaderAggregator;
use PHPUnit_Framework_TestCase;

/**
 * HeaderAggregator
 *
 * Test suite for the HeaderAggregator class.
 * @see Brickoo\Component\Http\Aggregator\HeaderAggregator
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HeaderAggregatorTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::__construct
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::getHeaders
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::loadHeaders
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::hasMappingHeaderClass
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::getHeader
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::createMappingHeader
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::createGenericHeader
     * @covers Brickoo\Component\Http\HttpHeaderNormalizer::normalizeHeaders
     */
    public function testGetHeadersWithValidMap() {
        $headerMap = include realpath(__DIR__)."/Assets/validHeader.map";
        $headerLoader = $this->getHeaderAggregatorStrategyStub();
        $headerLoader->expects($this->any())
                     ->method("getHeaders")
                     ->will($this->returnValue([
                         "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/web,*/*;q=0.8",
                         "Connection" => "keep-alive"
                     ]));
        $headerAggregator = new HeaderAggregator($headerMap, $headerLoader);
        $headers = $headerAggregator->getHeaders();
        $this->assertInternalType("array", $headers);

        foreach ($headers as $header) {
            $this->assertInstanceOf("\\Brickoo\\Component\\Http\\HttpHeader", $header);
            if ($header->getName() == "Accept") {
                $this->assertInstanceOf("\\Brickoo\\Component\\Http\\Header\\AcceptHeader", $header);
            }
        }
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::__construct
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::getHeaderLists
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::loadHeaders
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::hasMappingHeaderClass
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::getHeader
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::createMappingHeader
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::createGenericHeader
     * @covers Brickoo\Component\Http\HttpHeaderNormalizer::normalizeHeaders
     */
    public function testGetHeaderLists() {
        $headerMap = include realpath(__DIR__)."/Assets/validHeader.map";
        $headerLoader = $this->getHeaderAggregatorStrategyStub();
        $headerLoader->expects($this->any())
                     ->method("getHeaders")
                     ->will($this->returnValue([
                         "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/web,*/*;q=0.8",
                         "Connection" => "keep-alive"
                     ]));
        $headerAggregator = new HeaderAggregator($headerMap, $headerLoader);
        $headerLists = $headerAggregator->getHeaderLists();
        $this->assertInternalType("array", $headerLists);
        $this->assertEquals(2, count($headerLists));

        foreach ($headerLists as $headerName => $headerList) {
            $this->assertInstanceOf("\\Brickoo\\Component\\Http\\HttpHeaderList", $headerList);
            if ($headerName == "Accept") {
                $this->assertInstanceOf("\\Brickoo\\Component\\Http\\Header\\AcceptHeader", $headerList->first());
            }
        }
    }

    /**
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::__construct
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::getHeaders
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::loadHeaders
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::hasMappingHeaderClass
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::getHeader
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::createMappingHeader
     * @covers Brickoo\Component\Http\Aggregator\HeaderAggregator::createGenericHeader
     * @covers Brickoo\Component\Http\HttpHeaderNormalizer::normalizeHeaders
     * @covers Brickoo\Component\Http\Aggregator\Exception\HeaderClassNotFoundException
     * @expectedException \Brickoo\Component\Http\Aggregator\Exception\HeaderClassNotFoundException
     */
    public function testGetHeadersWithNotExistingHeaderThrowsException() {
        $headerMap = include realpath(__DIR__)."/Assets/wrongHeader.map";
        $headerLoader = $this->getHeaderAggregatorStrategyStub();
        $headerLoader->expects($this->any())
                     ->method("getHeaders")
                     ->will($this->returnValue([
                         "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8"
                     ]));
        $headerAggregator = new HeaderAggregator($headerMap, $headerLoader);
        $headerAggregator->getHeaders();
    }

    /**
     * Returns a header Aggregator loader stub.
     * @return \Brickoo\Component\Http\Aggregator\Strategy\HeaderAggregatorStrategy
     */
    private function getHeaderAggregatorStrategyStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Http\\Aggregator\\Strategy\\HeaderAggregatorStrategy")
            ->disableOriginalConstructor()
            ->getMock();
    }

}
