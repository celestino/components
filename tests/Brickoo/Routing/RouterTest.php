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

    require_once ('PHPUnit/Autoload.php');

    /**
     * RouterTest
     *
     * Test suite for the Router class.
     * This test suite uses the PHP system temporary directory to store and load the test routes.
     * @see Brickoo\Routing\Router
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RouterTest extends \PHPUnit_Framework_TestCase {

        /**
         * Returns a Request stub .
         * @return \Brickoo\Core\Interfaces\Dynamic
         */
        public function getRequestStub() {
            return $this->getMock
            (
                'Brickoo\Core\Interfaces\Request',
                array('getPath', 'getMethod', 'getHost', 'getProtocol', 'getFormat')
            );
        }

        /**
        * Returns a RouteCollection stub.
        * @return \Brickoo\Routing\RouteCollection
        */
        public function getRouteCollectionStub() {
            return $this->getMock(
                'Brickoo\Routing\RouteCollection',
                array('getRoutes', 'addRoutes', 'createRoute', 'hasRoutes', 'getRoute', 'hasRoute')
            );
        }

        /**
         * Returns an Aliases stub.
         * @return \Brickoo\Memory\Interfaces\Container
         */
        public function getAliasesStub() {
            return $this->getMock(
                'Brickoo\Memory\Container',
                array('valid', 'key', 'current', 'next', 'rewind', 'isEmpty')
            );
        }

        /**
        * Returns a Route stub .
        * @return \Brickoo\Routing\Interfaces\Route
        */
        public function getRouteStub() {
            return $this->getMock('Brickoo\Routing\Interfaces\Route');
        }

        /**
        * Returns an EventManager stub .
        * @return \Brickoo\Event\Manager
        */
        public function getEventManagerStub() {
            return $this->getMock('Brickoo\Event\Manager', array('notify', 'ask'));
        }

        /**
         * Holds an instance of the Router class.
         * @var Brickoo\Routing\Router
         */
        protected $Router;

        /**
         * Sets up the Router instance used for testing.
         * @return void
         */
        protected function setUp() {
            $this->Router = new Router($this->getRequestStub());
        }

        /**
         * Test if the Route instance is created and implements the interface.
         * @covers Brickoo\Routing\Router::__construct
         * @covers Brickoo\Routing\Events
         */
        public function testConstruct() {
            $this->assertInstanceOf('Brickoo\Routing\Interfaces\Router', $this->Router);
        }

        /**
         * Test if the Alias dependency can be injected and the Router reference is returned.
         * @covers Brickoo\Routing\Router::Aliases
         */
        public function testInjectAliases() {
            $Aliases = $this->getAliasesStub();
            $this->assertSame($this->Router, $this->Router->Aliases($Aliases));
            $this->assertAttributeContains($Aliases, 'dependencies', $this->Router);
            $this->assertSame($Aliases, $this->Router->Aliases());
        }

        /**
         * Test if the Aliases can be lazy initialized.
         * @covers Brickoo\Routing\Router::Aliases
         */
        public function testAliasesLazyInitialization() {
            $this->assertInstanceOf(
                'Brickoo\Memory\Container',
                ($Aliases = $this->Router->Aliases())
            );

            $this->assertAttributeContains($Aliases, 'dependencies', $this->Router);
            $this->assertSame($Aliases, $this->Router->Aliases());
        }

        /**
         * Test if the EventManager dependency can be injected and the Router reference is returned.
         * @covers Brickoo\Routing\Router::EventManager
         */
        public function testInjectEventManager() {
            $EventManager = $this->getEventManagerStub();
            $this->assertSame($this->Router, $this->Router->EventManager($EventManager));
            $this->assertAttributeContains($EventManager, 'dependencies', $this->Router);
            $this->assertSame($EventManager, $this->Router->EventManager());
        }

        /**
         * Test if the EventManager can be lazy initialized.
         * @covers Brickoo\Routing\Router::EventManager
         */
        public function testEventManagerLazyInitialization() {
            $this->assertInstanceOf(
                'Brickoo\Event\Interfaces\Manager',
                ($EventManager = $this->Router->EventManager())
            );

            $this->assertAttributeContains($EventManager, 'dependencies', $this->Router);
            $this->assertSame($EventManager, $this->Router->EventManager());
        }

        /**
         * Test if the RouteFinder dependency can be injected and the Router reference is returned.
         * @covers Brickoo\Routing\Router::RouteFinder
         */
        public function testInjectRouteFinder() {
            $RouteFinder = new \Brickoo\Routing\RouteFinder(
                new \Brickoo\Routing\RouteCollection(),
                new \Brickoo\Http\Request(),
                new \Brickoo\Memory\Container()
            );
            $this->assertSame($this->Router, $this->Router->RouteFinder($RouteFinder));
            $this->assertAttributeContains($RouteFinder, 'dependencies', $this->Router);
            $this->assertSame($RouteFinder, $this->Router->RouteFinder());
        }

        /**
         * Test if the RouteFinder can be lazy initialized.
         * @covers Brickoo\Routing\Router::RouteFinder
         */
        public function testRouteFinderLazyInitialization() {
            $this->assertInstanceOf(
                'Brickoo\Routing\Interfaces\RouteFinder',
                ($RouteFinder = $this->Router->RouteFinder())
            );

            $this->assertAttributeContains($RouteFinder, 'dependencies', $this->Router);
            $this->assertSame($RouteFinder, $this->Router->RouteFinder());
        }

        /**
         * Test if the available modules can be set and the Router reference is returned.
         * Test if the modules can be retrieved.
         * @covers Brickoo\Routing\Router::setModules
         * @covers Brickoo\Routing\Router::getModules
         */
        public function testGetSetModules() {
            $modules = array('Module' => '\Module\path');
            $this->assertSame($this->Router, $this->Router->setModules($modules));
            $this->assertAttributeEquals($modules, 'modules', $this->Router);
            $this->assertEquals($modules, $this->Router->getModules());

            return $this->Router;
        }

        /**
         * Test if the available modules are recognized.
         * @covers Brickoo\Routing\Router::hasModules
         */
        public function testHasModules() {
            $this->assertFalse($this->Router->hasModules());
            $modules = array('Module' => '\Module\path');
            $this->assertSame($this->Router, $this->Router->setModules($modules));
            $this->assertTrue($this->Router->hasModules());
        }

        /**
         * Test if trying to overwrite the modules throws an exception.
         * @covers Brickoo\Routing\Router::setModules
         * @covers Brickoo\Core\Exceptions\ValueOverwriteException::__construct
         * @expectedException Brickoo\Core\Exceptions\ValueOverwriteException
         * @depends testGetSetModules
         */
        public function testSetModulesValueOverwriteException($Router) {
            $Router->setModules(array('NewModule', '\NewModule\path'));
        }

        /**
         * Test if the routes filename can be set and the Router reference is returned.
         * Test if the routes filename can be retrieved.
         * @covers Brickoo\Routing\Router::getRoutesFilename
         * @covers Brickoo\Routing\Router::setRoutesFilename
         */
        public function testGetSetRoutesFilename() {
            $this->assertSame($this->Router, $this->Router->setRoutesFilename('routing.php'));
            $this->assertAttributeEquals('routing.php', 'routesFilename', $this->Router);
            $this->assertEquals('routing.php', $this->Router->getRoutesFilename());
        }

        /**
         * Test if the Request instance can be retrieved.
         * @covers Brickoo\Routing\Router::getRequest
         */
        public function testGetRequest() {
            $RequestStub = $this->getRequestStub();
            $Router = new Router($RequestStub);
            $this->assertSame($RequestStub, $Router->getRequest());
            $this->assertAttributeSame($RequestStub, 'Request', $Router);
        }

        /**
         * Test if the RouteCollection instance can be injected and the Router reference is returned.
         * @covers Brickoo\Routing\Router::RouteCollection
         * @covers Brickoo\Routing\Router::getDependency
         */
        public function testInjectRouteCollection() {
            $RouteCollection = $this->getRouteCollectionStub();
            $this->assertSame($this->Router, $this->Router->RouteCollection($RouteCollection));
            $this->assertAttributeContains($RouteCollection, 'dependencies', $this->Router);
        }

        /**
         * Test if the RouteCollection instance can be lazy initialized and retrieved.
         * @covers Brickoo\Routing\Router::RouteCollection
         * @covers Brickoo\Routing\Router::getDependency
         */
        public function testGetRouteCollection() {
            $this->assertInstanceOf
            (
                'Brickoo\Routing\Interfaces\RouteCollection',
                ($RouteCollection = $this->Router->RouteCollection())
            );
            $this->assertAttributeContains($RouteCollection, 'dependencies', $this->Router);
        }

        /**
         * Test if the RouteCollection instance can be retrieved with lazy initialization.
         * @covers Brickoo\Routing\Router::RouteCollection
         * @covers Brickoo\Routing\Router::getDependency
         */
        public function testLazyGetRouteCollection() {
            $this->assertInstanceOf
            (
                'Brickoo\Routing\Interfaces\RouteCollection',
                $this->Router->RouteCollection()
            );
        }

        /**
         * Test if the request route can be set and the Router reference is returned.
         * @covers Brickoo\Routing\Router::setRequestRoute
         */
        public function testSetRequestRoute() {
            $RequestRoute = $this->getMock('Brickoo\Routing\Interfaces\RequestRoute');
            $this->assertSame($this->Router, $this->Router->setRequestRoute($RequestRoute));
            $this->assertAttributeInstanceOf(
                'Brickoo\Routing\Interfaces\RequestRoute',
                'RequestRoute',
                $this->Router
            );

            return $this->Router;
        }

        /**
         * Test if trying to overwrite the value throws an exception.
         * @covers Brickoo\Routing\Router::setRequestRoute
         * @covers Brickoo\Core\Exceptions\ValueOverwriteException::__construct
         * @expectedException Brickoo\Core\Exceptions\ValueOverwriteException
         */
        public function testSetRequestRouteOverwriteException() {
            $RequestRoute = $this->getMock('Brickoo\Routing\Interfaces\RequestRoute');
            $this->Router->setRequestRoute($RequestRoute);
            $this->Router->setRequestRoute($RequestRoute);
        }

        /**
         * Test if the availability of the RequestRoute is recognized.
         * @covers Brickoo\Routing\Router::hasRequestRoute
         */
        public function testHasRequestRoute() {
            $this->assertFalse($this->Router->hasRequestRoute());

            $RequestRoute = $this->getMock('Brickoo\Routing\Interfaces\RequestRoute');
            $this->Router->setRequestRoute($RequestRoute);

            $this->assertTrue($this->Router->hasRequestRoute());
        }

        /**
         * Test if the request route is returned when it is already recognized.
         * @covers Brickoo\Routing\Router::getRequestRoute
         */
        public function testGetRequestRoutePreset() {
            $RequestRoute = $this->getMock('Brickoo\Routing\Interfaces\RequestRoute');
            $this->Router->setRequestRoute($RequestRoute);

            $this->assertSame($RequestRoute, $this->Router->getRequestRoute());
        }

        /**
         * Test if the route can be retrived using the routes.load event.
         * @covers Brickoo\Routing\Router::getRequestRoute
         */
        public function testGetRequestRoute() {
            $RouteStub = $this->getRouteStub();

            $RouteCollectionStub = $this->getRouteCollectionStub();
            $RouteCollectionStub->expects($this->once())
                                ->method('hasRoutes')
                                ->will($this->onConsecutiveCalls(false, true));
            $this->Router->RouteCollection($RouteCollectionStub);


            $RequestStub = $this->getRequestStub();

            $EventManager = $this->getEventManagerStub();
            $EventManager->expects($this->once())
                         ->method('ask')
                         ->will($this->returnValue($RouteCollectionStub));
            $this->Router->EventManager($EventManager);

            $RequestRoute = $this->getMock('Brickoo\Routing\Interfaces\RequestRoute');

            $RouteFinder = $this->getMock('Brickoo\Routing\Interfaces\RouteFinder', array('find'));
            $RouteFinder->expects($this->once())
                        ->method('find')
                        ->will($this->returnValue($RequestRoute));
            $this->Router->RouteFinder($RouteFinder);

            $this->assertSame($RequestRoute, $this->Router->getRequestRoute());
            $this->assertAttributeSame($RequestRoute, 'RequestRoute', $this->Router);
        }

        /**
         * Test if no route matches or is available throws an exception.
         * @covers Brickoo\Routing\Router::getRequestRoute
         * @expectedException \Brickoo\Routing\Exceptions\RequestHasNoRouteException
         */
        public function testGetRequestRouteException() {
            $this->Router->getRequestRoute();
        }

        /**
         * Test if the modules would be loaded with an event.
         * @covers Brickoo\Routing\Router::loadModulesRoutes
         */
        public function testLoadModulesRoutes() {
            $RouteCollection = $this->getRouteCollectionStub();

            $EventManager = $this->getEventManagerStub();
            $EventManager->expects($this->once())
                         ->method('ask')
                         ->will($this->returnValue($RouteCollection));
            $this->Router->EventManager($EventManager);

            $this->assertSame($this->Router, $this->Router->loadModulesRoutes());
            $this->assertSame($RouteCollection, $this->Router->RouteCollection());
        }

        /**
         * Test if the collection would be loaded from filesystem.
         * @covers Brickoo\Routing\Router::loadModulesRoutes
         */
        public function testLoadModulesRoutesByCollection() {
            $expectedController = array(
                'controller'    => '\module\lib\Controller',
                'method'        => 'method',
                'static'        => true
            );
            $this->Router->setModules(array('module', realpath(__DIR__) . '/assets/'));
            $this->Router->collectModulesRoutes();
            $Route = $this->Router->RouteCollection()->getRoute('test');

            $this->assertSame($this->Router, $this->Router->loadModulesRoutes());
            $this->assertEquals('/', $Route->getPath());
            $this->assertEquals($expectedController, $Route->getController());
            $this->assertEquals('GET', $Route->getMethod());
        }

        /**
         * Test if the event manager is called to notify the event.
         * @covers Brickoo\Routing\Router::saveModulesRoutes
         */
        public function testSaveModulesRoutes() {
            $EventManager = $this->getEventManagerStub();
            $EventManager->expects($this->once())
                         ->method('notify')
                         ->will($this->returnValue(null));
            $this->Router->EventManager($EventManager);

            $this->assertSame($this->Router, $this->Router->saveModulesRoutes());
        }

        /**
         * Test if routes can be collected from the modules available.
         * @covers Brickoo\Routing\Router::collectModulesRoutes
         */
        public function testCollectModulesRoutes() {
            $expectedController = array(
                'controller'    => '\module\lib\Controller',
                'method'        => 'method',
                'static'        => true
            );
            $this->Router->setModules(array('module', realpath(__DIR__) . '/assets/'));
            $this->Router->collectModulesRoutes();
            $Route = $this->Router->RouteCollection()->getRoute('test');

            $this->assertNotEmpty(($routes = $this->Router->RouteCollection()->getRoutes()));
            $this->assertEquals('/', $Route->getPath());
            $this->assertEquals($expectedController, $Route->getController());
            $this->assertEquals('GET', $Route->getMethod());
        }

    }
