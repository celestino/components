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

    use Brickoo\Cache\Provider\Apc;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * ApcTest
     *
     * Test suite for the Apc class.
     * @see Brickoo\Cache\Provider\Apc
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ApcTest extends \PHPUnit_Framework_TestCase {

        /**
         * Holds an instance of the Apc class.
         * @var Apc
         */
        protected $Apc;

        /**
         * Set up the Apc object used.
         * @return void
         */
        protected function setUp() {
            if
            (
                (! extension_loaded('apc')) ||
                (! ini_get('apc.enable_cli'))
            ) {
                $this->markTestSkipped('The apc extension is not available.');
            }

            $this->Apc = new Apc();
        }

        /**
         * Test if a content can be set to the Apc and the result is returned.
         * @covers Brickoo\Cache\Provider\Apc::set
         */
        public function testSet() {
            $this->assertTrue($this->Apc->set('some_identifier', 'some cached content', 3600));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception
         * @covers Brickoo\Cache\Provider\Apc::set
         * @expectedException InvalidArgumentException
         */
        public function testSetArgumentException() {
            $this->Apc->set(array('wrongType'), 'whatever', array('wrongType'));
        }

        /**
         * Test if a content can be retrieved from the APC.
         * @covers Brickoo\Cache\Provider\Apc::get
         * @depends testSet
         */
        public function testGet() {
            $this->assertEquals('some cached content', $this->Apc->get('some_identifier'));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception
         * @covers Brickoo\Cache\Provider\Apc::get
         * @expectedException InvalidArgumentException
         */
        public function testGetArgumentException() {
            $this->Apc->get(array('wrongType'));
        }

        /**
         * Test if a cached content can be delete by its identifier and the result is returned.
         * @covers Brickoo\Cache\Provider\Apc::delete
         * @depends testSet
         */
        public function testDelete() {
            $this->assertTrue($this->Apc->delete('some_identifier'));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception
         * @covers Brickoo\Cache\Provider\Apc::delete
         * @expectedException InvalidArgumentException
         */
        public function testDeleteArgumentException() {
            $this->Apc->delete(array('wrongType'));
        }

        /**
         * Test if a cached content can be flushed and the result is returned.
         * @covers Brickoo\Cache\Provider\Apc::flush
         */
        public function testFlush() {
            $this->assertTrue($this->Apc->flush());
        }

        /**
         * Test if a APC method not implemented can be called and the result is returned.
         * @covers Brickoo\Cache\Provider\Apc::__call
         */
        public function test__call() {
            $this->assertTrue($this->Apc->apc_add('some_identifier', 'some other content', 3600));
            $this->assertEquals('some other content', $this->Apc->get('some_identifier'));
        }

        /**
         * Test if trying to call a not available APC function throws an exception
         * @covers Brickoo\Cache\Provider\Apc::__call
         * @expectedException BadMethodCallException
         */
        public function test__callBadMethodCallException() {
            $this->Apc->whatever();
        }

    }
