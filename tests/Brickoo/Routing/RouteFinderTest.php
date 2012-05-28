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

    use Brickoo\Routing\RouteFinder;

    require_once ('PHPUnit/Autoload.php');

    /**
     * RouteFinderTest
     *
     * Test suite for the RouteFinder class.
     * @see Brickoo\Routing\RouteFinder
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RouteFinderTest extends \PHPUnit_Framework_TestCase {

        /**
         * Returns a pre configured RouteFinder instance.
         * @return \Brickoo\Routing\RouteFinder
         */
        protected function getRouteFinder() {
            $RouteCollection = $this->getMock('Brickoo\Routing\Interfaces\RouteCollection');
            $Request = $this->getMock('Brickoo\Core\Interfaces\Request');
            $Aliases = $this->getMock('Brickoo\Memory\Interfaces\Container');

            return new RouteFinder($RouteCollection, $Request, $Aliases);
        }

        /**
         * Test if the class properties are set.
         * @covers Brickoo\Routing\RouteFinder::__construct
         */
        public function testConstruct() {
            $RouteCollection = $this->getMock('Brickoo\Routing\Interfaces\RouteCollection');
            $Request = $this->getMock('Brickoo\Core\Interfaces\Request');
            $Aliases = $this->getMock('Brickoo\Memory\Interfaces\Container');

            $this->assertInstanceOf(
                'Brickoo\Routing\Interfaces\RouteFinder',
                ($RouteFinder = new RouteFinder($RouteCollection, $Request, $Aliases))
            );
            $this->assertAttributeEquals($RouteCollection, 'RouteCollection', $RouteFinder);
            $this->assertAttributeEquals($Request, 'Request', $RouteFinder);
            $this->assertAttributeEquals($Aliases, 'Aliases', $RouteFinder);
        }

        /**
         * Test if the matched Route can be found.
         * @covers Brickoo\Routing\RouteFinder::find
         */
        public function testFind() {
            $Route = $this->getMock('Brickoo\Routing\Route', array('getPath', 'getMethod'), array('test.route'));
            $Route->expects($this->any())
                  ->method('getPath')
                  ->will($this->returnValue('/some/path'));
            $Route->expects($this->any())
                  ->method('getMethod')
                  ->will($this->returnValue('GET'));

            $RouteCollection = $this->getMock('Brickoo\Routing\RouteCollection', array('getRoutes'));
            $RouteCollection->expects($this->once())
                            ->method('getRoutes')
                            ->will($this->returnValue(array($Route)));

            $Request = $this->getMock('Brickoo\Core\Interfaces\Request',
                array('getPath', 'getMethod', 'getFormat','getHost', 'getProtocol')
            );
            $Request->expects($this->any())
                    ->method('getPath')
                    ->will($this->returnValue('/some/path'));
            $Request->expects($this->any())
                    ->method('getMethod')
                    ->will($this->returnValue('GET'));

            $Aliases = $this->getMock('Brickoo\Memory\Container', array('isEmpty'));
            $Aliases->expects($this->once())
                    ->method('isEmpty')
                    ->will($this->returnValue(true));

            $RouteFinder = new RouteFinder($RouteCollection, $Request, $Aliases);
            $this->assertInstanceOf('Brickoo\Routing\Interfaces\RequestRoute', $RouteFinder->find());
        }

        /**
         * Test if no routes are available throws an exception.
         * @covers Brickoo\Routing\RouteFinder::find
         * @covers Brickoo\Routing\Exceptions\RequestHasNoRouteException
         * @expectedException Brickoo\Routing\Exceptions\RequestHasNoRouteException
         */
        public function testFindRequestHasNoRouteException() {
            $RouteCollection = $this->getMock('Brickoo\Routing\RouteCollection', array('getRoutes'));
            $RouteCollection->expects($this->once())
                            ->method('getRoutes')
                            ->will($this->returnValue(array()));

            $Request = $this->getMock('Brickoo\Core\Interfaces\Request');
            $Aliases = $this->getMock('Brickoo\Memory\Interfaces\Container');

            $RouteFinder = new RouteFinder($RouteCollection, $Request, $Aliases);
            $RouteFinder->find();
        }

        /**
         * Test if no routes did match throws an exception.
         * @covers Brickoo\Routing\RouteFinder::find
         * @covers Brickoo\Routing\Exceptions\RequestHasNoRouteException
         * @expectedException Brickoo\Routing\Exceptions\RequestHasNoRouteException
         */
        public function testFindNoRoutesMatchException() {
            $Route = $this->getMock('Brickoo\Routing\Route', array('getPath', 'getMethod'), array('test.route'));
            $Route->expects($this->any())
                  ->method('getPath')
                  ->will($this->returnValue('/wrong/path'));
            $Route->expects($this->any())
                  ->method('getMethod')
                  ->will($this->returnValue('GET'));

            $RouteCollection = $this->getMock('Brickoo\Routing\RouteCollection', array('getRoutes'));
            $RouteCollection->expects($this->once())
                            ->method('getRoutes')
                            ->will($this->returnValue(array()));

            $Request = $this->getMock('Brickoo\Core\Interfaces\Request',
                array('getPath', 'getMethod', 'getFormat','getHost', 'getProtocol')
            );
            $Request->expects($this->any())
                    ->method('getPath')
                    ->will($this->returnValue('/some/path'));
            $Request->expects($this->any())
                    ->method('getMethod')
                    ->will($this->returnValue('GET'));

            $Aliases = $this->getMock('Brickoo\Memory\Interfaces\Container');

            $RouteFinder = new RouteFinder($RouteCollection, $Request, $Aliases);
            $RouteFinder->find();
        }

        /**
         * Test if the Route rules parameters can be retrieved.
         * @covers Brickoo\Routing\RouteFinder::getRulesParamaters
         */
        public function testGetRulesParamaters() {
            $pathMatches = array(
                'parameter1'  => 'value 1',
            );

            $rules = array(
                'parameter1'  => '\\w',
                'parameter2'  => '\\w'
            );

            $Route = $this->getMock(
                'Brickoo\Routing\Route',
                array('hasDefaultValue', 'getRules', 'getDefaultValue'),
                array('test.route')
            );
            $Route->expects($this->once())
                  ->method('getRules')
                  ->will($this->returnValue($rules));
            $Route->expects($this->once())
                  ->method('hasDefaultValue')
                  ->will($this->returnValue(true));
            $Route->expects($this->once())
                  ->method('getDefaultValue')
                  ->with('parameter2')
                  ->will($this->returnValue('value 2'));

            $expected = array(
                'parameter1'  => 'value 1',
                'parameter2'  => 'value 2'
            );

            $RouteFinder = $this->getRouteFinder();
            $this->assertEquals($expected, $RouteFinder->getRulesParamaters($Route, $pathMatches));
        }

        /**
         * Test if the Route parameters can be retrieved containing a valid format.
         * @covers Brickoo\Routing\RouteFinder::getRouteParameters
         */
        public function testGetRouteParameters() {
            $pathMatches = array(
                '__FORMAT__'  => 'xml'
            );

            $Route = $this->getMock(
                'Brickoo\Routing\Route',
                array('hasRules', 'getRules'),
                array('test.route')
            );
            $Route->expects($this->once())
                  ->method('hasRules')
                  ->will($this->returnValue(true));
            $Route->expects($this->once())
                  ->method('getRules')
                  ->will($this->returnValue(array()));

            $expected = array(
                'format'      => 'xml'
            );

            $RouteFinder = $this->getRouteFinder();
            $this->assertEquals($expected, $RouteFinder->getRouteParameters($Route, $pathMatches));
        }

        /**
         * Test if the Route parameters can be retrieved and contains the standard format.
         * @covers Brickoo\Routing\RouteFinder::getRouteParameters
         */
        public function testGetRouteParametersWithStandardFormat() {
            $Route = $this->getMock(
                'Brickoo\Routing\Route',
                array('hasRules', 'getDefaultFormat'),
                array('test.route')
            );
            $Route->expects($this->once())
                  ->method('hasRules')
                  ->will($this->returnValue(false));
            $Route->expects($this->once())
                  ->method('getDefaultFormat')
                  ->will($this->returnValue('html'));

            $expected = array(
                'format'      => 'html'
            );

            $RouteFinder = $this->getRouteFinder();
            $this->assertEquals($expected, $RouteFinder->getRouteParameters($Route, array()));
        }

        /**
         * Test if a RequestRoute can be created with the parameters.
         * @covers Brickoo\Routing\RouteFinder::createRequestRoute
         */
        public function testCreateRequestRoute() {
            $pathMatches = array();

            $Route = $this->getMock(
                'Brickoo\Routing\Route',
                array('hasRules', 'getDefaultFormat'),
                array('test.route')
            );
            $Route->expects($this->once())
                  ->method('hasRules')
                  ->will($this->returnValue(false));
            $Route->expects($this->once())
                  ->method('getDefaultFormat')
                  ->will($this->returnValue('json'));

            $RouteFinder = $this->getRouteFinder();
            $this->assertInstanceOf(
                'Brickoo\Routing\Interfaces\RequestRoute',
                ($RequestRoute = $RouteFinder->createRequestRoute($Route, $pathMatches))
            );
            $this->assertEquals(array('format' => 'json'), $RequestRoute->Params()->toArray());
        }

        /**
         * Test if the route can be recognized as request matching route.
         * @covers Brickoo\Routing\RouteFinder::isAllowedRoute
         */
        public function testIsAllowedRoute() {
            $Route = $this->getMock(
                'Brickoo\Routing\Route',
                array('getMethod', 'getHostname'),
                array('test.route')
            );
            $Route->expects($this->exactly(2))
                  ->method('getMethod')
                  ->will($this->returnValue('GET'));
            $Route->expects($this->once())
                  ->method('getHostname')
                  ->will($this->returnValue('localhost'));

            $RouteCollection = $this->getMock('Brickoo\Routing\Interfaces\RouteCollection');
            $Request = $this->getMock('Brickoo\Core\Interfaces\Request');
            $Request->expects($this->exactly(2))
                    ->method('getMethod')
                    ->will($this->onConsecutiveCalls('GET', 'POST'));
            $Request->expects($this->once())
                    ->method('getHost')
                    ->will($this->returnValue('localhost'));
            $Aliases = $this->getMock('Brickoo\Memory\Interfaces\Container');

            $RouteFinder = new RouteFinder($RouteCollection, $Request, $Aliases);

            $this->assertTrue($RouteFinder->isAllowedRoute($Route));
            $this->assertFalse($RouteFinder->isAllowedRoute($Route));
        }

        /**
         * Test if the expected route format regular expression is generated.
         * @covers Brickoo\Routing\RouteFinder::getRegexRouteFormat
         */
        public function testGetRegexRouteFormat() {
            $Route = $this->getMock('Brickoo\Routing\Route', array('getFormat'), array('test.route'));
            $Route->expects($this->once())
                  ->method('getFormat')
                  ->will($this->returnValue('xml'));

            $RouteFinder = $this->getRouteFinder();
            $this->assertEquals('(\.(?<__FORMAT__>xml))?', $RouteFinder->getRegexRouteFormat($Route));
        }

        /**
         * Test if the route alias paths regular expression is generated.
         * @covers Brickoo\Routing\RouteFinder::getRouteAliasesPath
         */
        public function testGetRouteAliasesPath() {
            $RouteCollection = $this->getMock('Brickoo\Routing\Interfaces\RouteCollection');
            $Request = $this->getMock('Brickoo\Core\Interfaces\Request');
            $Aliases = $this->getMock('Brickoo\Memory\Interfaces\Container');
            $Aliases->expects($this->once())
                    ->method('isEmpty')
                    ->will($this->returnValue(false));
            $Aliases->expects($this->once())
                    ->method('rewind');
            $Aliases->expects($this->once())
                    ->method('next');
            $Aliases->expects($this->exactly(2))
                    ->method('valid')
                    ->will($this->onConsecutiveCalls(true, true));
            $Aliases->expects($this->exactly(2))
                    ->method('key')
                    ->will($this->onConsecutiveCalls('skip', 'home'));
            $Aliases->expects($this->once())
                    ->method('current')
                    ->will($this->returnValue('start'));

            $Route = $this->getMock('Brickoo\Routing\Route', array('getPath'), array('test.route'));
            $Route->expects($this->once())
                  ->method('getPath')
                  ->will($this->returnValue('/home'));

            $RouteFinder = new RouteFinder($RouteCollection, $Request, $Aliases);
            $this->assertEquals('/(home|start)', $RouteFinder->getRouteAliasesPath($Route));
        }

        /**
         * Test if the route path regular expression is generated.
         * @covers Brickoo\Routing\RouteFinder::getRegexFromRoutePath
         */
        public function testGetRegexFromRoutePath() {
            $RouteCollection = $this->getMock('Brickoo\Routing\Interfaces\RouteCollection');
            $Request = $this->getMock('Brickoo\Core\Interfaces\Request');
            $Aliases = $this->getMock('Brickoo\Memory\Interfaces\Container');
            $Aliases->expects($this->once())
                    ->method('isEmpty')
                    ->will($this->returnValue(true));

            $Route = $this->getMock(
                'Brickoo\Routing\Route',
                array('getFormat','getPath', 'hasRule', 'hasDefaultValue', 'getRule'),
                array('test.route')
            );
            $Route->expects($this->once())
                  ->method('getFormat')
                  ->will($this->returnValue(null));
            $Route->expects($this->once())
                  ->method('getPath')
                  ->will($this->returnValue('/{home}/{page}/{selected}'));
            $Route->expects($this->exactly(3))
                  ->method('hasRule')
                  ->will($this->onConsecutiveCalls(true, true, false));
            $Route->expects($this->exactly(2))
                  ->method('hasDefaultValue')
                  ->will($this->onConsecutiveCalls(true, false));
            $Route->expects($this->exactly(2))
                  ->method('getRule')
                  ->will($this->onConsecutiveCalls('\w', '[0-9]'));

            $RouteFinder = new RouteFinder($RouteCollection, $Request, $Aliases);
            $this->assertEquals(
                '~^/(/(?<home>(\w)?))?/(?<page>[0-9])/ef7de3f485174ff47f061ad27d83d0ee(\.[a-zA-Z0-9]+)?$~i',
                $RouteFinder->getRegexFromRoutePath($Route)
            );
        }

    }