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

    namespace Tests\Brickoo\Cache\Provider;

    use Brickoo\Cache\Provider\Apc;

    /**
     * ApcTest
     *
     * Test suite for the Apc class.
     * @see Brickoo\Cache\Provider\Apc
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ApcTest extends \PHPUnit_Framework_TestCase {

        public function setUp() {
            if ((! extension_loaded('apc')) || (! ini_get('apc.enable_cli'))) {
                $this->markTestSkipped('The apc extension is not available.');
            }
        }

        /**
         * @covers Brickoo\Cache\Provider\Apc::set
         */
        public function testSet() {
            $Provider = new Apc();
            $this->assertSame($Provider, $Provider->set('some_identifier', 'some cached content', 3600));
        }

        /**
         * @covers Brickoo\Cache\Provider\Apc::set
         * @expectedException InvalidArgumentException
         */
        public function testSetArgumentException() {
            $Provider = new Apc();
            $Provider->set(array('wrongType'), 'whatever', array('wrongType'));
        }

        /**
         * @covers Brickoo\Cache\Provider\Apc::get
         * @depends testSet
         */
        public function testGet() {
            $Provider = new Apc();
            $this->assertEquals('some cached content', $Provider->get('some_identifier'));
        }

        /**
         * @covers Brickoo\Cache\Provider\Apc::get
         * @expectedException InvalidArgumentException
         */
        public function testGetArgumentException() {
            $Provider = new Apc();
            $Provider->get(array('wrongType'));
        }

        /**
         * @covers Brickoo\Cache\Provider\Apc::delete
         * @depends testSet
         */
        public function testDelete() {
            $Provider = new Apc();
            $this->assertSame($Provider, $Provider->delete('some_identifier'));
        }

        /**
         * @covers Brickoo\Cache\Provider\Apc::delete
         * @expectedException InvalidArgumentException
         */
        public function testDeleteArgumentException() {
            $Provider = new Apc();
            $Provider->delete(array('wrongType'));
        }

        /**
         * @covers Brickoo\Cache\Provider\Apc::flush
         */
        public function testFlush() {
            $Provider = new Apc();
            $this->assertSame($Provider, $Provider->flush());
        }

        /**
         * @covers Brickoo\Cache\Provider\Apc::__call
         */
        public function test__call() {
            $Provider = new Apc();
            $this->assertTrue($Provider->apc_add('some_identifier', 'some other content', 3600));
            $this->assertEquals('some other content', $Provider->get('some_identifier'));
        }

        /**
         * @covers Brickoo\Cache\Provider\Apc::__call
         * @expectedException BadMethodCallException
         */
        public function test__callBadMethodCallException() {
            $Provider = new Apc();
            $Provider->whatever();
        }

    }
