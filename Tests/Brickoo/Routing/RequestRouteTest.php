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

    use Brickoo\Routing\RequestRoute;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * RequestRouteTest
     *
     * Test suite for the RequestRoute class.
     * @see Brickoo\Routing\RequestRoute
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RequestRouteTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the RequestRoute class.
         * @var \Brickoo\Routing\RequestRoute
         */
        protected $RequestRoute;

        /**
         * Sets up the RequestRoute instance ised.
         * @return void
         */
        protected function setUp()
        {
            $this->RequestRoute = new RequestRoute(
                $this->getMock('Brickoo\Routing\Interfaces\RouteInterface')
            );
        }

        /**
         * Test if the ResquestRoute implements the requestRouteInterface
         * @covers Brickoo\Routing\RequestRoute::__construct
         */
        public function testConstructor()
        {
            $this->assertInstanceOf('Brickoo\Routing\Interfaces\RequestRouteInterface', $this->RequestRoute);
        }

        /**
         * Test if the Params container can be injected and the RequesstRoute reference is returned.
         * @covers Brickoo\Routing\RequestRoute::Params
         * @covers Brickoo\Routing\RequestRoute::getDependency
         */
        public function testInjectParams()
        {
            $Container = $this->getMock('Brickoo\Memory\Interfaces\ContainerInterface');
            $this->assertSame($this->RequestRoute, $this->RequestRoute->Params($Container));
            $this->assertAttributeContains($Container, 'dependencies', $this->RequestRoute);
            $this->assertSame($Container, $this->RequestRoute->Params());
        }

        /**
         * Test if the Params container can be lazy initialized and returned.
         * @covers Brickoo\Routing\RequestRoute::Params
         * @covers Brickoo\Routing\RequestRoute::getDependency
         */
        public function testParamsLazyInitialization()
        {
            $this->assertInstanceOf(
                'Brickoo\Memory\Interfaces\ContainerInterface',
                ($Container = $this->RequestRoute->Params())
            );
            $this->assertAttributeContains($Container, 'dependencies', $this->RequestRoute);
        }

        /**
         * test if the method are forwarded to the Route.
         * @covers Brickoo\Routing\RequestRoute::__call
         */
        public function testMagicCall()
        {
            $Route = $this->getMock('Brickoo\Routing\Route', array('setPath'));
            $Route->expects($this->once())
                  ->method('setPath')
                  ->with('/path/to/somewhere')
                  ->will($this->returnSelf());

            $RequestRoute = new RequestRoute($Route);
            $this->assertSame($Route, $RequestRoute->setPath('/path/to/somewhere'));
        }

        /**
         * test if calling a not available method throws an exception.
         * @covers Brickoo\Routing\RequestRoute::__call
         * @expectedException BadMethodCallException
         */
        public function testMagicCallBadMethodException()
        {
            $this->RequestRoute->unknowed('test');
        }

    }
