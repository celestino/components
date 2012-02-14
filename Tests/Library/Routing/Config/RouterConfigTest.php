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

    use Brickoo\Library\Routing\Config\RouterConfig;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * RouterConfigTest
     *
     * Test suite for the RouterConfig class.
     * @see Brickoo\Library\Routing\Config\RouterConfig
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RouterConfigTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the RouterConfig class.
         * @var Brickoo\Library\Routing\Config\RouterConfig
         */
        protected $RouterConfig;

        /**
         * Sets the RouterConfig instance used.
         * @return void
         */
        protected function setUp()
        {
            $this->RouterConfig = new RouterConfig();
        }

        /**
         * Test if the configuartion can be set and the RouterConfig reference is returned.
         * Test if the configuration can be retrieved.
         * @covers Brickoo\Library\Routing\Config\RouterConfig::getConfiguration
         * @covers Brickoo\Library\Routing\Config\RouterConfig::setConfiguration
         */
        public function testGetSetConfiguration()
        {
            $config = array
            (
                'cacheDirectory'    => '/path/to/cache/directory',
                'modules'           => array('module' => '/module/path'),
            );

            $this->assertSame($this->RouterConfig, $this->RouterConfig->setConfiguration($config));
            $this->assertAttributeEquals($config, 'configuration', $this->RouterConfig);
            $this->assertEquals($config, $this->RouterConfig->getConfiguration());

            return $this->RouterConfig;
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Routing\Config\RouterConfig::setConfiguration
         * @expectedException InvalidArgumentException
         */
        public function testSetConfigurationArgumentException()
        {
            $this->RouterConfig->setConfiguration(array('missingKeys'));
        }

        /**
         * Testif the configuration can be applied to the Router.
         * @covers Brickoo\Library\Routing\Config\RouterConfig::configure
         * @depends testGetSetConfiguration
         */
        public function testConfigure($RouterConfig)
        {
            $RouterStub = $this->getMock
            (
                'Brickoo\Library\Routing\Router',
                array('setCacheDirectory', 'setModules'),
                array($this->getMock('Brickoo\Library\Core\Interfaces\RequestInterface'))
            );

            $RouterStub->expects($this->once())
                       ->method('setCacheDirectory')
                       ->will($this->returnSelf());
            $RouterStub->expects($this->once())
                       ->method('setModules')
                       ->will($this->returnSelf());

            $this->assertSame($RouterConfig, $RouterConfig->configure($RouterStub));
        }

    }