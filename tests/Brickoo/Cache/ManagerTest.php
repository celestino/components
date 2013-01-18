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

    use Brickoo\Cache\Manager;

    /**
     * Manager
     *
     * Test suite for the Manager class.
     * @see Brickoo\Cache\Manager
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ManagerTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Cache\Manager::__construct
         */
        public function testConstructorAssignsTheProperties() {
            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $CacheManager = new Manager($Provider);
            $this->assertAttributeSame($Provider, 'CacheProvider', $CacheManager);
        }

        /**
         * @covers Brickoo\Cache\Manager::get
         */
        public function testGetCachedContent() {
            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $Provider->expects($this->any())
                     ->method('get')
                     ->with('some_identifier')
                     ->will($this->returnValue('provider cache content'));

            $CacheManager = new Manager($Provider);
            $this->assertEquals('provider cache content', $CacheManager->get('some_identifier'));
        }

        /**
         * @covers Brickoo\Cache\Manager::get
         * @expectedException InvalidArgumentException
         */
        public function testGetIdentifierThrowsArgumentException() {
            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $CacheManager = new Manager($Provider);
            $CacheManager->get(array('wrongType'));
        }

        /**
         * @covers Brickoo\Cache\Manager::set
         */
        public function testStoringContentToCache() {
            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $Provider->expects($this->once())
                     ->method('set')
                     ->with('some_identifier', array('content'), 60);

            $CacheManager = new Manager($Provider);
            $this->assertSame($CacheManager, $CacheManager->set('some_identifier', array('content'), 60));
        }

        /**
         * @covers Brickoo\Cache\Manager::set
         * @expectedException InvalidArgumentException
         */
        public function testSetIdentifierThrowsArgumentException() {
            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $CacheManager = new Manager($Provider);
            $CacheManager->set(array('wrongType'), '', 60);
        }

        /**
         * @covers Brickoo\Cache\Manager::set
         * @expectedException InvalidArgumentException
         */
        public function testSetLifetimeThrowsArgumentException() {
            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $CacheManager = new Manager($Provider);
            $CacheManager->set('some_identifier', '', 'wrongType');
        }

        /**
         * @covers Brickoo\Cache\Manager::delete
         */
        public function testDeleteCachedContentFromLocalAndProvider() {
            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $Provider->expects($this->once())
                     ->method('delete')
                     ->with('some_identifier');

            $CacheManager = new Manager($Provider);
            $this->assertSame($CacheManager, $CacheManager->delete('some_identifier'));
        }

        /**
         * @covers Brickoo\Cache\Manager::delete
         * @expectedException InvalidArgumentException
         */
        public function testDeleteIdentifierThrowsArgumentException() {
            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $CacheManager = new Manager($Provider);
            $CacheManager->delete(array('wrongType'));
        }

        /**
         * @covers Brickoo\Cache\Manager::flush
         */
        public function testFlushCachedContent() {
            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $Provider->expects($this->once())
                     ->method('flush');

            $CacheManager = new Manager($Provider);
            $this->assertSame($CacheManager, $CacheManager->flush());
        }

        /**
         * @covers Brickoo\Cache\Manager::getByCallback
         */
        public function testGetByCallbackFromLocalStorage() {
            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $CacheManager = new Manager($Provider);
            $this->assertEquals(
                'callback content',
                $CacheManager->getByCallback('unique_identifier', array($this, 'callbackGetCachedContent'), array())
            );
        }

        /**
         * @covers Brickoo\Cache\Manager::getByCallback
         */
        public function testGetByCallbackFallbackFromCacheProvider() {
            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $Provider->expects($this->once())
                     ->method('get')
                     ->will($this->returnValue('fallback content'));

            $CacheManager = new Manager($Provider);
            $this->assertEquals(
                'fallback content',
                $CacheManager->getByCallback('unique_identifier', array($this, 'callbackNotFound'), array())
            );
        }

        /**
         * @covers Brickoo\Cache\Manager::getByCallback
         * @expectedException InvalidArgumentException
         */
        public function testGetByCallbackIdentifierArgumentException() {
            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $CacheManager = new Manager($Provider);
            $CacheManager->getByCallback(array('wrongType'), 'someFunction', array());
        }

        /**
         * @covers Brickoo\Cache\Manager::getByCallback
         * @expectedException InvalidArgumentException
         */
        public function testGetByCallbackCallableArgumentException() {
            $Provider = $this->getMock('Brickoo\Cache\Provider\Interfaces\Provider');
            $CacheManager = new Manager($Provider);
            $CacheManager->getByCallback('some_identifier', 'this.is.not.callable', array());
        }

        /**
         * Helper method for the testGetCacheCallback.
         * @return string the callback response
         */
        public function callbackGetCachedContent() {
            return 'callback content';
        }

        /**
         * Helper method for the testGetFallbackForTheCallback.
         * @return null
         */
        public function callbackNotFound() {
            return;
        }

    }