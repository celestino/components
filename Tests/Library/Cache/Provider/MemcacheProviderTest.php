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

    use Brickoo\Library\Cache\Provider\MemcacheProvider;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * MemcacheProviderTest
     *
     * Test suite for the MemcacheProvider class.
     * @see Brickoo\Library\Cache\Provider\MemcacheProvider
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class MemcacheProviderTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Creates and returns a Memcache stub.
         * @return object memcache stub
         */
        public function getMemcacheStub()
        {
            return $this->getMock('Memcache', array('get', 'set', 'delete', 'flush', 'addServer', 'add'));
        }

        /**
         * Creates an returns a configured MemcacheConfig stub.
         * @return object MemcacheConfig stub
         */
        public function getMemcacheConfigStub()
        {
            $memcacheServer = array('host' => 'unix:://some/socket', 'port' => 0);

            $MemcacheConfigStub = $this->getMock
            (
                'Brickoo\Library\Cache\Interfaces\MemcacheConfigInterface',
                array('getServers', 'addServer', 'reset')
            );

            $MemcacheConfigStub->expects($this->once())
                               ->method('getServers')
                               ->will($this->returnValue(array($memcacheServer)));

            return $MemcacheConfigStub;
        }

        /**
         * Holds an instance of the MemcacheProvider class.
         * @var MemcacheProvider
         */
        protected $MemcacheProvider;

        /**
         * Set up the MemcacheProvider object used.
         * @return void
         */
        protected function setUp()
        {
            if (! defined('MEMCACHE_COMPRESSED'))
            {
                define('MEMCACHE_COMPRESSED', 2);
            }

            $this->MemcacheProvider = new MemcacheProvider();
        }

        /**
         * Test if the Memcache dependency can be injected.
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::injectMemcache
         */
        public function testInjectMemcache()
        {
            $MemcacheStub = $this->getMemcacheStub();
            $this->assertSame($this->MemcacheProvider, $this->MemcacheProvider->injectMemcache($MemcacheStub));
            $this->assertAttributeSame($MemcacheStub, 'Memcache', $this->MemcacheProvider);

            return $this->MemcacheProvider;
        }

        /**
         * Test if trying to overwrite the Memcache dependency throws an expcetion.
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::injectMemcache
         * @covers Brickoo\Library\Core\Exceptions\DependencyOverwriteException::__construct
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         */
        public function testInjectMemcacheOverwriteException()
        {
            $MemcacheStub = $this->getMemcacheStub();
            $this->MemcacheProvider->injectMemcache($MemcacheStub);
            $this->MemcacheProvider->injectMemcache($MemcacheStub);
        }

        /**
         * Test if the Memcache dependency can be retrieved.
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::getMemcache
         * @depends testInjectMemcache
         */
        public function testGetMemcache($MemcacheProvider)
        {
            $this->assertInstanceOf('Memcache', $MemcacheProvider->getMemcache());
        }

        /**
         * Test if trying to retrieve the not available Memcache dependency throws an exception.
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::getMemcache
         * @covers Brickoo\Library\Core\Exceptions\DependencyNotAvailableException::__construct
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyNotAvailableException
         */
        public function testGetMemcacheDependencyExcepetion()
        {
            $this->MemcacheProvider->getMemcache();
        }

        /**
         * Test if the compression can be enabled and the MemcacheProvider reference is returned.
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::enableCompression
         */
        public function testEnableCompression()
        {
            $this->assertAttributeEquals(false, 'compression', $this->MemcacheProvider);
            $this->assertSame($this->MemcacheProvider, $this->MemcacheProvider->enableCompression());
            $this->assertAttributeEquals(MEMCACHE_COMPRESSED, 'compression', $this->MemcacheProvider);

            return $this->MemcacheProvider;
        }

        /**
         * Test if the compression can be disabled and the MemcacheProvider reference is returned.
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::disableCompression
         * @depends testEnableCompression
         */
        public function testDisableCompression($MemcacheProvider)
        {
            $this->assertAttributeEquals(MEMCACHE_COMPRESSED, 'compression', $MemcacheProvider);
            $this->assertSame($MemcacheProvider, $MemcacheProvider->disableCompression());
            $this->assertAttributeEquals(false, 'compression', $this->MemcacheProvider);
        }

        /**
         * Test if the MemcacheConfig dependency can be injectd and the MemcacheProvider reference is returned.
         * Test if the configuration is recognized as configured.
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::injectMemcacheConfig
         */
        public function testInjectMemcacheConfig()
        {
            $MemcacheStub = $this->getMemcacheStub();
            $MemcacheStub->expects($this->once())
                         ->method('addServer')
                         ->will($this->returnSelf());

            $MemcacheConfigStub = $this->getMemcacheConfigStub();

            $this->MemcacheProvider->injectMemcache($MemcacheStub);
            $this->assertSame($this->MemcacheProvider, $this->MemcacheProvider->injectMemcacheConfig($MemcacheConfigStub));
            $this->assertAttributeEquals(true, 'configured', $this->MemcacheProvider);

            return $this->MemcacheProvider;
        }

        /**
         * Test if trying to overwrite the MemcacheConfig dependency throws an exception.
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::injectMemcacheConfig
         * @covers Brickoo\Library\Core\Exceptions\DependencyOverwriteException::__construct
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         * @depends testInjectMemcacheConfig
         */
        public function testInjectMemcacheConfigOverwriteException($MemcacheProvider)
        {
            $MemcacheConfigStub = $this->getMock
            (
                'Brickoo\Library\Cache\Interfaces\MemcacheConfigInterface',
                array('getServers', 'addServer', 'reset')
            );
            $MemcacheProvider->injectMemcacheConfig($MemcacheConfigStub);
        }

        /**
         * Test if trying to use a MemcacheConfig dependency without servers throws an exception.
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::injectMemcacheConfig
         * @covers Brickoo\Library\Core\Exceptions\ConfigurationMissingException::__construct
         * @expectedException Brickoo\Library\Core\Exceptions\ConfigurationMissingException
         */
        public function testInjectMemcacheConfigConfigurationException()
        {
            $MemcacheConfigStub = $this->getMock
            (
                'Brickoo\Library\Cache\Interfaces\MemcacheConfigInterface',
                array('getServers', 'addServer', 'reset')
            );

            $this->MemcacheProvider->injectMemcacheConfig($MemcacheConfigStub);
        }

        /**
         * Test if the Memcache is regognized as configured.
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::isConfigured
         * @depends testInjectMemcacheConfig
         */
        public function testIsConfigured($MemcacheProvider)
        {
            $this->assertTrue($MemcacheProvider->isConfigured());
        }

        /**
         * Test if trying to call a Memcache method without be configured throws an exception
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::get
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::checkIsConfigured
         * @covers Brickoo\Library\Cache\Exceptions\MemcacheNotConfiguredException::__construct
         * @expectedException Brickoo\Library\Cache\Exceptions\MemcacheNotConfiguredException
         */
        public function testNotConfiguredException()
        {
            $this->MemcacheProvider->get('some_identifier');
        }

        /**
         * Test if a content can be retrieved from the Memcache.
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::get
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::checkIsConfigured
         */
        public function testGet()
        {
            $MemcacheStub = $this->getMemcacheStub();
            $MemcacheStub->expects($this->once())
                         ->method('addServer')
                         ->will($this->returnSelf());
            $MemcacheStub->expects($this->once())
                         ->method('get')
                         ->will($this->returnValue('some cached content'));

            $MemcacheConfigStub = $this->getMemcacheConfigStub();

            $this->MemcacheProvider->injectMemcache($MemcacheStub);
            $this->MemcacheProvider->injectMemcacheConfig($MemcacheConfigStub);

            $this->assertSame('some cached content', $this->MemcacheProvider->get('some_identifier'));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::get
         * @expectedException InvalidArgumentException
         */
        public function testGetArgumentException()
        {
            $this->MemcacheProvider->get(array('wrongType'));
        }

        /**
         * Test if a content can be set to the Memcache and the result is returned.
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::set
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::checkIsConfigured
         */
        public function testSet()
        {
            $MemcacheStub = $this->getMemcacheStub();
            $MemcacheStub->expects($this->once())
                         ->method('addServer')
                         ->will($this->returnSelf());
            $MemcacheStub->expects($this->once())
                         ->method('set')
                         ->will($this->returnValue(true));

            $MemcacheConfigStub = $this->getMemcacheConfigStub();

            $this->MemcacheProvider->injectMemcache($MemcacheStub);
            $this->MemcacheProvider->injectMemcacheConfig($MemcacheConfigStub);

            $this->assertSame(true, $this->MemcacheProvider->set('some_identifier', 'content'));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::set
         * @expectedException InvalidArgumentException
         */
        public function testSetArgumentException()
        {
            $this->MemcacheProvider->set(array('wrongType'), 'whatever', array('wrongType'));
        }

        /**
         * Test if a cached content can be delete by its identifier and the result is returned.
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::delete
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::checkIsConfigured
         */
        public function testDelete()
        {
            $MemcacheStub= $this->getMemcacheStub();
            $MemcacheStub->expects($this->once())
                         ->method('addServer')
                         ->will($this->returnSelf());
            $MemcacheStub->expects($this->once())
                         ->method('delete')
                         ->will($this->returnValue(true));

            $MemcacheConfigStub = $this->getMemcacheConfigStub();

            $this->MemcacheProvider->injectMemcache($MemcacheStub);
            $this->MemcacheProvider->injectMemcacheConfig($MemcacheConfigStub);

            $this->assertSame(true, $this->MemcacheProvider->delete('some_identifier'));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::delete
         * @expectedException InvalidArgumentException
         */
        public function testDeleteArgumentException()
        {
            $this->MemcacheProvider->delete(array('wrongType'));
        }

        /**
         * Test if a cached content can be flushed and the result is returned.
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::flush
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::checkIsConfigured
         */
        public function testFlush()
        {
            $MemcacheStub= $this->getMemcacheStub();
            $MemcacheStub->expects($this->once())
                         ->method('addServer')
                         ->will($this->returnSelf());
            $MemcacheStub->expects($this->once())
                         ->method('flush')
                         ->will($this->returnValue(true));

            $MemcacheConfigStub = $this->getMemcacheConfigStub();

            $this->MemcacheProvider->injectMemcache($MemcacheStub);
            $this->MemcacheProvider->injectMemcacheConfig($MemcacheConfigStub);

            $this->assertSame(true, $this->MemcacheProvider->flush('some_identifier'));
        }

        /**
         * Test if a Memcache method not implemented can be called and the result is returned.
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::__call
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::checkIsConfigured
         */
        public function test__call()
        {
            $MemcacheStub= $this->getMemcacheStub();
            $MemcacheStub->expects($this->once())
                         ->method('addServer')
                         ->will($this->returnSelf());
            $MemcacheStub->expects($this->once())
                         ->method('add')
                         ->will($this->returnValue(true));

            $MemcacheConfigStub = $this->getMemcacheConfigStub();

            $this->MemcacheProvider->injectMemcache($MemcacheStub);
            $this->MemcacheProvider->injectMemcacheConfig($MemcacheConfigStub);

            $this->assertSame(true, $this->MemcacheProvider->add('some_identifier', 'some_content'));
        }

        /**
         * Test if trying to call a not available method on the Memcache object throws an exception
         * @covers Brickoo\Library\Cache\Provider\MemcacheProvider::__call
         * @expectedException BadMethodCallException
         */
        public function test__callBadMethodCallException()
        {
            $MemcacheStub= $this->getMemcacheStub();
            $MemcacheStub->expects($this->once())
                         ->method('addServer')
                         ->will($this->returnSelf());

            $MemcacheConfigStub = $this->getMemcacheConfigStub();

            $this->MemcacheProvider->injectMemcache($MemcacheStub);
            $this->MemcacheProvider->injectMemcacheConfig($MemcacheConfigStub);

            $this->MemcacheProvider->whatever();
        }

    }

?>