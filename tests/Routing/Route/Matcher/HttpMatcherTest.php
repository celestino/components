<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
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

namespace Brickoo\Tests\Component\Routing\Route\Matcher;

use Brickoo\Component\Routing\Route\HttpRoute;
use Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher;
use PHPUnit_Framework_TestCase;

/**
 * HttpMatcherTest
 *
 * Test suite for the HttpMatcher class.
 * @see Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpMatcherTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::__construct
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::matchesCollection
     */
    public function testMatchesCollectionWithPathAccepted() {
        $pathRegexGenerator = $this->getRegexGeneratorStub();
        $request = $this->getRequestStub();

        $routeCollection = $this->getRouteCollectionStub();
        $routeCollection->expects($this->any())
                        ->method("hasPath")
                        ->will($this->returnValue(true));
        $routeCollection->expects($this->any())
                        ->method("getPath")
                        ->will($this->returnValue("/articles"));

        $routeHttpMatcher = new HttpRouteMatcher($request, $pathRegexGenerator);
        $this->assertTrue($routeHttpMatcher->matchesCollection($routeCollection));
    }

    /** @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::matchesCollection */
    public function testMatchesCollectionWithoutPathAccepted() {
        $pathRegexGenerator = $this->getRegexGeneratorStub();
        $request = $this->getRequestStub();

        $routeCollection = $this->getRouteCollectionStub();
        $routeCollection->expects($this->any())
                        ->method("hasPath")
                        ->will($this->returnValue(false));

        $routeHttpMatcher = new HttpRouteMatcher($request, $pathRegexGenerator);
        $this->assertTrue($routeHttpMatcher->matchesCollection($routeCollection));
    }

    /** @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::matchesCollection */
    public function testMatchesCollectionFails() {
        $pathRegexGenerator = $this->getRegexGeneratorStub();
        $request = $this->getRequestStub();

        $routeCollection = $this->getRouteCollectionStub();
        $routeCollection->expects($this->any())
                        ->method("hasPath")
                        ->will($this->returnValue(true));
        $routeCollection->expects($this->any())
                        ->method("getPath")
                        ->will($this->returnValue("/undefined"));

        $routeHttpMatcher = new HttpRouteMatcher($request, $pathRegexGenerator);
        $this->assertFalse($routeHttpMatcher->matchesCollection($routeCollection));
    }

    /**
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::matchesRoute
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::isAllowedRoute
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::doesPropertyMatch
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::isMatchingRoute
     */
    public function testMatchesRouteCompleteWorkflow() {
        $routeHttpMatcher = new HttpRouteMatcher($this->getRequestStub(), $this->getRegexGeneratorStub());
        $this->assertTrue($routeHttpMatcher->matchesRoute($this->getHttpRouteFixture()));
    }

    /**
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::matchesRoute
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::isAllowedRoute
     */
    public function testMatchesRouteRequestNotAllowed() {
        $routeHttpMatcher = new HttpRouteMatcher($this->getRequestStub("POST"), $this->getRegexGeneratorStub());
        $this->assertFalse($routeHttpMatcher->matchesRoute($this->getHttpRouteFixture()));
    }

    /**
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::getRouteParameters
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::collectRouteParameters
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::getRuleCorrespondingRouteParameter
     */
    public function testGetRouteRulesParametersWithPageAsDefaultValue() {
        $expectedParameters = [
            "name" => "doing_unit-tests",
            "page" => 1
        ];
        $pathRegexGenerator = $this->getRegexGeneratorStub();
        $request = $this->getRequestStub();
        $route = $this->getHttpRouteFixture(["name" => "[\\w\\-]+", "page" => "[0-9]+"], ["page" => 1]);

        $routeHttpMatcher = new HttpRouteMatcher($request, $pathRegexGenerator);
        $this->assertTrue($routeHttpMatcher->matchesRoute($route));
        $this->assertEquals($expectedParameters, $routeHttpMatcher->getRouteParameters());
    }

    /**
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::getRouteParameters
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::collectRouteParameters
     * @covers Brickoo\Component\Routing\Route\Matcher\HttpRouteMatcher::getRuleCorrespondingRouteParameter
     */
    public function testGetRouteRulesParametersWithoutRulesAreEmpty() {
        $expectedParameters = [];
        $pathRegexGenerator = $this->getRegexGeneratorStub();
        $request = $this->getRequestStub();
        $route = $this->getHttpRouteFixture();

        $routeHttpMatcher = new HttpRouteMatcher($request, $pathRegexGenerator);
        $this->assertTrue($routeHttpMatcher->matchesRoute($route));
        $this->assertEquals($expectedParameters, $routeHttpMatcher->getRouteParameters());
    }

    /**
     * Returns a request stub.
     * @param string $httpMethodString
     * @return \Brickoo\Component\Http\HttpRequest
     */
    private function getRequestStub($httpMethodString = "GET") {
        $Uri = $this->getMockBuilder("\\Brickoo\\Component\\Http\\Uri")
            ->disableOriginalConstructor()->getMock();
        $Uri->expects($this->any())
            ->method("getScheme")
            ->will($this->returnValue("https"));
        $Uri->expects($this->any())
            ->method("getHostname")
            ->will($this->returnValue("example.org"));
        $Uri->expects($this->any())
            ->method("getPath")
            ->will($this->returnValue("/articles/doing_unit-tests"));

        $httpMethod = $this->getMockBuilder("\\Brickoo\\Component\\Http\\HttpMethod")
            ->disableOriginalConstructor()->getMock();
        $httpMethod->expects($this->any())
                   ->method("toString")
                   ->will($this->returnValue($httpMethodString));

        $request = $this->getMockBuilder("\\Brickoo\\Component\\Http\\HttpRequest")
            ->disableOriginalConstructor()->getMock();
        $request->expects($this->any())
                ->method("getMethod")
                ->will($this->returnValue($httpMethod));
        $request->expects($this->any())
                ->method("getUri")
                ->will($this->returnValue($Uri));
        return $request;
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
     * Returns a route regex generator stub.
     * @return \Brickoo\Component\Routing\Route\RoutePathRegexGenerator
     */
    private function getRegexGeneratorStub() {
        $generatedRegex = "~^/articles/(?<name>[\w\-]+)(/(?<page>([0-9]+)?))?$~i";
        $pathRegexGenerator = $this->getMock("\\Brickoo\\Component\\Routing\\Route\\RoutePathRegexGenerator");
        $pathRegexGenerator->expects($this->any())
                           ->method("generate")
                           ->will($this->returnValue($generatedRegex));
        return $pathRegexGenerator;
    }

    /**
     * Returns a http route fixture.
     * @param array $rules
     * @param array $defaultValues
     * @return \Brickoo\Component\Routing\Route\GenericRoute
     */
    private function getHttpRouteFixture(array $rules = [], array $defaultValues = []) {
        $route = new HttpRoute(
            "test.route",
            "/article/{name}/{page}",
            "NewspaperController",
            "getArticle"
        );
        $route->setRules($rules);
        $route->setDefaultValues($defaultValues);
        $route->setScheme("https");
        $route->setHostname("example.org");
        $route->setMethod("GET");
        return $route;
    }

}
