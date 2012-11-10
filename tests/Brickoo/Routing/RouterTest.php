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

    namespace Tests\Brickoo\Routing;

    use Brickoo\Routing\Router;

    /**
     * RouterTest
     *
     * Test suite for the Router class.
     * @see Brickoo\Routing\Router
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RouterTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Routing\Router::__construct
         */
        public function testConstructor() {
            $Collector = $this->getMock('Brickoo\Routing\Collector\Interfaces\Collector');
            $Matcher = $this->getMock('Brickoo\Routing\Matcher\Interfaces\Matcher');

            $Router = new Router($Collector, $Matcher);
            $this->assertInstanceOf('Brickoo\Routing\Interfaces\Router', $Router);
            $this->assertAttributeSame($Collector, 'Collector', $Router);
            $this->assertAttributeSame($Matcher, 'Matcher', $Router);
        }

        /**
         * @covers Brickoo\Routing\Router::getRoute
         * @covers Brickoo\Routing\Router::getRouteCollection
         */
        public function testGetRoute() {
            $Route = $this->getMock('Brickoo\Routing\Interfaces\Route');

            $RouteCollection = $this->getMock('Brickoo\Routing\Route\Interfaces\Collection');
            $RouteCollection->expects($this->any())
                            ->method('hasRoute')
                            ->with('unit.test.route')
                            ->will($this->returnValue(true));
            $RouteCollection->expects($this->any())
                            ->method('getRoute')
                            ->with('unit.test.route')
                            ->will($this->returnValue($Route));

            $Matcher = $this->getMock('Brickoo\Routing\Matcher\Interfaces\Matcher');

            $Router = new Router($this->getCollectorStub($RouteCollection), $Matcher);
            $this->assertSame($Route, $Router->getRoute('unit.test.route'));
        }

        /**
         * @covers Brickoo\Routing\Router::getRoute
         * @covers Brickoo\Routing\Router::getRouteCollection
         * @covers Brickoo\Routing\Route\Exceptions\RouteNotFound
         * @expectedException Brickoo\Routing\Route\Exceptions\RouteNotFound
         */
        public function testGetRouteThrowsRouteNotFoundException() {
            $RouteCollection = $this->getMock('Brickoo\Routing\Route\Interfaces\Collection');
            $RouteCollection->expects($this->any())
                            ->method('hasRoute')
                            ->with('unit.test.route')
                            ->will($this->returnValue(false));

            $Matcher = $this->getMock('Brickoo\Routing\Matcher\Interfaces\Matcher');

            $Router = new Router($this->getCollectorStub($RouteCollection), $Matcher);
            $Router->getRoute('unit.test.route');
        }

        /**
         * @covers Brickoo\Routing\Router::getRoute
         * @expectedException InvalidArgumentException
         */
        public function testGetRouteThrowsInvalidArgumentException() {
            $Collector = $this->getMock('Brickoo\Routing\Collector\Interfaces\Collector');
            $Matcher = $this->getMock('Brickoo\Routing\Matcher\Interfaces\Matcher');

            $Router = new Router($Collector, $Matcher);
            $Router->getRoute(array('wrongType'));
        }

        /**
         * @covers Brickoo\Routing\Router::hasRoute
         * @covers Brickoo\Routing\Router::getRouteCollection
         */
        public function testHasRoute() {
            $RouteCollection = $this->getMock('Brickoo\Routing\Route\Interfaces\Collection');
            $RouteCollection->expects($this->any())
                            ->method('hasRoute')
                            ->will($this->onConsecutiveCalls(true, false));

            $Matcher = $this->getMock('Brickoo\Routing\Matcher\Interfaces\Matcher');

            $Router = new Router($this->getCollectorStub($RouteCollection), $Matcher);
            $this->assertTrue($Router->hasRoute('unit.test.route'));
            $this->assertFalse($Router->hasRoute('route.does.not.exist'));
        }

        /**
         * @covers Brickoo\Routing\Router::HasRoute
         * @expectedException InvalidArgumentException
         */
        public function testHasRouteThrowsInvalidArgumentException() {
            $Collector = $this->getMock('Brickoo\Routing\Collector\Interfaces\Collector');
            $Matcher = $this->getMock('Brickoo\Routing\Matcher\Interfaces\Matcher');

            $Router = new Router($Collector, $Matcher);
            $Router->hasRoute(array('wrongType'));
        }

        /**
         * @covers Brickoo\Routing\Router::getExecutableRoute
         */
        public function testGetExecutableRoute() {
            $Route = $this->getMock('Brickoo\Routing\Interfaces\Route');

            $RouteCollection = $this->getMock('Brickoo\Routing\Route\Interfaces\Collection');
            $RouteCollection->expects($this->any())
                            ->method('getRoutes')
                            ->will($this->returnValue(array($Route)));

            $Router = new Router($this->getCollectorStub($RouteCollection), $this->getMatcherMock($Route));
            $this->assertInstanceOf('Brickoo\Routing\Route\Interfaces\ExecutableRoute', ($ExecutableRoute = $Router->getExecutableRoute()));
            $this->assertSame($ExecutableRoute, $Router->getExecutableRoute());
        }

        /**
         * @covers Brickoo\Routing\Router::getExecutableRoute
         * @covers Brickoo\Routing\Exceptions\NoMatchingRouteFound
         * @expectedException Brickoo\Routing\Exceptions\NoMatchingRouteFound
         */
        public function testGetExecutableRouteThrowsNoExecutableRouteFoundException() {
            $RouteCollection = $this->getMock('Brickoo\Routing\Route\Interfaces\Collection');
            $RouteCollection->expects($this->any())
                            ->method('getRoutes')
                            ->will($this->returnValue(array()));

            $Matcher = $this->getMock('Brickoo\Routing\Matcher\Interfaces\Matcher');
            $Router = new Router($this->getCollectorStub($RouteCollection), $Matcher);
            $Router->getExecutableRoute();
        }

        /**
         * Stub creator for the route search dependency.
         * @param \Brickoo\Routing\Route\Interfaces\Collection $RouteCollection the route collection to return by the search
         * @return \Brickoo\Routing\Route\Interfaces\Collector
         */
        private function getCollectorStub($RouteCollection) {
            $CollectorStub = $this->getMock('Brickoo\Routing\Collector\Interfaces\Collector');
            $CollectorStub->expects($this->any())
                       ->method('collect')
                       ->will($this->returnValue($RouteCollection));
            return $CollectorStub;
        }

        /**
         * Mock creator for the route matcher dependency.
         * @param \Brickoo\Routing\Interfaces\Route $Route
         * @return Brickoo\Routing\Matcher\Interfaces\Matcher
         */
        private function getMatcherMock($Route) {
            $MatcherMock = $this->getMock('Brickoo\Routing\Matcher\Interfaces\Matcher');
            $MatcherMock->expects($this->any())
                        ->method('matches')
                        ->with($Route)
                        ->will($this->returnValue(true));
            $MatcherMock->expects($this->any())
                        ->method('getParameters')
                        ->will($this->returnValue(array('key' => 'value')));
            return $MatcherMock;
        }

    }