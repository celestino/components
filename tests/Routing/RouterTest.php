<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Tests\Component\Routing;

use ArrayIterator;
use Brickoo\Component\Routing\Router;
use PHPUnit_Framework_TestCase;

/**
 * RouterTest
 *
 * Test suite for the Router class.
 * @see Brickoo\Component\Routing\Router
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class RouterTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Routing\Router::__construct
     * @covers Brickoo\Component\Routing\Router::getRoute
     * @covers Brickoo\Component\Routing\Router::getRouteCollectorIterator
     */
    public function testGetRouteWithoutCollectionName() {
        $route = $this->getRouteStub();

        $routeCollection = $this->getRouteCollectionStub();
        $routeCollection->expects($this->any())
                        ->method("hasRoute")
                        ->with("unit.test.route")
                        ->will($this->returnValue(true));
        $routeCollection->expects($this->any())
                        ->method("getRoute")
                        ->with("unit.test.route")
                        ->will($this->returnValue($route));

        $router = new Router($this->getRouteCollectorStub($routeCollection), $this->getRouteMatcherMock());
        $this->assertSame($route, $router->getRoute("unit.test.route"));
    }

    /**
     * @covers Brickoo\Component\Routing\Router::getRoute
     * @covers Brickoo\Component\Routing\Router::getRouteCollectorIterator
     * @covers Brickoo\Component\Routing\Router::isCollectionResponsible
     */
    public function testGetRouteWithCollectionName() {
        $collectionName = "unit-test-collection";

        $route = $this->getRouteStub();

        $routeCollection = $this->getRouteCollectionStub();
        $routeCollection->expects($this->any())
                        ->method("hasName")
                        ->will($this->returnValue(true));
        $routeCollection->expects($this->any())
                        ->method("getName")
                        ->will($this->returnValue($collectionName));
        $routeCollection->expects($this->any())
                        ->method("hasRoute")
                        ->with("unit.test.route")
                        ->will($this->returnValue(true));
        $routeCollection->expects($this->any())
                        ->method("getRoute")
                        ->with("unit.test.route")
                        ->will($this->returnValue($route));

        $router = new Router($this->getRouteCollectorStub($routeCollection), $this->getRouteMatcherMock());
        $this->assertSame($route, $router->getRoute("unit.test.route", $collectionName));
    }

    /**
     * @covers Brickoo\Component\Routing\Router::getRoute
     * @covers Brickoo\Component\Routing\Router::getRouteCollectorIterator
     * @covers Brickoo\Component\Routing\Exception\RouteNotFoundException
     * @expectedException \Brickoo\Component\Routing\Exception\RouteNotFoundException
     */
    public function testGetRouteThrowsRouteNotFoundException() {
        $routeCollection = $this->getRouteCollectionStub();
        $routeCollection->expects($this->any())
                        ->method("hasRoute")
                        ->with("unit.test.route")
                        ->will($this->returnValue(false));

        $router = new Router($this->getRouteCollectorStub($routeCollection), $this->getRouteMatcherMock());
        $router->getRoute("unit.test.route");
    }

    /**
     * @covers Brickoo\Component\Routing\Router::getRoute
     * @expectedException \InvalidArgumentException
     */
    public function testGetRouteThrowsInvalidArgumentException() {
        $router = new Router($this->getRouteCollectorStub(), $this->getRouteMatcherMock());
        $router->getRoute(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Routing\Router::hasRoute
     * @covers Brickoo\Component\Routing\Router::getRoute
     */
    public function testHasRouteWithoutCollectionName() {
        $route = $this->getRouteStub();

        $routeCollection = $this->getRouteCollectionStub();
        $routeCollection->expects($this->any())
                        ->method("hasRoute")
                        ->will($this->onConsecutiveCalls(true, false));
        $routeCollection->expects($this->any())
                        ->method("getRoute")
                        ->will($this->returnValue($route));

        $router = new Router($this->getRouteCollectorStub($routeCollection), $this->getRouteMatcherMock());
        $this->assertTrue($router->hasRoute("unit.test.route"));
        $this->assertFalse($router->hasRoute("route.does.not.exist"));
    }

    /**
     * @covers Brickoo\Component\Routing\Router::hasRoute
     * @covers Brickoo\Component\Routing\Router::getRoute
     */
    public function testHasRouteWithCollectionName() {
        $collectionName = "unit-test-collection";

        $route = $this->getRouteStub();

        $routeCollection = $this->getRouteCollectionStub();
        $routeCollection->expects($this->any())
                        ->method("hasName")
                        ->will($this->returnValue(true));
        $routeCollection->expects($this->any())
                        ->method("getName")
                        ->will($this->returnValue($collectionName));
        $routeCollection->expects($this->any())
                        ->method("hasRoute")
                        ->will($this->onConsecutiveCalls(true, false));
        $routeCollection->expects($this->any())
                        ->method("getRoute")
                        ->with("unit.test.route")
                        ->will($this->returnValue($route));

        $router = new Router($this->getRouteCollectorStub($routeCollection), $this->getRouteMatcherMock());
        $this->assertTrue($router->hasRoute("unit.test.route", $collectionName));
        $this->assertFalse($router->hasRoute("route.does.not.exist", $collectionName));
    }

    /**
     * @covers Brickoo\Component\Routing\Router::HasRoute
     * @expectedException \InvalidArgumentException
     */
    public function testHasRouteThrowsInvalidArgumentException() {
        $router = new Router($this->getRouteCollectorStub(), $this->getRouteMatcherMock());
        $router->hasRoute(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Routing\Router::getRequestRoute
     * @covers Brickoo\Component\Routing\Router::getMatchingRoute
     * @covers Brickoo\Component\Routing\Router::getMatchingRouteFromCollection
     */
    public function testGetRequestRoute() {
        $route = $this->getRouteStub();

        $routeCollection = $this->getRouteCollectionStub();
        $routeCollection->expects($this->any())
                        ->method("hasPath")
                        ->will($this->returnValue(false));
        $routeCollection->expects($this->any())
                        ->method("getIterator")
                        ->will($this->returnValue(new \ArrayIterator(array($route))));

        $router = new Router($this->getRouteCollectorStub($routeCollection), $this->getRouteMatcherMock($route));
        $this->assertInstanceOf("\\Brickoo\\Component\\Routing\\Route\\RequestRoute", $router->getRequestRoute());
    }

    /**
     * @covers Brickoo\Component\Routing\Router::getRequestRoute
     * @covers Brickoo\Component\Routing\Router::getMatchingRoute
     * @covers Brickoo\Component\Routing\Router::getMatchingRouteFromCollection
     * @covers Brickoo\Component\Routing\Exception\NoMatchingRouteFoundException
     * @expectedException \Brickoo\Component\Routing\Exception\NoMatchingRouteFoundException
     */
    public function testGetExecutableThrowsNoMatchingRouteFoundException() {
        $routeCollection = $this->getRouteCollectionStub();
        $routeCollection->expects($this->any())
                        ->method("getIterator")
                        ->will($this->returnValue(new \ArrayIterator()));

        $router = new Router($this->getRouteCollectorStub($routeCollection), $this->getRouteMatcherMock());
        $router->getRequestRoute();
    }

    /**
     * Returns a route stub.
     * @return \Brickoo\Component\Routing\Route\Route
     */
    private function getRouteStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Routing\\Route\\Route")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a route collection stub.
     * @return \Brickoo\Component\Routing\Route\RouteCollection
     */
    private function getRouteCollectionStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Routing\\Route\\RouteCollection")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Stub creator for the route collector dependency.
     * @param \Brickoo\Component\Routing\Route\RouteCollection $routeCollection
     * @return \Brickoo\Component\Routing\Route\Collector\RouteCollector
     */
    private function getRouteCollectorStub($routeCollection = null) {
        $routeCollector = $this->getMock("\\Brickoo\\Component\\Routing\\Route\\Collector\\RouteCollector");
        $routeCollector->expects($this->any())
                       ->method("collect")
                       ->will($this->returnValue($routeCollector));
        $routeCollector->expects($this->any())
                       ->method("getIterator")
                       ->will($this->returnValue(new ArrayIterator(array($routeCollection))));
        return $routeCollector;
    }

    /**
     * Mock creator for the route matcher dependency.
     * @param \Brickoo\Component\Routing\Route\Route $route
     * @return \Brickoo\Component\Routing\Route\Matcher\RouteMatcher
     */
    private function getRouteMatcherMock($route = null) {
        $routeMatcher = $this->getMock("\\Brickoo\\Component\\Routing\\Route\\Matcher\\RouteMatcher");
        $routeMatcher->expects($this->any())
                     ->method("matchesCollection")
                     ->will($this->returnValue(true));
        $routeMatcher->expects($this->any())
                     ->method("matchesRoute")
                     ->with($route)
                     ->will($this->returnValue(true));
        $routeMatcher->expects($this->any())
                     ->method("getRouteParameters")
                     ->will($this->returnValue(array("key" => "value")));
        return $routeMatcher;
    }

}
