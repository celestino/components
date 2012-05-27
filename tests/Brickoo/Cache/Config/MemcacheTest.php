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

    use Brickoo\Cache\Config\Memcache;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Memcache
     *
     * Test suite for the Memcache class.
     * @see Brickoo\Cache\Config\Memcache
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class MemcacheTest extends \PHPUnit_Framework_TestCase {
        /**
         * Holds an instance of the Memcache class-
         * @var Memcache
         */
        protected $Memcache;

        /**
         * Sets up the Memcache instance used for testing.
         * @return vodi
         */
        protected function setUp() {
            $this->Memcache = new Memcache();
        }

        /**
         * Test if the Memcache implements the Cache\Interface\Memcache.
         * @covers Brickoo\Cache\Config\Memcache::__construct
         */
        public function testConstruct() {
            $this->assertInstanceOf
            (
                'Brickoo\Cache\Config\Interfaces\Memcache',
                $this->Memcache
            );
        }

        /**
         * Test if a serevr configuration can be added and the Memcache reference is returned.
         * @covers Brickoo\Cache\Config\Memcache::addServer
         */
        public function testAddServer() {
            $serverConfig = array('host' => 'unix://some/socket', 'port' => 0);

            $this->assertSame($this->Memcache, $this->Memcache->addServer($serverConfig['host'], $serverConfig['port']));
            $this->assertAttributeEquals(array($serverConfig), 'servers', $this->Memcache);

            return $this->Memcache;
        }

        /**
         * Test if missing some array keys of the configuration throws an exception.
         * @covers Brickoo\Cache\Config\Memcache::addServer
         * @expectedException InvalidArgumentException
         */
        public function testAddServerValueException() {
            $this->Memcache->addServer(array(), 'wrongType');
        }

        /**
         * Test if the servers added can be retrieved.
         * @covers Brickoo\Cache\Config\Memcache::getServers
         * @depends testAddServer
         */
        public function testGetServers($Memcache) {
            $serverConfig = array('host' => 'unix://some/socket', 'port' => 0);

            $this->assertEquals(array($serverConfig), $Memcache->getServers());
        }

        /**
         * Test if a collection of servers can be set.
         * @covers Brickoo\Cache\Config\Memcache::setServers
         */
        public function testSetServer() {
            $servers = array(
                array('host' => 'localhost', 'port' => 112211),
                array('host' => '127.0.0.1', 'port' => 112211),
            );

            $this->assertSame($this->Memcache, $this->Memcache->setServers($servers));
            $this->assertAttributeEquals($servers, 'servers', $this->Memcache);
        }

        /**
        * Test if the Memcache instacne can be configured.
        * @covers Brickoo\Cache\Config\Memcache::configure
        * @depends testAddServer
        */
        public function testConfigure($Memcache) {
            $MemcacheStub = $this->getMock('Memcache', array('addServer'));
            $MemcacheStub->expects($this->once())
                         ->method('addServer')
                         ->will($this->returnValue(true));

            $this->assertSame($MemcacheStub, $Memcache->configure($MemcacheStub));
        }

    }
