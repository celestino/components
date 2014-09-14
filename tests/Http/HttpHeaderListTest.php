<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Brickoo\Tests\Component\Http;

use Brickoo\Component\Http\Header\GenericHeader,
    Brickoo\Component\Http\HttpHeaderList,
    PHPUnit_Framework_TestCase;

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
