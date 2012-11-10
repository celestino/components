<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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

    namespace Tests\Brickoo\Memory;

    use Brickoo\Memory\Container;

    /**
     * ContainerTest
     *
     * Test suite for the Container class.
     * @see Brickoo\Memory\Container
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ContainerTest extends \PHPUnit_Framework_TestCase {

        /**
         * Holds an instance of the Container class.
         * @var \Brickoo\Memory\Container
         */
        protected $Container;

        /**
         * Sets up the Container instance used.
         * @return void
         */
        protected function setUp() {
            $this->Container = new Container();
        }

        /**
         * @covers Brickoo\Memory\Container::__construct
         */
        public function testConstructor() {
            $expectedContainerValues = array("name" => "test.case");
            $Container = new Container($expectedContainerValues);
            $this->assertInstanceOf('Brickoo\Memory\Interfaces\Container', $Container);
            $this->assertAttributeEquals($expectedContainerValues, "container", $Container);
        }

        /**
         * @covers Brickoo\Memory\Container::offsetSet
         * @covers Brickoo\Memory\Container::offsetGet
         * @covers Brickoo\Memory\Container::offsetExists
         * @covers Brickoo\Memory\Container::offsetUnset
         */
        public function testArrayAccess() {
            $this->Container['unit'] = 'test';
            $this->assertAttributeEquals(array('unit' => 'test'), 'container', $this->Container);
            $this->assertEquals('test', $this->Container['unit']);
            $this->assertNull($this->Container['undefined']);
            $this->assertTrue(isset($this->Container['unit']));
            unset($this->Container['unit']);
            $this->assertAttributeEquals(array(), 'container', $this->Container);
        }

        /**
         * @covers Brickoo\Memory\Container::valid
         * @covers Brickoo\Memory\Container::key
         * @covers Brickoo\Memory\Container::current
         * @covers Brickoo\Memory\Container::next
         * @covers Brickoo\Memory\Container::rewind
         */
        public function testInterator() {
            $data = array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3');
            $this->Container->merge($data);
            while($this->Container->valid()) {
                $this->assertTrue(array_key_exists($this->Container->key(), $data));
                $this->assertTrue($data[$this->Container->key()] == $this->Container->current());
                $this->Container->next();
            }
            $this->assertEquals('value1', $this->Container->rewind());

        }

        /**
         * @covers Brickoo\Memory\Container::count
         */
        public function testCount() {
            $this->Container->merge(array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'));
            $this->assertEquals(3, count($this->Container));
        }

        /**
         * @covers Brickoo\Memory\Container::get
         */
        public function testGet() {
            $this->Container['unit'] = 'test';
            $this->assertEquals('test', $this->Container->get('unit'));
            $this->assertEquals('DEFAULT', $this->Container->get('undefined', 'DEFAULT'));
        }

        /**
         * @covers Brickoo\Memory\Container::set
         */
        public function testSet() {
            $this->assertSame($this->Container, $this->Container->set('unit', 'test'));
            $this->assertAttributeEquals(array('unit' => 'test'), 'container', $this->Container);
        }

        /**
         * @covers Brickoo\Memory\Container::has
         */
        public function testHas() {
            $this->Container['unit'] = 'test';
            $this->assertTrue($this->Container->has('unit'));
            $this->assertFalse($this->Container->has('undefinied'));
        }

        /**
         * @covers Brickoo\Memory\Container::delete
         */
        public function testDelete() {
            $this->Container['unit'] = 'test';
            $this->assertSame($this->Container, $this->Container->delete('unit'));
            $this->assertAttributeEquals(array(), 'container', $this->Container);
        }

        /**
         * @covers Brickoo\Memory\Container::merge
         */
        public function testMerge() {
            $initData        = array('key1' => 'value1');
            $mergeData       = array('key2' => 'value2');
            $expectedData    = array_merge($initData, $mergeData);

            $this->Container['key1'] = 'value1';
            $this->assertAttributeEquals($initData, 'container', $this->Container);
            $this->assertSame($this->Container, $this->Container->merge($mergeData));
            $this->assertAttributeEquals($expectedData, 'container', $this->Container);
        }

        /**
         * @covers Brickoo\Memory\Container::fromArray
         */
        public function testFromArray() {
            $expected  = array('test', 'import');
            $this->assertSame($this->Container, $this->Container->fromArray($expected));
            $this->assertAttributeEquals($expected, 'container', $this->Container);
        }

        /**
         * @covers Brickoo\Memory\Container::toArray
         */
        public function testToArray() {
            $expected = array('test');
            $this->assertSame($this->Container, $this->Container->fromArray($expected));
            $this->assertEquals($expected, $this->Container->toArray());
        }

        /**
         * @covers Brickoo\Memory\Container::isEmpty
         */
        public function testIsEmpty() {
            $this->assertTrue($this->Container->isEmpty());
            $this->Container['unit'] = 'test';
            $this->assertFalse($this->Container->isEmpty());
        }

        /**
         * @covers Brickoo\Memory\Container::flush
         */
        public function testFlush() {
            $this->Container['unit'] = 'test';
            $this->Container->flush();
            $this->assertAttributeEquals(array(), 'container', $this->Container);
        }

        /**
         * @covers Brickoo\Memory\Container::__get
         */
        public function test__get() {
            $this->Container['unit'] = 'test';
            $this->assertEquals('test', $this->Container->unit);
        }

        /**
         * @covers Brickoo\Memory\Container::__set
         */
        public function test__set() {
            $this->assertAttributeEquals(array(), 'container', $this->Container);
            $this->Container->unit = 'test';
            $this->assertAttributeEquals(array('unit' => 'test'), 'container', $this->Container);
        }

    }