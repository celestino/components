<?php

    /*
     * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    namespace Tests\Brickoo\Event;

    use Brickoo\Event\Response\Collection;

    /**
     * CollectionTest
     *
     * Test suite for the Collection class.
     * @see Brickoo\Event\Response\Collection
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class CollectionTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Event\Response\Collection::__construct
         */
        public function testConstructor() {
            $responsesContainer = array("response1", "response2");
            $Collection =  new Collection($responsesContainer);
            $this->assertInstanceOf('Brickoo\Event\Response\Interfaces\Collection', $Collection);
            $this->assertAttributeEquals($responsesContainer, 'responsesContainer', $Collection);
        }

        /**
         * @covers Brickoo\Event\Response\Collection::shift
         */
        public function testShift() {
            $responsesContainer = array("response1", "response2");
            $Collection =  new Collection($responsesContainer);
            $this->assertEquals("response1", $Collection->shift());
            $this->assertAttributeEquals(array("response2"), 'responsesContainer', $Collection);
        }

        /**
         * @covers Brickoo\Event\Response\Collection::shift
         * @covers Brickoo\Event\Response\Exceptions\ResponseNotAvailable
         * @expectedException Brickoo\Event\Response\Exceptions\ResponseNotAvailable
         */
        public function testShiftEmptyListThrowsResponseNotAvailableException() {
            $Collection = new Collection(array());
            $Collection->shift();
        }

        /**
         * @covers Brickoo\Event\Response\Collection::pop
         */
        public function testPop() {
            $responsesContainer = array("response1", "response2");
            $Collection =  new Collection($responsesContainer);
            $this->assertEquals("response2", $Collection->pop());
            $this->assertAttributeEquals(array("response1"), 'responsesContainer', $Collection);
        }

        /**
         * @covers Brickoo\Event\Response\Collection::pop
         * @covers Brickoo\Event\Response\Exceptions\ResponseNotAvailable
         * @expectedException Brickoo\Event\Response\Exceptions\ResponseNotAvailable
         */
        public function testPopEmptyListThrowsResponseNotAvailableException() {
            $Collection = new Collection(array());
            $Collection->pop();
        }

        /**
         * @covers Brickoo\Event\Response\Collection::getAll
         */
        public function testGetAll() {
            $responsesContainer = array("response1", "response2");
            $Collection =  new Collection($responsesContainer);
            $this->assertEquals($responsesContainer, $Collection->getAll());
        }

        /**
         * @covers Brickoo\Event\Response\Collection::getAll
         * @covers Brickoo\Event\Response\Exceptions\ResponseNotAvailable
         * @expectedException Brickoo\Event\Response\Exceptions\ResponseNotAvailable
         */
        public function testGetEmptyListThrowsResponseNotAvailableException() {
            $Collection = new Collection(array());
            $Collection->getAll();
        }

        /**
         * @covers Brickoo\Event\Response\Collection::isEmpty
         */
        public function testIsEmpty() {
            $Collection = new Collection(array());
            $this->assertTrue($Collection->isEmpty());

            $Collection = new Collection(array("response"));
            $this->assertFalse($Collection->isEmpty());
        }

        /**
         * @covers Brickoo\Event\Response\Collection::count
         */
        public function testCount() {
            $Collection = new Collection(array("r1", "r2", "r3", "r4"));
            $this->assertEquals(4, count($Collection));
        }

    }