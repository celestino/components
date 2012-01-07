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

    use Brickoo\Library\Routing\Router;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * RouterTest
     *
     * Test suite for the Router class.
     * @see Brickoo\Library\Routing\Router
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RouterTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Returns a Request stub .
         * @return objetc Request stub implementing the Brickoo\Library\Core\Interfaces\DynamicInterface
         */
        public function getRequestStub()
        {
            return $this->getMock
            (
                'Brickoo\Library\Core\Interfaces\DynamicRequestInterface',
                array('getRequestPath', 'getRequestMethod')
            );
        }

        /**
        * Returns a RouteCollection stub.
        * @return object RouteCollection stub
        */
        public function getRouteCollectionStub()
        {
            return $this->getMock
            (
                'Brickoo\Library\Routing\RouteCollection',
                 array('getIterator')
            );
        }

        /**
        * Returns a Route stub with the methods passed.
        * @param array $methods the methods to mock
        * @return object Route stub
        */
        public function getRouteStub()
        {
            return $this->getMock
            (
                'Brickoo\Library\Routing\Route',
                array('getPath', 'getMethod', 'hasRule','hasDefaultValue', 'getRule', 'getDefaultValue')
            );
        }

        /**
         * Holds an instance of the Router class.
         * @var Brickoo\Library\Routing\Router
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
         * @covers Brickoo\Library\Routing\Router::__construct
         */
        public function testConstruct()
        {
            $this->assertInstanceOf('Brickoo\Library\Routing\Interfaces\RouterInterface', $this->Router);
        }

        /**
         * Test if the Request instance can be retrieved.
         * @covers Brickoo\Library\Routing\Router::getRequest
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
         * @covers Brickoo\Library\Routing\Router::injectRouteCollection
         */
        public function testInjectRouteCollection()
        {
            $RouteCollection = $this->getRouteCollectionStub();
            $this->assertSame($this->Router, $this->Router->injectRouteCollection($RouteCollection));
            $this->assertAttributeSame($RouteCollection, 'RouteCollection', $this->Router);

            return $this->Router;
        }

        /**
         * Test if trying to overwrite the dependecy throws an exception.
         * @covers Brickoo\Library\Routing\Router::injectRouteCollection
         * @covers Brickoo\Library\Core\Exceptions\DependencyOverwriteException::__construct
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         */
        public function testInjectRouteCollectionOverwriteException()
        {
            $RouteCollection = $this->getRouteCollectionStub();
            $this->Router->injectRouteCollection($RouteCollection);
            $this->Router->injectRouteCollection($RouteCollection);
        }

        /**
         * Test if the RouteCollection instance can be retrieved.
         * @covers Brickoo\Library\Routing\Router::getRouteCollection
         * @depends testInjectRouteCollection
         */
        public function testGetRouteCollection($Router)
        {
            $this->assertInstanceOf('Brickoo\Library\Routing\RouteCollection', $Router->getRouteCollection());
        }

        /**
         * Test if the RouteCollection instance can be retrieved with lazy initialization.
         * @covers Brickoo\Library\Routing\Router::getRouteCollection
         */
        public function testGetRouteLazyCollection()
        {
            $this->assertInstanceOf('Brickoo\Library\Routing\RouteCollection', $this->Router->getRouteCollection());
        }

        /**
         * Test if the request route can be set and the Router reference is returned.
         * @covers Brickoo\Library\Routing\Router::setRequestRoute
         */
        public function testSetRequestRoute()
        {
            $Route = $this->getRouteStub();
            $this->assertSame($this->Router, $this->Router->setRequestRoute($Route));
            $this->assertAttributeSame($Route, 'RequestRoute', $this->Router);

            return $this->Router;
        }

        /**
         * Test if trying to overwrite the value throws an exception.
         * @covers Brickoo\Library\Routing\Router::setRequestRoute
         * @covers Brickoo\Library\Core\Exceptions\ValueOverwriteException::__construct
         * @expectedException Brickoo\Library\Core\Exceptions\ValueOverwriteException
         */
        public function testSetRequestRouteOverwriteException()
        {
            $Route = $this->getRouteStub();
            $this->Router->setRequestRoute($Route);
            $this->Router->setRequestRoute($Route);
        }

        /**
         * Test if the availability of the RequestRoute is recognized.
         * @covers Brickoo\Library\Routing\Router::hasRequestRoute
         * @depends testSetRequestRoute
         */
        public function testHasRequestRoute($Router)
        {
            $this->assertTrue($Router->hasRequestRoute());
        }

        /**
         * Test if the Route is recognized as responsible request route.
         * @covers Brickoo\Library\Routing\Router::isRequestRoute
         * @covers Brickoo\Library\Routing\Router::getRegexFromPath
         * @covers Brickoo\Library\Routing\Router::getRegexFromMethod
         */
        public function testIsRequestRoute()
        {
            $valueMap = array
            (
                array('name', true),
                array('otherplace', false)
            );

            $RouteStub = $this->getRouteStub();
            $RouteStub->expects($this->exactly(2))
                      ->method('getPath')
                      ->will($this->returnValue('/path/{name}/to/{otherplace}'));
            $RouteStub->expects($this->once())
                      ->method('getMethod')
                      ->will($this->returnValue('GET|POST'));
            $RouteStub->expects($this->exactly(2))
                      ->method('hasRule')
                      ->will($this->returnValueMap($valueMap));
            $RouteStub->expects($this->once())
                      ->method('hasDefaultValue')
                      ->will($this->returnValue(true));
            $RouteStub->expects($this->once())
                      ->method('getRule')
                      ->will($this->returnValue('[a-z]+'));

            $RequestStub = $this->Router->getRequest();
            $RequestStub->expects($this->once())
                        ->method('getRequestPath')
                        ->will($this->returnValue('/path/goes/to/home'));
            $RequestStub->expects($this->once())
                        ->method('getRequestMethod')
                        ->will($this->returnValue('POST'));

            $this->assertTrue($this->Router->isRequestRoute($RouteStub));
        }

        /**
         * Test if the Route is not the responsible request route because of the wrong path.
         * @covers Brickoo\Library\Routing\Router::isRequestRoute
         * @covers Brickoo\Library\Routing\Router::getRegexFromPath
         * @covers Brickoo\Library\Routing\Router::getRegexFromMethod
         */
        public function testIsRequestRouteFailure()
        {
            $RouteStub = $this->getRouteStub();
            $RouteStub->expects($this->exactly(2))
                      ->method('getPath')
                      ->will($this->returnValue('/path/to/some/place/{notExpected}'));
            $RouteStub->expects($this->once())
                      ->method('hasRule')
                      ->will($this->returnValue(false));
            $RouteStub->expects($this->once())
                      ->method('hasDefaultValue')
                     ->will($this->returnValue(false));

            $RequestStub = $this->Router->getRequest();
            $RequestStub->expects($this->once())
                        ->method('getRequestPath')
                        ->will($this->returnValue('/path/to/some/place'));

            $this->assertFalse($this->Router->isRequestRoute($RouteStub));
        }

        /**
         * Test if the request route is returned when it is already recognized.
         * @covers Brickoo\Library\Routing\Router::getRequestRoute
         * @depends testSetRequestRoute
         */
        public function testGetRequestRoute($Router)
        {
            $this->assertInstanceOf('Brickoo\Library\Routing\Route', $Router->getRequestRoute());
        }

        /**
         * Test if the request route does not implement the ArrayIterator interface throws an exception.
         * @covers Brickoo\Library\Routing\Router::getRequestRoute
         * @expectedException UnexpectedValueException
         */
        public function testGetRequestRouteValueException()
        {
            $RouteCollectionStub = $this->getRouteCollectionStub();
            $RouteCollectionStub->expects($this->once())
                                ->method('getIterator')
                                ->will($this->returnValue(new stdClass()));

            $this->Router->injectRouteCollection($RouteCollectionStub);

            $this->Router->getRequestRoute();
        }

        /**
         * Test if the request route is returned when it is responsible and it is set as property.
         * @covers Brickoo\Library\Routing\Router::getRequestRoute
         */
        public function testGetRequestRouteResponsible()
        {
            $valueMap = array
            (
                array('name', true),
                array('otherplace', false)
            );

            $RouteStub = $this->getRouteStub();
            $RouteStub->expects($this->exactly(2))
                      ->method('getPath')
                      ->will($this->returnValue('/path/{name}/to/{otherplace}'));
            $RouteStub->expects($this->once())
                      ->method('getMethod')
                      ->will($this->returnValue('GET|POST'));
            $RouteStub->expects($this->exactly(2))
                      ->method('hasRule')
                      ->will($this->returnValueMap($valueMap));
            $RouteStub->expects($this->once())
                      ->method('hasDefaultValue')
                      ->will($this->returnValue(true));
            $RouteStub->expects($this->once())
                      ->method('getRule')
                      ->will($this->returnValue('[a-z]+'));

            $RouteCollectionStub = $this->getRouteCollectionStub();
            $RouteCollectionStub->expects($this->once())
                                ->method('getIterator')
                                ->will($this->returnValue(new ArrayIterator(array($RouteStub))));

            $RequestStub = $this->Router->getRequest();
            $RequestStub->expects($this->once())
                        ->method('getRequestPath')
                        ->will($this->returnValue('/path/goes/to/home'));
            $RequestStub->expects($this->once())
                        ->method('getRequestMethod')
                        ->will($this->returnValue('POST'));

            $this->Router->injectRouteCollection($RouteCollectionStub);

            $this->assertSame($RouteStub, $this->Router->getRequestRoute());
            $this->assertAttributeSame($RouteStub, 'RequestRoute', $this->Router);
        }

        /**
         * Test if none request route is responsible throws an expection.
         * @covers Brickoo\Library\Routing\Router::getRequestRoute
         * @covers Brickoo\Library\Routing\Exceptions\RequestHasNoRouteException::__construct
         * @expectedException \Brickoo\Library\Routing\Exceptions\RequestHasNoRouteException
         */
        public function testGetRequestRouteNoRouteException()
        {
            $RouteCollectionStub = $this->getRouteCollectionStub();
            $RouteCollectionStub->expects($this->once())
                                ->method('getIterator')
                                ->will($this->returnValue(new ArrayIterator(array())));

            $RequestStub = $this->Router->getRequest();
            $RequestStub->expects($this->once())
                        ->method('getRequestPath')
                        ->will($this->returnValue('/path/goes/to/home'));

            $this->Router->injectRouteCollection($RouteCollectionStub);

            $this->Router->getRequestRoute();
        }

        /**
         * Test if the class properties are reseted and the Route refernce is returned.
         * @covers Brickoo\Library\Routing\Router::reset
         */
        public function testReset()
        {
            $this->assertSame($this->Router, $this->Router->reset());
        }

        /**
         * Test if the regular expression is returned as expected.
         * @covers Brickoo\Library\Routing\Router::getRegexFromMethod
         */
        public function testGetRegexFromMethod()
        {
            $RouteStub = $this->getRouteStub();
            $RouteStub->expects($this->once())
                      ->method('getMethod')
                      ->will($this->returnValue('GET|POST'));

            $this->assertEquals
            (
                '~^(GET|POST)$~i',
                $this->Router->getRegexFromMethod($RouteStub)
            );
        }

        /**
         * Test if the regular expression is returned as expected.
         * @covers Brickoo\Library\Routing\Router::getRegexFromPath
         */
        public function testGetRegexFromPath()
        {
            $valueMap = array
            (
                array('name', true),
                array('otherplace', false)
            );

            $RouteStub = $this->getRouteStub();
            $RouteStub->expects($this->exactly(2))
                      ->method('getPath')
                      ->will($this->returnValue('/path/{name}/to/{otherplace}'));
            $RouteStub->expects($this->exactly(2))
                      ->method('hasRule')
                      ->will($this->returnValueMap($valueMap));
            $RouteStub->expects($this->once())
                      ->method('hasDefaultValue')
                      ->will($this->returnValue(true));
            $RouteStub->expects($this->once())
                      ->method('getRule')
                      ->will($this->returnValue('[a-z]+'));

            $this->assertEquals
            (
                '~^/path/(?<name>[a-z]+)/to/([^/]+)?.*$~i',
                $this->Router->getRegexFromPath($RouteStub)
            );
        }

    }

?>
