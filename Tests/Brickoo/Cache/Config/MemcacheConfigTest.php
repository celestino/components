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

    use Brickoo\Cache\Config\MemcacheConfig;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * MemcacheConfig
     *
     * Test suite for the MemcacheConfig class.
     * @see Brickoo\Cache\Config\MemcacheConfig
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class MemcacheConfigTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Holds an instance of the MemcacheConfig class-
         * @var MemcacheConfig
         */
        protected $MemcacheConfig;

        /**
         * Sets up the MemcacheConfig instance used for testing.
         * @return vodi
         */
        protected function setUp()
        {
            $this->MemcacheConfig = new MemcacheConfig();
        }

        /**
         * Test if the MemcacheConfig implements the Cache\Interface\MemcacheConfigInterface.
         * @covers Brickoo\Cache\Config\MemcacheConfig::__construct
         */
        public function testConstruct()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Cache\Config\Interfaces\MemcacheConfigInterface',
                $this->MemcacheConfig
            );
        }

        /**
         * Test if a serevr configuration can be added and the MemcacheConfig reference is returned.
         * @covers Brickoo\Cache\Config\MemcacheConfig::addServer
         */
        public function testAddServer()
        {
            $serverConfig = array('host' => 'unix://some/socket', 'port' => 0);

            $this->assertSame($this->MemcacheConfig, $this->MemcacheConfig->addServer($serverConfig['host'], $serverConfig['port']));
            $this->assertAttributeEquals(array($serverConfig), 'servers', $this->MemcacheConfig);

            return $this->MemcacheConfig;
        }

        /**
         * Test if missing some array keys of the configuration throws an exception.
         * @covers Brickoo\Cache\Config\MemcacheConfig::addServer
         * @expectedException InvalidArgumentException
         */
        public function testAddServerValueException()
        {
            $this->MemcacheConfig->addServer(array(), 'wrongType');
        }

        /**
         * Test if the servers added can be retrieved.
         * @covers Brickoo\Cache\Config\MemcacheConfig::getServers
         * @depends testAddServer
         */
        public function testGetServers($MemcacheConfig)
        {
            $serverConfig = array('host' => 'unix://some/socket', 'port' => 0);

            $this->assertEquals(array($serverConfig), $MemcacheConfig->getServers());
        }

        /**
        * Test if the Memcache instacne can be configured.
        * @covers Brickoo\Cache\Config\MemcacheConfig::configure
        * @depends testAddServer
        */
        public function testConfigure($MemcacheConfig)
        {
            $MemcacheStub = $this->getMock('Memcache', array('addServer'));
            $MemcacheStub->expects($this->once())
                         ->method('addServer')
                         ->will($this->returnValue(true));

            $this->assertSame($MemcacheConfig, $MemcacheConfig->configure($MemcacheStub));
        }

    }
