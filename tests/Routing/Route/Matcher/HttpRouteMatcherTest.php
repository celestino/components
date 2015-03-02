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

namespace Brickoo\Tests\Component\Routing\Route\Matcher;

use Brickoo\Component\Http\HttpMethod;
use Brickoo\Component\Http\HttpRequestBuilderDirector;
use Brickoo\Component\Routing\Route\HttpRoute;
use Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher;
use Brickoo\Component\Routing\Route\RouteCollection;
use Brickoo\Component\Routing\Route\RoutePathRegexGenerator;
use PHPUnit_Framework_TestCase;

/**
 * HttpRouteMatcherTest
 *
 * Test suite for the HttpRouteMatcher class.
 * @see Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpRouteMatcherTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::__construct
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::matchesCollection
     */
    public function testRequestPathMatchesCollection() {
        $routeHttpMatcher = new HttpRouteMatcher(
            $this->getRequestFixture("/test"),
            new RoutePathRegexGenerator()
        );

        $this->assertTrue($routeHttpMatcher->matchesCollection(new RouteCollection()));
        $this->assertTrue($routeHttpMatcher->matchesCollection(new RouteCollection("","/test")));
        $this->assertFalse($routeHttpMatcher->matchesCollection(new RouteCollection("","/abc")));
    }

    /**
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::matchesRoute
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::isAllowedRoute
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::doesPropertyMatch
     * @covers Brickoo\Component\Routing\Route\Matcher\CommonRouteMatcherStructure::isMatchingRoute
     */
    public function testRequestPathMatchesRoute() {
        $routeHttpMatcher = new HttpRouteMatcher(
            $this->getRequestFixture("/test"),
            new RoutePathRegexGenerator()
        );
        $this->assertTrue($routeHttpMatcher->matchesRoute(new HttpRoute("test.route", "/test", "ControllerPath")));
        $this->assertFalse($routeHttpMatcher->matchesRoute(new HttpRoute("test.route.failure", "/", "ControllerPath")));
    }

    /**
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::matchesRoute
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::isAllowedRoute
     */
    public function testRouteIsNotAllowed() {
        $routeHttpMatcher = new HttpRouteMatcher(
            $this->getRequestFixture("/test"),
            new RoutePathRegexGenerator()
        );
        $route = new HttpRoute("test.route", "/test", "ControllerPath");
        $route->setMethod(HttpMethod::POST);
        $this->assertFalse($routeHttpMatcher->matchesRoute($route));
    }

    /**
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::getRouteParameters
     * @covers Brickoo\Component\Routing\Route\Matcher\CommonRouteMatcherStructure::collectRouteParameters
     * @covers Brickoo\Component\Routing\Route\Matcher\CommonRouteMatcherStructure::getRuleCorrespondingRouteParameter
     */
    public function testGetRouteParametersWithPageAsDefaultValue() {
        $expectedParameters = [
            "name" => "doing-unit-tests",
            "page" => 1
        ];
        $routeHttpMatcher = new HttpRouteMatcher(
            $this->getRequestFixture("/doing-unit-tests"),
            new RoutePathRegexGenerator()
        );
        $route = new HttpRoute("test.route", "/{name}/{page}", "ControllerPath");
        $route->setRules([
            "name" => "[\\w\\-]+",
            "page" => "[0-9]+"
        ]);
        $route->setDefaultValues(["page" => 1]);
        $this->assertTrue($routeHttpMatcher->matchesRoute($route));
        $this->assertEquals($expectedParameters, $routeHttpMatcher->getRouteParameters());
    }

    /**
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::getRouteParameters
     * @covers Brickoo\Component\Routing\Route\Matcher\CommonRouteMatcherStructure::collectRouteParameters
     * @covers Brickoo\Component\Routing\Route\Matcher\CommonRouteMatcherStructure::getRuleCorrespondingRouteParameter
     */
    public function testGetRouteRulesParametersWithoutRulesAreEmpty() {
        $expectedParameters = [];
        $routeHttpMatcher = new HttpRouteMatcher(
            $this->getRequestFixture("/doing-unit-tests"),
            new RoutePathRegexGenerator()
        );
        $route = new HttpRoute("test.route", "/doing-unit-tests", "ControllerPath");
        $this->assertTrue($routeHttpMatcher->matchesRoute($route));
        $this->assertEquals($expectedParameters, $routeHttpMatcher->getRouteParameters());
    }

    /**
     * Return a http request fixture.
     * @param string $requestPath
     * @return \Brickoo\Component\Http\HttpRequest
     */
    private function getRequestFixture($requestPath) {
        return (new HttpRequestBuilderDirector())->build(["REQUEST_URI" => $requestPath]);
    }

}
