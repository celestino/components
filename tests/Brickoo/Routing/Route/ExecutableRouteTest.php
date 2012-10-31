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

    namespace Tests\Brickoo\Routing\Route;

    use Brickoo\Routing\Route\ExecutableRoute;

    /**
     * ExecutableRouteTest
     *
     * Test suite for the ExecutableRoute class.
     * @see Brickoo\Routing\Route\ExecutableRoute
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ExecutableRouteTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Routing\Route\ExecutableRoute::__construct
         */
        public function testConstructor() {
            $Route = $this->getMock('Brickoo\Routing\Interfaces\Route');
            $parameters = array('name' => 'test');
            $ExecutableRoute = new ExecutableRoute($Route, $parameters);
            $this->assertInstanceOf('Brickoo\Routing\Route\Interfaces\ExecutableRoute', $ExecutableRoute);
            $this->assertAttributeSame($Route, 'Route', $ExecutableRoute);
            $this->assertAttributeSame($parameters, 'parameters', $ExecutableRoute);
        }

        /**
         * @covers Brickoo\Routing\Route\ExecutableRoute::getRoute
         */
        public function testGetRoute() {
            $Route = $this->getMock('Brickoo\Routing\Interfaces\Route');
            $ExecutableRoute = new ExecutableRoute($Route);
            $this->assertSame($Route, $ExecutableRoute->getRoute());
        }

        /**
         * @covers Brickoo\Routing\Route\ExecutableRoute::getParameter
         */
        public function testGetParameter() {
            $parameters = array('param' => 'the parameter value');

            $Route = $this->getMock('Brickoo\Routing\Interfaces\Route');
            $ExecutableRoute = new ExecutableRoute($Route, $parameters);
            $this->assertEquals($parameters['param'], $ExecutableRoute->getParameter('param'));
        }

        /**
         * @covers Brickoo\Routing\Route\ExecutableRoute::getParameter
         * @expectedException InvalidArgumentException
         */
        public function testGetParameterThrowsInvalidArgumentException() {
            $Route = $this->getMock('Brickoo\Routing\Interfaces\Route');
            $ExecutableRoute = new ExecutableRoute($Route);
            $ExecutableRoute->getParameter(array('wrongType'));
        }

        /**
         * @covers Brickoo\Routing\Route\ExecutableRoute::getParameter
         * @covers Brickoo\Routing\Route\Exceptions\ParameterNotAvailable
         * @expectedException Brickoo\Routing\Route\Exceptions\ParameterNotAvailable
         */
        public function testGetParameterThrowsParameterNotAvailableException() {
            $Route = $this->getMock('Brickoo\Routing\Interfaces\Route');
            $ExecutableRoute = new ExecutableRoute($Route);
            $ExecutableRoute->getParameter('not.available');
        }

        /**
         * @covers Brickoo\Routing\Route\ExecutableRoute::hasParameter
         */
        public function testHasParameter() {
            $parameters = array('param' => 'the parameter value');

            $Route = $this->getMock('Brickoo\Routing\Interfaces\Route');
            $ExecutableRoute = new ExecutableRoute($Route, $parameters);
            $this->assertFalse($ExecutableRoute->hasParameter('nots.available'));
            $this->assertTrue($ExecutableRoute->hasParameter('param'));
        }

        /**
         * @covers Brickoo\Routing\Route\ExecutableRoute::hasParameter
         * @expectedException InvalidArgumentException
         */
        public function testHasParameterThrowsInvalidArgumentException() {
            $Route = $this->getMock('Brickoo\Routing\Interfaces\Route');
            $ExecutableRoute = new ExecutableRoute($Route);
            $ExecutableRoute->hasParameter(array('wrongType'));
        }

        /**
         * @covers Brickoo\Routing\Route\ExecutableRoute::execute
         */
        public function testExecute() {
            require_once "Assets/ExecutableController.php";

            $Route = $this->getMock('Brickoo\Routing\Interfaces\Route');
            $Route->expects($this->once())
                  ->method('getController')
                  ->will($this->returnValue('\Tests\Brickoo\Routing\Route\Assets\ExecutableController'));
            $Route->expects($this->once())
                  ->method('getAction')
                  ->will($this->returnValue('returnValues'));

            $ExecutableRoute = new ExecutableRoute($Route, array('param1' => 'value1', 'param2' => 'value2'));
            $this->assertEquals('value1 & value2', $ExecutableRoute->execute());
        }

    }