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
     * 3. Neither the name of Application nor the names of its contributors may be used
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

    use Brickoo\Core\Controller;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Test suite for the Controller class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ControllerTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the Controller class.
         * @var \Brickoo\Core\Interfaces\ControllerInterface
         */
        protected $Controller;

        /**
         * Sets up the Controller instance used in the tests.
         * @return void
         */
        protected function setUp()
        {
            $this->Controller = new Controller();
        }

        /**
         * Test if the Registry can be injected and the Controller reference is returned.
         * @covers Brickoo\Core\Controller::Registry
         * @covers Brickoo\Core\Controller::getDependency
         */
        public function testInjectAndRetriveRegistry()
        {
            $Registry = $this->getMock('Brickoo\Core\Interfaces\RegistryInterface');
            $this->assertSame($this->Controller, $this->Controller->Registry($Registry));
            $this->assertAttributeContains($Registry, 'dependencies', $this->Controller);
            $this->assertSame($Registry, $this->Controller->Registry());
        }

        /**
         * Test if trying to retrieve a not available Registry dependency throws an exception.
         * @covers Brickoo\Core\Controller::Registry
         * @covers Brickoo\Core\Controller::getDependency
         * @covers Brickoo\Core\Exceptions\DependencyNotAvailableException
         * @expectedException Brickoo\Core\Exceptions\DependencyNotAvailableException
         */
        public function testRegistrydependencyException()
        {
            $this->Controller->Registry();
        }

        /**
         * Test if the Application can be injected and the Controller reference is returned.
         * @covers Brickoo\Core\Controller::Application
         * @covers Brickoo\Core\Controller::getDependency
         */
        public function testInjectAndRetriveApplication()
        {
            $Application = $this->getMock('Brickoo\Core\Application', null, array(), 'ApplicationMock', false);
            $this->assertSame($this->Controller, $this->Controller->Application($Application));
            $this->assertAttributeContains($Application, 'dependencies', $this->Controller);
            $this->assertSame($Application, $this->Controller->Application());
        }

        /**
         * Test if trying to retrieve a not available Application dependency throws an exception.
         * @covers Brickoo\Core\Controller::Application
         * @covers Brickoo\Core\Controller::getDependency
         * @covers Brickoo\Core\Exceptions\DependencyNotAvailableException
         * @expectedException Brickoo\Core\Exceptions\DependencyNotAvailableException
         */
        public function testApplicationdependencyException()
        {
            $this->Controller->Application();
        }

        /**
         * Test if the Request can be injected and the Controller reference is returned.
         * @covers Brickoo\Core\Controller::Request
         * @covers Brickoo\Core\Controller::getDependency
         */
        public function testInjectAndRetriveRequest()
        {
            $Request = $this->getMock('Brickoo\Core\Interfaces\RequestInterface');
            $this->assertSame($this->Controller, $this->Controller->Request($Request));
            $this->assertAttributeContains($Request, 'dependencies', $this->Controller);
            $this->assertSame($Request, $this->Controller->Request());
        }

        /**
         * Test if trying to retrieve a not available Request dependency throws an exception.
         * @covers Brickoo\Core\Controller::Request
         * @covers Brickoo\Core\Controller::getDependency
         * @covers Brickoo\Core\Exceptions\DependencyNotAvailableException
         * @expectedException Brickoo\Core\Exceptions\DependencyNotAvailableException
         */
        public function testRequestdependencyException()
        {
            $this->Controller->Request();
        }

        /**
         * @covers Brickoo\Core\Controller::Route
         * @covers Brickoo\Core\Controller::getDependency
         */
        public function testInjectAndRetriveRoute()
        {
            $Route = $this->getMock('Brickoo\Routing\Interfaces\RequestRouteInterface');
            $this->assertSame($this->Controller, $this->Controller->Route($Route));
            $this->assertAttributeContains($Route, 'dependencies', $this->Controller);
            $this->assertSame($Route, $this->Controller->Route());
        }

        /**
         * Test if trying to retrieve a not available Route dependency throws an exception.
         * @covers Brickoo\Core\Controller::Route
         * @covers Brickoo\Core\Controller::getDependency
         * @covers Brickoo\Core\Exceptions\DependencyNotAvailableException
         * @expectedException Brickoo\Core\Exceptions\DependencyNotAvailableException
         */
        public function testRoutedependencyException()
        {
            $this->Controller->Route();
        }

    }