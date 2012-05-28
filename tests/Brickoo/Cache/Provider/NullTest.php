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

    use Brickoo\Cache\Provider\Null;

    require_once ('PHPUnit/Autoload.php');

    /**
     * NullTest
     *
     * Test suite for the Null class.
     * Some of the test cases are using the PHP temporary directory for the cache files.
     * @see Brickoo\Cache\Provider\File
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class NullProviderTest extends \PHPUnit_Framework_TestCase {

        /**
         * Holds an instance of the Null class.
         * @var Null
         */
        protected $Null;

        /**
         * Set up the Null object used.
         * @return void
         */
        protected function setUp() {
            $this->Null = new Null();
        }

        /**
         * Test if the Null returns always false.
         * @covers Brickoo\Cache\Provider\Null::get
         */
        public function testGet() {
            $this->assertFalse($this->Null->get('whatever'));
        }

        /**
         * Test if the Null returns always true.
         * @covers Brickoo\Cache\Provider\Null::set
         */
        public function testSet() {
            $this->assertTrue($this->Null->set('whatever', 'non cached content', 60));
        }

        /**
         * Test if the Null returns always true.
         * @covers Brickoo\Cache\Provider\Null::delete
         */
        public function testDelete() {
            $this->assertTrue($this->Null->delete('whatever'));
        }

        /**
         * Test if the Null returns always true.
         * @covers Brickoo\Cache\Provider\Null::flush
         */
        public function testFlush() {
            $this->assertTrue($this->Null->flush());
        }

    }
