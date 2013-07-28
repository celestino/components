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

    use Brickoo\Cache\Provider\DoNothing;

    /**
     * DoNothingTest
     *
     * Test suite for the DoNothing class.
     * @see Brickoo\Cache\Provider\DoNothing
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class DoNothingTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Cache\Provider\DoNothing::get
         */
        public function testGetReturnsNull() {
            $Provider = new DoNothing();
            $this->assertNull($Provider->get('whatever'));
        }

        /**
         * @covers Brickoo\Cache\Provider\DoNothing::set
         */
        public function testSetCacheContent() {
            $Provider = new DoNothing();
            $this->assertSame($Provider, $Provider->set('whatever', 'non cached content', 60));
        }

        /**
         * @covers Brickoo\Cache\Provider\DoNothing::delete
         */
        public function testDeleteDoesNothing() {
            $Provider = new DoNothing();
            $this->assertSame($Provider, $Provider->delete('whatever'));
        }

        /**
         * @covers Brickoo\Cache\Provider\DoNothing::flush
         */
        public function testFlushDoesNothing() {
            $Provider = new DoNothing();
            $this->assertSame($Provider, $Provider->flush());
        }

        /**
         * @covers Brickoo\Cache\Provider\DoNothing::isReady
         */
        public function testIsReadyReturnsAlwaysTrue() {
            $Provider = new DoNothing();
            $this->assertTrue($Provider->isReady());
        }

    }
