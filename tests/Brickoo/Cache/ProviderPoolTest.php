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

    namespace Tests\Brickoo\Cache;

    use Brickoo\Cache\ProviderPool;

    /**
     * ProviderPoolTest
     *
     * Test suite for the PlroviderPool class.
     * @see Brickoo\Cache\ProviderPool
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ProviderPoolTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Cache\ProviderPool::__construct
         */
        public function testConstructor() {
            $poolEntries = array(
                $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider')
            );
            $ProviderPool = new ProviderPool($poolEntries);
            $this->assertInstanceOf('Brickoo\Cache\Interfaces\ProviderPool', $ProviderPool);
            $this->assertAttributeEquals($poolEntries, "poolEntries", $ProviderPool);
            $this->assertAttributeInternalType("integer", "currentKey", $ProviderPool);
        }

        /**
         * @covers Brickoo\Cache\ProviderPool::__construct
         * @expectedException InvalidArgumentException
         */
        public function testConstructorThrowsArgumentException() {
            $poolEntries = array("wrongType");
            $ProviderPool = new ProviderPool($poolEntries);
        }

        /**
         * @covers Brickoo\Cache\ProviderPool::valid
         * @covers Brickoo\Cache\ProviderPool::next
         * @covers Brickoo\Cache\ProviderPool::key
         * @covers Brickoo\Cache\ProviderPool::current
         * @covers Brickoo\Cache\ProviderPool::rewind
         */
        public function testIteration() {
            $poolEntries = array(
                ($mok_1 = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider')),
                ($mok_2 = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider')),
                ($mok_3 = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider'))
            );
            $ProviderPool = new ProviderPool($poolEntries);
            $this->assertTrue($ProviderPool->valid());
            $this->assertEquals(0, $ProviderPool->key());
            $this->assertSame($mok_1, $ProviderPool->current());

            $ProviderPool->next();
            $this->assertTrue($ProviderPool->valid());
            $this->assertEquals(1, $ProviderPool->key());
            $this->assertSame($mok_2, $ProviderPool->current());

            $ProviderPool->next();
            $this->assertTrue($ProviderPool->valid());
            $this->assertEquals(2, $ProviderPool->key());
            $this->assertSame($mok_3, $ProviderPool->current());

            $ProviderPool->next();
            $this->assertFalse($ProviderPool->valid());

            $ProviderPool->rewind();
            $this->assertEquals(0, $ProviderPool->key());
        }

        /**
         * @covers Brickoo\Cache\ProviderPool::has
         */
        public function testHas() {
            $poolEntries = array(
                0 => $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider')
            );
            $ProviderPool = new ProviderPool($poolEntries);
            $this->assertTrue($ProviderPool->has(0));
            $this->assertFalse($ProviderPool->has("0"));
        }

        /**
         * @covers Brickoo\Cache\ProviderPool::has
         * @expectedException InvalidArgumentException
         */
        public function testHasThrowsInvalidArgumentException() {
            $ProviderPool = new ProviderPool(array());
            $ProviderPool->has(array("wrongType"));
        }

        /**
         * @covers Brickoo\Cache\ProviderPool::select
         */
        public function testSelect() {
            $poolEntries = array(
                0 => $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider'),
                "special" => ($special = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider'))
            );
            $ProviderPool = new ProviderPool($poolEntries);
            $this->assertSame($ProviderPool, $ProviderPool->select("special"));
            $this->assertSame($special, $ProviderPool->current());
            $this->assertEquals("special", $ProviderPool->key());
        }

        /**
         * @covers Brickoo\Cache\ProviderPool::select
         * @expectedException \InvalidArgumentException
         */
        public function testSelectThrowsInvalidArgumentException() {
            $ProviderPool =  new ProviderPool(array());
            $ProviderPool->select(array("wrongType"));
        }

        /**
         * @covers Brickoo\Cache\ProviderPool::select
         * @covers Brickoo\Cache\Exceptions\PoolEntryDoesNotExist
         * @expectedException Brickoo\Cache\Exceptions\PoolEntryDoesNotExist
         */
        public function testSelectThrowsPoolEntryDoesNotExistException() {
            $ProviderPool =  new ProviderPool(array());
            $ProviderPool->select(0);
        }

        /**
         * @covers Brickoo\Cache\ProviderPool::isEmpty
         */
        public function testIsEmpty() {
            $ProviderPool = new ProviderPool(array());
            $this->assertTrue($ProviderPool->isEmpty());

            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $ProviderPool = new ProviderPool(array($Provider));
            $this->assertFalse($ProviderPool->isEmpty());
        }

        /**
         * @covers Brickoo\Cache\ProviderPool::count
         */
        public function testCount() {
            $poolEntries = array(
                $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider'),
                $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider'),
                $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider')
            );
            $ProviderPool = new ProviderPool($poolEntries);
            $this->assertEquals(3, count($ProviderPool));
        }

    }