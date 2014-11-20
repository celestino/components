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

namespace Brickoo\Tests\Component\Http;

use Brickoo\Component\Http\Header\GenericHeader;
use Brickoo\Component\Http\HttpHeaderList;
use PHPUnit_Framework_TestCase;

/**
 * HttpHeaderListTest
 *
 * Test suite for the HttpHeaderList class.
 * @see Brickoo\Component\Http\HttpHeaderList
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpHeaderListTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\HttpHeaderList::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorAcceptsOnlyHttpHeaderElements() {
        new HttpHeaderList([new \stdClass()]);
    }

    /**
     * @covers Brickoo\Component\Http\HttpHeaderList::__construct
     * @covers Brickoo\Component\Http\HttpHeaderList::add
     * @covers Brickoo\Component\Http\HttpHeaderList::get
     * @covers Brickoo\Component\Http\HttpHeaderList::has
     */
    public function testAddAndGetListElement() {
        $header = new GenericHeader("Host", "brickoo.com");
        $headerList = new HttpHeaderList();
        $this->assertFalse($headerList->has(0));
        $this->assertSame($headerList, $headerList->add($header));
        $this->assertTrue($headerList->has(0));
        $this->assertSame($header, $headerList->get(0));
    }

    /**
     * @covers Brickoo\Component\Http\HttpHeaderList::get
     * @covers \Brickoo\Component\Http\Exception\HeaderListElementNotAvailableException
     * @expectedException \Brickoo\Component\Http\Exception\HeaderListElementNotAvailableException
     */
    public function testGetAnListElementWhichIsNotAvailableHeaderThrowsException() {
        $headerList = new HttpHeaderList();
        $headerList->get(0);
    }

    /**
     * @covers Brickoo\Component\Http\HttpHeaderList::remove
     * @covers Brickoo\Component\Http\HttpHeaderList::has
     */
    public function testRemoveListElement() {
        $headerList = new HttpHeaderList([new GenericHeader("Host", "brickoo.com")]);
        $this->assertTrue($headerList->has(0));
        $this->assertSame($headerList, $headerList->remove(0));
        $this->assertFalse($headerList->has(0));
    }

    /** @covers Brickoo\Component\Http\HttpHeaderList::first */
    public function testRetrieveFirstElementFromList() {
        $header1 = new GenericHeader("X-Unit-Test", "test case 1");
        $header2 = new GenericHeader("X-Unit-Test", "test case 2");
        $headerList = new HttpHeaderList([$header1, $header2]);
        $this->assertSame($header1, $headerList->first());

    }

    /** @covers Brickoo\Component\Http\HttpHeaderList::last */
    public function testRetrieveLastElementFromList() {
        $header1 = new GenericHeader("X-Unit-Test", "test case 1");
        $header2 = new GenericHeader("X-Unit-Test", "test case 2");
        $headerList = new HttpHeaderList([$header1, $header2]);
        $this->assertSame($header2, $headerList->last());
    }

    /** @covers Brickoo\Component\Http\HttpHeaderList::isEmpty */
    public function testListEmptyCheck() {
        $header = new GenericHeader("Host", "brickoo.com");
        $headerList = new HttpHeaderList();
        $this->assertTrue($headerList->isEmpty());
        $headerList->add($header);
        $this->assertFalse($headerList->isEmpty());

    }

    /**
     * @covers Brickoo\Component\Http\HttpHeaderList::toArray
     * @covers Brickoo\Component\Http\HttpHeaderList::count
     */
    public function testRetrieveListAsAnArrayAndIsCountable() {
        $expectedElementsCount = 2;

        $header1 = new GenericHeader("X-Unit-Test", "test case 1");
        $header2 = new GenericHeader("X-Unit-Test", "test case 2");
        $headerList = new HttpHeaderList([$header1, $header2]);
        $this->assertEquals($expectedElementsCount, count($headerList));
        $arrayList = $headerList->toArray();
        $this->assertInternalType("array", $arrayList);
        $this->assertEquals($expectedElementsCount, count($arrayList));
    }

    /** @covers Brickoo\Component\Http\HttpHeaderList::getIterator */
    public function testListIsTraversable() {
        $header1 = new GenericHeader("X-Unit-Test", "test case 1");
        $header2 = new GenericHeader("X-Unit-Test", "test case 2");
        $headerList = new HttpHeaderList([$header1, $header2]);
        foreach ($headerList as $header) {
            $this->assertInstanceOf("\\Brickoo\\Component\\Http\\Header\\GenericHeader", $header);
        }
    }

    /** @covers Brickoo\Component\Http\HttpHeaderList::toString */
    public function testListToStringConversion() {
        $expectedResult = "X-Unit-Test: test case 1\r\nX-Unit-Test: test case 2\r\n";

        $header1 = new GenericHeader("X-Unit-Test", "test case 1");
        $header2 = new GenericHeader("X-Unit-Test", "test case 2");
        $headerList = new HttpHeaderList([$header1, $header2]);
        $this->assertEquals($expectedResult, $headerList->toString());
    }

}
