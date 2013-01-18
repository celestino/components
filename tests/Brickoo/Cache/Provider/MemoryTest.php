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

    namespace Tests\Brickoo\Cache;

    use Brickoo\Cache\Provider\Memory;

    /**
     * MemoryTest
     *
     * Test suite for the Memory class.
     * @see Brickoo\Cache\Provider\Memory
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class MemoryTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Cache\Provider\Memory::__construct
         */
        public function testConstructor() {
            $Memory = new Memory();
            $this->assertInstanceOf('Brickoo\Cache\Provider\Interfaces\Provider', $Memory);
            $this->assertAttributeEquals(array(), 'cacheValues', $Memory);
        }

        /**
         * @covers Brickoo\Cache\Provider\Memory::set
         */
        public function testSetCacheContent() {
            $Memory = new Memory();
            $this->assertSame($Memory, $Memory->set('unique_identifier', 'some content'));
            $this->assertAttributeEquals(
                array('unique_identifier' => 'some content'),
                'cacheValues',
                $Memory
            );
        }

        /**
         * @covers Brickoo\Cache\Provider\Memory::set
         * @expectedException InvalidArgumentException
         */
        public function testSetIdentifierThrowsArgumentException() {
            $Memory = new Memory();
            $Memory->set(array('wrongTpe'), 'some content');
        }

        /**
         * @covers Brickoo\Cache\Provider\Memory::get
         */
        public function testGetCacheContent() {
            $Memory = new Memory();
            $Memory->set('unique_identifier', 'some content');
            $this->assertEquals('some content', $Memory->get('unique_identifier'));
        }

        /**
         * @covers Brickoo\Cache\Provider\Memory::get
         * @expectedException InvalidArgumentException
         */
        public function testGetIdentifierThrowsArgumentException() {
            $Memory = new Memory();
            $Memory->get(array('wrongTpe'));
        }

        /**
         * @covers Brickoo\Cache\Provider\Memory::get
         * @covers Brickoo\Cache\Provider\Exceptions\IdentifierNotAvailable
         * @expectedException Brickoo\Cache\Provider\Exceptions\IdentifierNotAvailable
         */
        public function testGetIdentifierThrowsIdentifierNotAvailableException() {
            $Memory = new Memory();
            $Memory->get('not_available');
        }

        /**
         * @covers Brickoo\Cache\Provider\Memory::delete
         * @depends testSetCacheContent
         */
        public function testDeleteCachedContent() {
            $Memory = new Memory();
            $Memory->set('unique_identifier', '');
            $this->assertSame($Memory, $Memory->delete('unique_identifier'));
            $this->assertAttributeEquals(array(), 'cacheValues', $Memory);
        }

        /**
         * @covers Brickoo\Cache\Provider\Memory::delete
         * @expectedException InvalidArgumentException
         */
        public function testDeleteIdentifierThrowsArgumentException() {
            $Memory = new Memory();
            $Memory->delete(array('wrongTpe'));
        }

        /**
         * @covers Brickoo\Cache\Provider\Memory::delete
         * @covers Brickoo\Cache\Provider\Exceptions\IdentifierNotAvailable
         * @expectedException Brickoo\Cache\Provider\Exceptions\IdentifierNotAvailable
         */
        public function testDeleteIdentifierThrowsIdentifierNotAvailableException() {
            $Memory = new Memory();
            $Memory->delete('not_available');
        }

        /**
         * @covers Brickoo\Cache\Provider\Memory::has
         * @depends testSetCacheContent
         */
        public function testHasAnIdentifier() {
            $Memory = new Memory();
            $this->assertFalse($Memory->has('unique_identifier'));

            $Memory->set('unique_identifier', 'some content');
            $this->assertTrue($Memory->has('unique_identifier'));
        }

        /**
         * @covers Brickoo\Cache\Provider\Memory::flush
         * @depends testSetCacheContent
         */
        public function testFlushCachedContent() {
            $Memory = new Memory();
            $Memory->set('unique_identifier', 'some content');
            $this->assertTrue($Memory->has('unique_identifier'));

            $this->assertSame($Memory, $Memory->flush());
            $this->assertAttributeEquals(array(), 'cacheValues', $Memory);
        }

    }