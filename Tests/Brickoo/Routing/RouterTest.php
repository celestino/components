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

    use Brickoo\Routing\Router;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * RouterTest
     *
     * Test suite for the Router class.
     * This test suite uses the PHP system temporary directory to store and load the test routes.
     * @see Brickoo\Routing\Router
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RouterTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Returns a Request stub .
         * @return \Brickoo\Core\Interfaces\DynamicInterface
         */
        public function getRequestStub()
        {
            return $this->getMock
            (
                'Brickoo\Core\Interfaces\RequestInterface',
                array('getPath', 'getMethod', 'getHost', 'getProtocol', 'getFormat')
            );
        }

        /**
        * Returns a RouteCollection stub.
        * @return \Brickoo\Routing\Interfaces\RouteCollectionInterface
        */
        public function getRouteCollectionStub()
        {
            return $this->getMock(
                'Brickoo\Routing\Interfaces\RouteCollectionInterface',
                array('getRoutes', 'addRoutes', 'createRoute', 'hasRoutes', 'getRoute', 'hasRoute')
            );
        }

        /**
         * Returns an Aliases stub.
         * @return \Brickoo\Memory\Interfaces\ContainerInterface
         */
        public function getAliasesStub()
        {
            return $this->getMock(
                'Brickoo\Memory\Container',
                array('valid', 'key', 'current', 'next', 'rewind', 'isEmpty')
            );
        }

        /**
        * Returns a Route stub .
        * @return \Brickoo\Routing\Interfaces\RouteInterface
        */
        public function getRouteStub()
        {
            return $this->getMock('Brickoo\Routing\Interfaces\RouteInterface');
        }

        /**
        * Returns an EventManager stub .
        * @return \Brickoo\Event\Manager
        */
        public function getEventManagerStub()
        {
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
        protected function setUp()
        {
            $this->Router = new Router($this->getRequestStub());
        }

        /**
         * Test if the Route instance is created and implements the interface.
         * @covers Brickoo\Routing\Router::__construct
         * @covers Brickoo\Routing\Events
         */
        public function testConstruct()
        {
            $this->assertInstanceOf('Brickoo\Routing\Interfaces\RouterInterface', $this->Router);
        }

        /**
         * Test if the Alias dependency can be injected and the Router reference is returned.
         * @covers Brickoo\Routing\Router::Aliases
         */
        public function testInjectAliases()
        {
            $Aliases = $this->getAliasesStub();
            $this->assertSame($this->Router, $this->Router->Aliases($Aliases));
            $this->assertAttributeContains($Aliases, 'dependencies', $this->Router);
            $this->assertSame($Aliases, $this->Router->Aliases());
        }

        /**
         * Test if the Aliases can be lazy initialized.
         * @covers Brickoo\Routing\Router::Aliases
         */
        public function testAliasesLazyInitialization()
        {
            $this->assertInstanceOf(
                'Brickoo\Memory\Container',
                ($Aliases = $this->Router->Aliases())
            );

            $this->assertAttributeContains($Aliases, 'dependencies', $this->Router);
            $this->assertSame($Aliases, $this->Router->Aliases());
        }

        /**
         * Test if the Alias dependency can be injected and the Router reference is returned.
         * @covers Brickoo\Routing\Router::EventManager
         */
        public function testInjectEventManager()
        {
            $EventManager = $this->getEventManagerStub();
            $this->assertSame($this->Router, $this->Router->EventManager($EventManager));
            $this->assertAttributeContains($EventManager, 'dependencies', $this->Router);
            $this->assertSame($EventManager, $this->Router->EventManager());
        }

        /**
         * Test if the EventManager can be lazy initialized.
         * @covers Brickoo\Routing\Router::EventManager
         */
        public function testEventManagerLazyInitialization()
        {
            $this->assertInstanceOf(
                'Brickoo\Event\Interfaces\ManagerInterface',
                ($EventManager = $this->Router->EventManager())
            );

            $this->assertAttributeContains($EventManager, 'dependencies', $this->Router);
            $this->assertSame($EventManager, $this->Router->EventManager());
        }

        /**
         * Test if the available modules can be set and the Router reference is returned.
         * Test if the modules can be retrieved.
         * @covers Brickoo\Routing\Router::setModules
         * @covers Brickoo\Routing\Router::getModules
         */
        public function testGetSetModules()
        {
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
        public function testHasModules()
        {
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
        public function testSetModulesValueOverwriteException($Router)
        {
            $Router->setModules(array('NewModule', '\NewModule\path'));
        }

        /**
         * Test if the routes filename can be set and the Router reference is returned.
         * Test if the routes filename can be retrieved.
         * @covers Brickoo\Routing\Router::getRoutesFilename
         * @covers Brickoo\Routing\Router::setRoutesFilename
         */
        public function testGetSetRoutesFilename()
        {
            $this->assertSame($this->Router, $this->Router->setRoutesFilename('routing.php'));
            $this->assertAttributeEquals('routing.php', 'routesFilename', $this->Router);
            $this->assertEquals('routing.php', $this->Router->getRoutesFilename());
        }

        /**
         * Test if the Request instance can be retrieved.
         * @covers Brickoo\Routing\Router::getRequest
         */
        public function testGetRequest()
        {
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
        public function testInjectRouteCollection()
        {
            $RouteCollection = $this->getRouteCollectionStub();
            $this->assertSame($this->Router, $this->Router->RouteCollection($RouteCollection));
            $this->assertAttributeContains($RouteCollection, 'dependencies', $this->Router);
        }

        /**
         * Test if the RouteCollection instance can be lazy initialized and retrieved.
         * @covers Brickoo\Routing\Router::RouteCollection
         * @covers Brickoo\Routing\Router::getDependency
         */
        public function testGetRouteCollection()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Routing\Interfaces\RouteCollectionInterface',
                ($RouteCollection = $this->Router->RouteCollection())
            );
            $this->assertAttributeContains($RouteCollection, 'dependencies', $this->Router);
        }

        /**
         * Test if the RouteCollection instance can be retrieved with lazy initialization.
         * @covers Brickoo\Routing\Router::RouteCollection
         * @covers Brickoo\Routing\Router::getDependency
         */
        public function testLazyGetRouteCollection()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Routing\Interfaces\RouteCollectionInterface',
                $this->Router->RouteCollection()
            );
        }

        /**
         * Test if the request route can be set and the Router reference is returned.
         * @covers Brickoo\Routing\Router::setRequestRoute
         */
        public function testSetRequestRoute()
        {
            $Route = $this->getRouteStub();
            $this->assertSame($this->Router, $this->Router->setRequestRoute($Route));
            $this->assertAttributeInstanceOf(
                'Brickoo\Routing\Interfaces\RequestRouteInterface',
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
        public function testSetRequestRouteOverwriteException()
        {
            $Route = $this->getRouteStub();
            $this->Router->setRequestRoute($Route);
            $this->Router->setRequestRoute($Route);
        }

        /**
         * Test if the availability of the RequestRoute is recognized.
         * @covers Brickoo\Routing\Router::hasRequestRoute
         * @depends testSetRequestRoute
         */
        public function testHasRequestRoute($Router)
        {
            $this->assertFalse($this->Router->hasRequestRoute());
            $this->assertTrue($Router->hasRequestRoute());
        }

        /**
         * Test if the Route is recognized as responsible request route.
         * @covers Brickoo\Routing\Router::isRequestRoute
         * @covers Brickoo\Routing\Router::getRegexFromRoutePath
         */
        public function testIsRequestRoute()
        {
            $hasRule = array
            (
                array('name', true),
                array('otherplace', true)
            );

            $getRule = array
            (
                array('name', '[a-z]+'),
                array('otherplace', '.*'),
            );

            $RouteStub = $this->getRouteStub();
            $RouteStub->expects($this->once())
                      ->method('getPath')
                      ->will($this->returnValue('/path/{name}/to/{otherplace}'));
            $RouteStub->expects($this->exactly(2))
                      ->method('hasRule')
                      ->will($this->returnValueMap($hasRule));
            $RouteStub->expects($this->exactly(2))
                      ->method('hasDefaultValue')
                      ->will($this->returnValue(true));
            $RouteStub->expects($this->exactly(2))
                      ->method('getRule')
                      ->will($this->returnValueMap($getRule));
            $RouteStub->expects($this->once())
                      ->method('getMethod')
                      ->will($this->returnValue('GET|HEAD'));
            $RouteStub->expects($this->once())
                      ->method('getHostname')
                      ->will($this->returnValue('([a-z]+\.)?localhost\.com'));

            $RequestStub = $this->Router->getRequest();
            $RequestStub->expects($this->once())
                        ->method('getPath')
                        ->will($this->returnValue('/path/goes/to/home'));
            $RequestStub->expects($this->once())
                        ->method('getMethod')
                        ->will($this->returnValue('HEAD'));
            $RequestStub->expects($this->once())
                        ->method('getHost')
                        ->will($this->returnValue('home.localhost.com'));

            $this->assertTrue($this->Router->isRequestRoute($RouteStub));
        }

        /**
         * Test if the Route is not the responsible request route because of the wrong path.
         * @covers Brickoo\Routing\Router::isRequestRoute
         * @covers Brickoo\Routing\Router::getRegexFromRoutePath
         */
        public function testIsRequestRouteFailure()
        {
            $RouteStub = $this->getRouteStub();
            $RouteStub->expects($this->once())
                      ->method('getPath')
                      ->will($this->returnValue('/path/to/some/place/{notExpected}'));
            $RouteStub->expects($this->once())
                      ->method('hasRule')
                      ->will($this->returnValue(false));

            $RequestStub = $this->Router->getRequest();
            $RequestStub->expects($this->once())
                        ->method('getPath')
                        ->will($this->returnValue('/path/to/some/place'));

            $this->assertFalse($this->Router->isRequestRoute($RouteStub));
        }

        /**
         * Test if the request route is returned when it is already recognized.
         * @covers Brickoo\Routing\Router::getRequestRoute
         * @depends testSetRequestRoute
         */
        public function testGetRequestRoute($Router)
        {
            $this->assertInstanceOf
            (
                'Brickoo\Routing\Interfaces\RequestRouteInterface',
                $Router->getRequestRoute()
            );
        }

        /**
         * Test if the route can be retrived by the route.get event.
         * @covers Brickoo\Routing\Router::getRequestRoute
         */
        public function testGetRequestRouteByGetEvent()
        {
            $Route = $this->getRouteStub();
            $EventManager = $this->getEventManagerStub();
            $EventManager->expects($this->once())
                         ->method('ask')
                         ->will($this->returnValue($Route));

            $this->Router->EventManager($EventManager);
            $this->assertInstanceOf
            (
                'Brickoo\Routing\Interfaces\RequestRouteInterface',
                $this->Router->getRequestRoute()
            );
        }

        /**
         * Test if the route can be retrived using the routes.load event.
         * @covers Brickoo\Routing\Router::getRequestRoute
         * @covers Brickoo\Routing\Router::collectModulesRoutes
         */
        public function testGetRequestRouteByLoadEvent()
        {
            $RouteStub = $this->getRouteStub();
            $RouteStub->expects($this->any())
                      ->method('getPath')
                      ->will($this->returnValue('/path/goes/to/home'));
            $RouteStub->expects($this->any())
                      ->method('getMethod')
                      ->will($this->returnValue('HEAD'));

            $RouteCollectionStub = $this->getRouteCollectionStub();
            $RouteCollectionStub->expects($this->once())
                                ->method('hasRoutes')
                                ->will($this->returnValue(true));
            $RouteCollectionStub->expects($this->once())
                                ->method('getRoutes')
                                ->will($this->returnValue(array($RouteStub)));

            $RequestStub = $this->Router->getRequest();
            $RequestStub->expects($this->once())
                        ->method('getPath')
                        ->will($this->returnValue('/path/goes/to/home'));
            $RequestStub->expects($this->once())
                        ->method('getMethod')
                        ->will($this->returnValue('HEAD'));

            $EventManager = $this->getEventManagerStub();
            $EventManager->expects($this->exactly(2))
                         ->method('ask')
                         ->will($this->onConsecutiveCalls(null, $RouteCollectionStub));

            $this->Router->EventManager($EventManager);

            $this->assertInstanceOf('Brickoo\Routing\Interfaces\RequestRouteInterface', $this->Router->getRequestRoute());
            $this->assertAttributeInstanceOf(
                'Brickoo\Routing\Interfaces\RequestRouteInterface',
                'RequestRoute',
                $this->Router
            );
        }

        /**
         * Test if routes can be collected from the modules available.
         * @covers Brickoo\Routing\Router::collectModulesRoutes
         */
        public function testCollectModulesRoutes()
        {
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

        /**
         * Test if the request route is returned when it is responsible and it is set as property.
         * @covers Brickoo\Routing\Router::getRequestRoute
         * @covers Brickoo\Routing\Router::collectModulesRoutes
         */
        public function testGetRequestRouteResponsible()
        {
            $hasRule = array(
                array('name', true),
                array('otherplace', true)
            );

            $getRule = array(
                array('name', '[a-z]+'),
                array('otherplace', '.*')
            );

            $RouteStub = $this->getRouteStub();
            $RouteStub->expects($this->once())
                      ->method('getPath')
                      ->will($this->returnValue('/path/{name}/to/{otherplace}'));
            $RouteStub->expects($this->exactly(2))
                      ->method('hasRule')
                      ->will($this->returnValueMap($hasRule));
            $RouteStub->expects($this->exactly(2))
                      ->method('hasDefaultValue')
                      ->will($this->returnValue(true));
            $RouteStub->expects($this->exactly(2))
                      ->method('getRule')
                      ->will($this->returnValueMap($getRule));
            $RouteStub->expects($this->once())
                      ->method('getMethod')
                      ->will($this->returnValue('HEAD'));

            $RouteCollectionStub = $this->getRouteCollectionStub();
            $RouteCollectionStub->expects($this->any())
                                ->method('getRoutes')
                                ->will($this->returnValue(array($RouteStub)));
            $RouteCollectionStub->expects($this->once())
                                ->method('hasRoutes')
                                ->will($this->returnValue(false));

            $RequestStub = $this->Router->getRequest();
            $RequestStub->expects($this->once())
                        ->method('getPath')
                        ->will($this->returnValue('/path/goes/to/home'));
            $RequestStub->expects($this->once())
                        ->method('getMethod')
                        ->will($this->returnValue('HEAD'));

            $this->Router->RouteCollection($RouteCollectionStub);

            $this->assertInstanceOf('Brickoo\Routing\Interfaces\RequestRouteInterface', $this->Router->getRequestRoute());
            $this->assertAttributeInstanceOf(
                'Brickoo\Routing\Interfaces\RequestRouteInterface',
                'RequestRoute',
                $this->Router
            );
        }

        /**
         * Test if the request route params are passed to the request route.
         * @covers Brickoo\Routing\Router::getRequestRouteParams
         */
        public function testGetRequestRouteParams()
        {
            $expected = array(
                'name'    => 'brickoo',
                'place'   => 'home',
                'format'  => 'xml'
            );

            $hasRule = array(
                array('name', true),
                array('place', true)
            );

            $getRule = array(
                array('name', '[a-z]+'),
                array('place', '.*')
            );

            $getRules = array('name' => '[a-z]+', 'place' => '.*');

            $hasDefaultValue = array(
                array('name', false),
                array('place', true)
            );

            $getDefaultValue = array(
                array('place', 'home')
            );

            $RouteStub = $this->getRouteStub();
            $RouteStub->expects($this->any())
                      ->method('getPath')
                      ->will($this->returnValue('/path/{name}/{place}'));
            $RouteStub->expects($this->any())
                      ->method('hasRules')
                      ->will($this->returnValue(true));
            $RouteStub->expects($this->any())
                      ->method('hasRule')
                      ->will($this->returnValueMap($hasRule));
            $RouteStub->expects($this->any())
                      ->method('hasDefaultValue')
                      ->will($this->returnValueMap($hasDefaultValue));
            $RouteStub->expects($this->any())
                      ->method('getDefaultValue')
                      ->will($this->returnValueMap($getDefaultValue));
            $RouteStub->expects($this->any())
                      ->method('getRule')
                      ->will($this->returnValueMap($getRule));
            $RouteStub->expects($this->any())
                      ->method('getRules')
                      ->will($this->returnValue($getRules));
            $RouteStub->expects($this->any())
                      ->method('getMethod')
                      ->will($this->returnValue('GET'));
            $RouteStub->expects($this->any())
                      ->method('getFormat')
                      ->will($this->returnValue('xml|json'));


            $RequestStub = $this->Router->getRequest();
            $RequestStub->expects($this->any())
                        ->method('getPath')
                        ->will($this->returnValue('/path/brickoo.xml'));
            $RequestStub->expects($this->any())
                        ->method('getMethod')
                        ->will($this->returnValue('GET'));

            $this->Router->setRequestRoute($RouteStub);
            $this->assertEquals($expected, $this->Router->getRequestRouteParams());
        }

        /**
         * Test if the request format is retrieved from the request url if the
         * Route has no format expected.
         * @covers Brickoo\Routing\Router::getRequestRouteParams
         */
        public function testGetRequestRouteParamsDefaultWithRequestFormat()
        {
            $expected = array('format' => 'xml');

            $RouteStub = $this->getRouteStub();
            $RouteStub->expects($this->any())
                      ->method('getPath')
                      ->will($this->returnValue('/path/to/brickoo'));
            $RouteStub->expects($this->any())
                      ->method('getMethod')
                      ->will($this->returnValue('GET'));
            $RouteStub->expects($this->any())
                      ->method('getFormat')
                      ->will($this->returnValue('xml|json'));

            $RequestStub = $this->Router->getRequest();
            $RequestStub->expects($this->any())
                        ->method('getPath')
                        ->will($this->returnValue('/path/to/brickoo.xml'));
            $RequestStub->expects($this->any())
                        ->method('getMethod')
                        ->will($this->returnValue('GET'));
            $RequestStub->expects($this->any())
                        ->method('getFormat')
                        ->will($this->returnValue('xml'));

            $this->Router->setRequestRoute($RouteStub);
            $this->assertEquals($expected, $this->Router->getRequestRouteParams());
        }

        /**
         * Test if trying to retrieve the params from an not available request route throws an exception.
         * @covers Brickoo\Routing\Router::getRequestRouteParams
         * @covers Brickoo\Routing\Exceptions\RequestHasNoRouteException::__construct
         * @expectedException \Brickoo\Routing\Exceptions\RequestHasNoRouteException
         */
        public function testGetRequestRouteParamsException()
        {
            $this->Router->getRequestRouteParams();
        }

        /**
         * Test if none request route is responsible throws an expection.
         * @covers Brickoo\Routing\Router::getRequestRoute
         * @covers Brickoo\Routing\Exceptions\RequestHasNoRouteException::__construct
         * @expectedException \Brickoo\Routing\Exceptions\RequestHasNoRouteException
         */
        public function testGetRequestRouteNoRouteException()
        {
            $RequestStub = $this->Router->getRequest();
            $RequestStub->expects($this->once())
                        ->method('getPath')
                        ->will($this->returnValue('/path/goes/to/home'));

            $this->Router->RouteCollection($this->getRouteCollectionStub())->getRequestRoute();
        }

        /**
         * Test if the regular expression is returned as expected.
         * @covers Brickoo\Routing\Router::getRegexFromRoutePath
         * @covers Brickoo\Routing\Router::getRegexRouteFormat
         * @covers Brickoo\Routing\Router::getRouteAliasesPath
         */
        public function testGetRegexFromRoutePath()
        {
            $hasRule = array
            (
                array('name', true),
                array('otherplace', true)
            );

            $getRule = array
            (
                array('name', '[a-z]+'),
                array('otherplace', '.*'),
            );

            $RouteStub = $this->getRouteStub();
            $RouteStub->expects($this->once())
                      ->method('getPath')
                      ->will($this->returnValue('/path/{name}/to/{otherplace}/index'));
            $RouteStub->expects($this->exactly(2))
                      ->method('hasRule')
                      ->will($this->returnValueMap($hasRule));
            $RouteStub->expects($this->exactly(2))
                      ->method('hasDefaultValue')
                      ->will($this->returnValue(false));
            $RouteStub->expects($this->exactly(2))
                      ->method('getRule')
                      ->will($this->returnValueMap($getRule));
            $RouteStub->expects($this->once())
                      ->method('getFormat')
                      ->will($this->returnValue('json'));

            $Aliases = $this->getAliasesStub();
            $Aliases->expects($this->exactly(2))
                    ->method('valid')
                    ->will($this->returnValue(true));
            $Aliases->expects($this->exactly(2))
                    ->method('key')
                    ->will($this->onConsecutiveCalls('next', 'path'));
            $Aliases->expects($this->once())
                    ->method('current')
                    ->will($this->returnValue('new_path'));
            $Aliases->expects($this->once())
                    ->method('rewind')
                    ->will($this->returnSelf());
            $Aliases->expects($this->once())
                    ->method('isEmpty')
                    ->will($this->returnValue(false));

            $this->Router->Aliases($Aliases);

            $this->assertEquals
            (
                '~^/(path|new_path)/(?<name>[a-z]+)/to/(?<otherplace>.*)/index(\.(?<__FORMAT__>json))?$~i',
                $this->Router->getRegexFromRoutePath($RouteStub)
            );
        }

    }
