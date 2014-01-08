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

namespace Brickoo\Tests\Event;

use Brickoo\Event\ResponseCollection,
    PHPUnit_Framework_TestCase;

/**
 * ResponseCollectionTest
 *
 * Test suite for the Collection class.
 * @see Brickoo\Event\ResponseCollection
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ResponseCollectionTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Event\ResponseCollection::__construct */
    public function testConstructor() {
        $responsesContainer = array("response1", "response2");
        $Collection = new ResponseCollection($responsesContainer);
        $this->assertAttributeEquals($responsesContainer, "responsesContainer", $Collection);
    }

    /** @covers Brickoo\Event\ResponseCollection::shift */
    public function testShift() {
        $responsesContainer = array("response1", "response2");
        $Collection = new ResponseCollection($responsesContainer);
        $this->assertEquals("response1", $Collection->shift());
        $this->assertAttributeEquals(array("response2"), "responsesContainer", $Collection);
    }

    /**
     * @covers Brickoo\Event\ResponseCollection::shift
     * @covers Brickoo\Event\Exception\ResponseNotAvailableException
     * @expectedException Brickoo\Event\Exception\ResponseNotAvailableException
     */
    public function testShiftEmptyListThrowsResponseNotAvailableException() {
        $Collection = new ResponseCollection(array());
        $Collection->shift();
    }

    /** @covers Brickoo\Event\ResponseCollection::pop */
    public function testPop() {
        $responsesContainer = array("response1", "response2");
        $Collection =  new ResponseCollection($responsesContainer);
        $this->assertEquals("response2", $Collection->pop());
        $this->assertAttributeEquals(array("response1"), "responsesContainer", $Collection);
    }

    /**
     * @covers Brickoo\Event\ResponseCollection::pop
     * @covers Brickoo\Event\Exception\ResponseNotAvailableException
     * @expectedException Brickoo\Event\Exception\ResponseNotAvailableException
     */
    public function testPopEmptyListThrowsResponseNotAvailableException() {
        $Collection = new ResponseCollection(array());
        $Collection->pop();
    }

    /** @covers Brickoo\Event\ResponseCollection::getAll */
    public function testGetAll() {
        $responsesContainer = array("response1", "response2");
        $Collection =  new ResponseCollection($responsesContainer);
        $this->assertEquals($responsesContainer, $Collection->getAll());
    }

    /** @covers Brickoo\Event\ResponseCollection::isEmpty */
    public function testIsEmpty() {
        $Collection = new ResponseCollection(array());
        $this->assertTrue($Collection->isEmpty());

        $Collection = new ResponseCollection(array("response"));
        $this->assertFalse($Collection->isEmpty());
    }

    /** @covers Brickoo\Event\ResponseCollection::count */
    public function testCount() {
        $Collection = new ResponseCollection(array("r1", "r2", "r3", "r4"));
        $this->assertEquals(4, count($Collection));
    }

}