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

    namespace Tests\Brickoo\Cache\Provider;

    use Brickoo\Cache\Provider\Memcache;

    /**
     * MemcacheTest
     *
     * Test suite for the Memcache class.
     * @see Brickoo\Cache\Provider\Memcache
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class MemcacheProviderTest extends \PHPUnit_Framework_TestCase {

        public function setUp() {
            if (! extension_loaded('memcache')) {
                $this->markTestSkipped('The memcache extension is not available.');
            }
        }

        /**
         * @covers Brickoo\Cache\Provider\Memcache::__construct
         */
        public function testConstructor() {
            $Memcache= $this->getMock('Memcache');
            $MemcacheProvider = new Memcache($Memcache);
            $this->assertAttributeSame($Memcache, 'Memcache', $MemcacheProvider);
        }

        /**
         * @covers Brickoo\Cache\Provider\Memcache::set
         */
        public function testSetCacheContent() {
            $cacheIdentifier = 'identifier';
            $cacheContent = 'some content to cache';
            $cacheCompression = MEMCACHE_COMPRESSED;
            $cacheLifetime = 60;

            $Memcache = $this->getMock('Memcache');
            $Memcache->expects($this->once())
                     ->method('set')
                     ->with($cacheIdentifier,  $cacheContent, $cacheCompression, $cacheLifetime)
                     ->will($this->returnSelf());

            $MemcacheProvider = new Memcache($Memcache, $cacheCompression);
            $this->assertSame($MemcacheProvider, $MemcacheProvider->set($cacheIdentifier, $cacheContent, $cacheLifetime));
        }

        /**
         * @covers Brickoo\Cache\Provider\Memcache::set
         * @expectedException InvalidArgumentException
         */
        public function testSetThrowsAnArgumentException() {
            $MemcacheProvider = new Memcache($this->getMock('Memcache'));
            $MemcacheProvider->set(array('wrongType'), 'whatever', array('wrongType'));
        }

        /**
         * @covers Brickoo\Cache\Provider\Memcache::get
         */
        public function testGetCacheContent() {
            $cacheIdentifier = 'someIdentifier';
            $cachedContent = 'some cached content';

            $Memcache = $this->getMock('Memcache');
            $Memcache->expects($this->once())
                     ->method('get')
                     ->with($cacheIdentifier)
                     ->will($this->returnValue($cachedContent));

            $MemcacheProvider = new Memcache($Memcache);
            $this->assertEquals($cachedContent, $MemcacheProvider->get($cacheIdentifier));
        }

        /**
         * @covers Brickoo\Cache\Provider\Memcache::get
         * @expectedException InvalidArgumentException
         */
        public function testGetThrowsAnArgumentException() {
            $MemcacheProvider = new Memcache($this->getMock('Memcache'));
            $MemcacheProvider->get(array('wrongType'));
        }


        /**
         * @covers Brickoo\Cache\Provider\Memcache::delete
         */
        public function testDeleteCacheContent() {
            $cacheIdentifier = 'someIdentifier';

            $Memcache = $this->getMock('Memcache');
            $Memcache->expects($this->once())
                     ->method('delete')
                     ->with($cacheIdentifier)
                     ->will($this->returnSelf());

            $MemcacheProvider = new Memcache($Memcache);
            $this->assertSame($MemcacheProvider, $MemcacheProvider->delete($cacheIdentifier));
        }

        /**
         * @covers Brickoo\Cache\Provider\Memcache::delete
         * @expectedException InvalidArgumentException
         */
        public function testDeleteThrowsAnArgumentException() {
            $MemcacheProvider = new Memcache($this->getMock('Memcache'));
            $MemcacheProvider->delete(array('wrongType'));
        }

        /**
         * @covers Brickoo\Cache\Provider\Memcache::flush
         */
        public function testFlushCachedContent() {
            $Memcache = $this->getMock('Memcache');
            $Memcache->expects($this->once())
                     ->method('flush')
                     ->will($this->returnSelf());

            $MemcacheProvider = new Memcache($Memcache);
            $this->assertSame($MemcacheProvider, $MemcacheProvider->flush());
        }

        /**
         * @covers Brickoo\Cache\Provider\Memcache::isReady
         */
        public function testIsReady() {
            $Memcache = new Memcache($this->getMock('Memcache'));
            $this->assertTrue($Memcache->isReady());
        }

        /**
         * @covers Brickoo\Cache\Provider\Memcache::__call
         */
        public function testMagicCallToMemcacheMethod() {
            $cacheIdentifier = 'someIdentifier';
            $cacheContent = 'some content to cache';

            $Memcache= $this->getMock('Memcache');
            $Memcache->expects($this->once())
                     ->method('add')
                     ->with($cacheIdentifier, $cacheContent)
                     ->will($this->returnValue(true));

            $MemcacheProvider = new Memcache($Memcache);
            $this->assertTrue($MemcacheProvider->add($cacheIdentifier, $cacheContent));
        }

        /**
         * @covers Brickoo\Cache\Provider\Memcache::__call
         * @expectedException BadMethodCallException
         */
        public function testMagicCallThrowsABadMethodCallException() {
            $MemcacheProvider = new Memcache($this->getMock('Memcache'));
            $MemcacheProvider->whatever();
        }

    }