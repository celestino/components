<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
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

use Brickoo\Component\Routing\Route\Collector\CallbackRouteCollector;
use Brickoo\Component\Routing\Route\GenericRoute;
use Brickoo\Component\Routing\Route\Matcher\BasicRouteMatcher;
use Brickoo\Component\Routing\Route\RouteCollection;
use Brickoo\Component\Routing\Route\RoutePathRegexGenerator;
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
     * @covers Brickoo\Component\Routing\Router::isCollectionResponsible
     */
    public function testGetRoute() {
        $route = new GenericRoute("unit.test.route", "/", "Controller");

        $routeCollector = new CallbackRouteCollector(
            function() use ($route) {
                $routeCollection = new RouteCollection("unit-test-collection");
                $routeCollection->addRoutes([$route]);
                return [$routeCollection];
            }
        );

        $router = new Router($routeCollector, new BasicRouteMatcher("/", new RoutePathRegexGenerator()));
        $this->assertSame($route, $router->getRoute("unit.test.route"));
    }

    /**
     * @covers Brickoo\Component\Routing\Router::getRoute
     * @covers Brickoo\Component\Routing\Router::getRouteCollectorIterator
     * @covers Brickoo\Component\Routing\Exception\RouteNotFoundException
     * @expectedException \Brickoo\Component\Routing\Exception\RouteNotFoundException
     */
    public function testTryingGetNotAvailableRouteThrowsRouteNotFoundException() {
        $router = new Router(
            new CallbackRouteCollector(function(){}),
            new BasicRouteMatcher("/", new RoutePathRegexGenerator())
        );
        $router->getRoute("unit.test.route");
    }

    /**
     * @covers Brickoo\Component\Routing\Router::hasRoute
     * @covers Brickoo\Component\Routing\Router::getRoute
     */
    public function testCheckIfRouteIsAvailable() {
        $route = new GenericRoute("unit.test.route", "/", "Controller");

        $routeCollector = new CallbackRouteCollector(
            function() use ($route) {
                $routeCollection = new RouteCollection("unit-test-collection");
                $routeCollection->addRoutes([$route]);
                return [$routeCollection];
            }
        );

        $router = new Router($routeCollector, new BasicRouteMatcher("/", new RoutePathRegexGenerator()));
        $this->assertTrue($router->hasRoute("unit.test.route"));
        $this->assertFalse($router->hasRoute("route.does.not.exist"));
    }

    /**
     * @covers Brickoo\Component\Routing\Router::getRequestRoute
     * @covers Brickoo\Component\Routing\Router::getMatchingRoute
     * @covers Brickoo\Component\Routing\Router::getMatchingRouteFromCollection
     */
    public function testGetRequestRoute() {
        $route = new GenericRoute("unit.test.route", "/test", "Controller");

        $routeCollector = new CallbackRouteCollector(
            function() use ($route) {
                $routeCollection = new RouteCollection("unit-test-collection");
                $routeCollection->addRoutes([$route]);
                return [$routeCollection];
            }
        );

        $router = new Router($routeCollector, new BasicRouteMatcher("/test", new RoutePathRegexGenerator()));
        $requestRoute = $router->getRequestRoute();
        $this->assertSame($route, $requestRoute->getRoute());
    }

    /**
     * @covers Brickoo\Component\Routing\Router::getRequestRoute
     * @covers Brickoo\Component\Routing\Router::getMatchingRoute
     * @covers Brickoo\Component\Routing\Router::getMatchingRouteFromCollection
     * @covers Brickoo\Component\Routing\Exception\NoMatchingRouteFoundException
     * @expectedException \Brickoo\Component\Routing\Exception\NoMatchingRouteFoundException
     */
    public function testTryingToGetNotAvailableRequestMAtchingThrowsException() {
        $router = new Router(
            new CallbackRouteCollector(function(){}),
            new BasicRouteMatcher("/", new RoutePathRegexGenerator())
        );
        $router->getRequestRoute();
    }

}
