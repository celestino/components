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

    use Brickoo\Cache\Provider\Memcache;

    require_once ('PHPUnit/Autoload.php');

    /**
     * MemcacheTest
     *
     * Test suite for the Memcache class.
     * @see Brickoo\Cache\Provider\Memcache
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class MemcacheProviderTest extends \PHPUnit_Framework_TestCase {

        /**
         * Creates and returns a Memcache stub.
         * @return object memcache stub
         */
        public function getMemcacheStub() {
            return $this->getMock('Memcache', array('get', 'set', 'delete', 'flush', 'add'));
        }

        /**
         * Holds an instance of the Memcache class.
         * @var Memcache
         */
        protected $Memcache;

        /**
         * Set up the Memcache object used.
         * @return void
         */
        protected function setUp() {
            if (! defined('MEMCACHE_COMPRESSED')) {
                define('MEMCACHE_COMPRESSED', 2);
            }

            $this->Memcache = new Memcache($this->getMemcacheStub());
        }

        /**
         * Test if the Memcache dependency has been be injected.
         * @covers Brickoo\Cache\Provider\Memcache::__construct
         */
        public function testConstruct() {
            $MemcacheStub = $this->getMemcacheStub();
            $Memcache = new Memcache($MemcacheStub);
            $this->assertAttributeSame($MemcacheStub, '_Memcache', $Memcache);
        }

        /**
         * Test if the Memcache dependency can be retrieved.
         * @covers Brickoo\Cache\Provider\Memcache::Memcache
         */
        public function testGetMemcache() {
            $this->assertInstanceOf('Memcache', $this->Memcache->Memcache());
        }

        /**
         * Test if the compression can be enabled and the Memcache reference is returned.
         * @covers Brickoo\Cache\Provider\Memcache::enableCompression
         */
        public function testEnableCompression() {
            $this->assertAttributeEquals(false, 'compression', $this->Memcache);
            $this->assertSame($this->Memcache, $this->Memcache->enableCompression());
            $this->assertAttributeEquals(MEMCACHE_COMPRESSED, 'compression', $this->Memcache);

            return $this->Memcache;
        }

        /**
         * Test if the compression can be disabled and the Memcache reference is returned.
         * @covers Brickoo\Cache\Provider\Memcache::disableCompression
         * @depends testEnableCompression
         */
        public function testDisableCompression($Memcache) {
            $this->assertAttributeEquals(MEMCACHE_COMPRESSED, 'compression', $Memcache);
            $this->assertSame($Memcache, $Memcache->disableCompression());
            $this->assertAttributeEquals(false, 'compression', $this->Memcache);
        }

        /**
         * Test if a content can be retrieved from the Memcache.
         * @covers Brickoo\Cache\Provider\Memcache::get
         */
        public function testGet() {
            $MemcacheStub = $this->Memcache->Memcache();
            $MemcacheStub->expects($this->once())
                         ->method('get')
                         ->will($this->returnValue('some cached content'));

            $this->assertEquals('some cached content', $this->Memcache->get('some_identifier'));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception
         * @covers Brickoo\Cache\Provider\Memcache::get
         * @expectedException InvalidArgumentException
         */
        public function testGetArgumentException() {
            $this->Memcache->get(array('wrongType'));
        }

        /**
         * Test if a content can be set to the Memcache and the result is returned.
         * @covers Brickoo\Cache\Provider\Memcache::set
         */
        public function testSet() {
            $MemcacheStub = $this->Memcache->Memcache();
            $MemcacheStub->expects($this->once())
                         ->method('set')
                         ->will($this->returnValue(true));

            $this->assertTrue($this->Memcache->set('some_identifier', 'content'));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception
         * @covers Brickoo\Cache\Provider\Memcache::set
         * @expectedException InvalidArgumentException
         */
        public function testSetArgumentException() {
            $this->Memcache->set(array('wrongType'), 'whatever', array('wrongType'));
        }

        /**
         * Test if a cached content can be delete by its identifier and the result is returned.
         * @covers Brickoo\Cache\Provider\Memcache::delete
         */
        public function testDelete() {
            $MemcacheStub= $this->Memcache->Memcache();
            $MemcacheStub->expects($this->once())
                         ->method('delete')
                         ->will($this->returnValue(true));

            $this->assertTrue($this->Memcache->delete('some_identifier'));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception
         * @covers Brickoo\Cache\Provider\Memcache::delete
         * @expectedException InvalidArgumentException
         */
        public function testDeleteArgumentException() {
            $this->Memcache->delete(array('wrongType'));
        }

        /**
         * Test if a cached content can be flushed and the result is returned.
         * @covers Brickoo\Cache\Provider\Memcache::flush
         */
        public function testFlush() {
            $MemcacheStub= $this->Memcache->Memcache();
            $MemcacheStub->expects($this->once())
                         ->method('flush')
                         ->will($this->returnValue(true));

            $this->assertTrue($this->Memcache->flush());
        }

        /**
         * Test if a Memcache method not implemented can be called and the result is returned.
         * @covers Brickoo\Cache\Provider\Memcache::__call
         */
        public function test__call() {
            $MemcacheStub= $this->Memcache->Memcache();
            $MemcacheStub->expects($this->once())
                         ->method('add')
                         ->will($this->returnValue(true));

            $this->assertTrue($this->Memcache->add('some_identifier', 'some_content'));
        }

        /**
         * Test if trying to call a not available method on the Memcache object throws an exception
         * @covers Brickoo\Cache\Provider\Memcache::__call
         * @expectedException BadMethodCallException
         */
        public function test__callBadMethodCallException() {
            $this->Memcache->whatever();
        }

    }