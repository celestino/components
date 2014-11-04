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

namespace Brickoo\Tests\Component\Routing\Route;

use Brickoo\Component\Routing\Route\RouteUriBuilder;
use Brickoo\Component\Routing\Route\Exception\RouteNotFoundException;
use PHPUnit_Framework_TestCase;

/**
 * RouteUriBuilderTest
 *
 * Test suite for the Uri Builder class.
 * @see Brickoo\Component\Routing\Route\RouteUriBuilder
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class RouteUriBuilderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Routing\Route\RouteUriBuilder::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorThrowsInvalidArgumentException() {
        new RouteUriBuilder(["wrongType"], $this->getRouterStub(), $this->getRoutePathRegexGeneratorStub());
    }

    /**
     * @covers Brickoo\Component\Routing\Route\RouteUriBuilder::__construct
     * @covers Brickoo\Component\Routing\Route\RouteUriBuilder::build
     * @covers Brickoo\Component\Routing\Route\RouteUriBuilder::createUriString
     * @covers Brickoo\Component\Routing\Route\RouteUriBuilder::getExpectedRoutePath
     */
    public function testBuildUriWithCustomRegexGenerator() {
        $routeName = "news.get.articles";
        $pathParameters = array("article" => "test-case");
        $queryString = "order=date_desc";

        $pathRegexGenerator = $this->getRoutePathRegexGeneratorFixture();
        $router = $this->getRouterFixture($routeName);

        $UriBuilder = new RouteUriBuilder("http://test-case.localhost:8080", $router, $pathRegexGenerator);
        $uriString = $UriBuilder->build($routeName, $pathParameters, $queryString);

        $this->assertEquals("http://test-case.localhost:8080/articles/test-case/1?order=date_desc", $uriString);
    }

    /**
     * @covers Brickoo\Component\Routing\Route\RouteUriBuilder::build
     * @covers Brickoo\Component\Routing\Route\Exception\RouteNotFoundException
     * @expectedException \Brickoo\Component\Routing\Route\Exception\RouteNotFoundException
     */
    public function testBuildThrowsRouteNotFoundException() {
        $routeName = "news.get.articles";
        $router = $this->getRouterStub();
        $router->expects($this->any())
               ->method("getRoute")
               ->with($routeName)
               ->will($this->throwException(new RouteNotFoundException($routeName)));
        $pathRegexGenerator = $this->getRoutePathRegexGeneratorStub();

        $UriBuilder = new RouteUriBuilder("http://test-case.localhost:8080", $router, $pathRegexGenerator);
        $UriBuilder->build($routeName);
    }

    /**
     * @covers Brickoo\Component\Routing\Route\RouteUriBuilder::build
     * @covers Brickoo\Component\Routing\Route\RouteUriBuilder::getExpectedRoutePath
     * @covers Brickoo\Component\Routing\Route\Exception\PathNotValidException
     * @expectedException \Brickoo\Component\Routing\Route\Exception\PathNotValidException
     */
    public function testBuildThrowsPathNotValidException() {
        $routeName = "news.get.articles";
        $pathParameters = array("article" => "111-i-am-not-valid-111");

        $pathRegexGenerator = $this->getRoutePathRegexGeneratorFixture();
        $router = $this->getRouterFixture($routeName);

        $UriBuilder = new RouteUriBuilder("http://test-case.localhost:8080", $router, $pathRegexGenerator);
        $UriBuilder->build($routeName, $pathParameters);
    }

    /**
     * @covers Brickoo\Component\Routing\Route\RouteUriBuilder::build
     * @covers Brickoo\Component\Routing\Route\RouteUriBuilder::getExpectedRoutePath
     * @covers Brickoo\Component\Routing\Route\Exception\RouteRequiredParametersMissingException
     * @expectedException \Brickoo\Component\Routing\Route\Exception\RouteRequiredParametersMissingException
     */
    public function testBuildThrowsRequiredParametersMissingException() {
        $routeName = "news.get.articles";

        $pathRegexGenerator = $this->getRoutePathRegexGeneratorFixture();
        $router = $this->getRouterFixture($routeName);

        $UriBuilder = new RouteUriBuilder("http://test-case.localhost:8080", $router, $pathRegexGenerator);
        $UriBuilder->build($routeName);
    }

    /**
     * Returns a router mock object containing a configured route.
     * @param string $routeName the route name of the test case
     * @return \Brickoo\Component\Routing\Router
     */
    private function getRouterFixture($routeName) {
        $rulesValuesMap = array(
            array("article", "[\\w\\-]+"),
            array("page", "[0-9]+")
        );

        $defaultValues = array("page" => 1);
        $defaultValuesMap = array(
            array("article", false),
            array("page", true)
        );

        $Route = $this->getMock("\\Brickoo\\Component\\Routing\\Route\\Route");
        $Route->expects($this->any())
              ->method("getName")
              ->will($this->returnValue($routeName));
        $Route->expects($this->any())
              ->method("getPath")
              ->will($this->returnValue("/articles/{article}/{page}"));
        $Route->expects($this->any())
              ->method("hasRule")
              ->will($this->returnValue(true));
        $Route->expects($this->any())
              ->method("getRule")
              ->will($this->returnValueMap($rulesValuesMap));
        $Route->expects($this->any())
              ->method("hasDefaultValue")
              ->will($this->returnValueMap($defaultValuesMap));
        $Route->expects($this->any())
              ->method("getDefaultValues")
              ->will($this->returnValue($defaultValues));

        $routeValuesMap = array(
            array($routeName, null, true),
            array("unknown.route.name", null, false)
        );

        $router = $this->getRouterStub();
        $router->expects($this->any())
               ->method("hasRoute")
               ->will($this->returnValueMap($routeValuesMap));
        $router->expects($this->any())
               ->method("getRoute")
               ->with($routeName, null)
               ->will($this->returnValue($Route));

        return $router;
    }

    /**
     * Returns a regular route expression generator to validate the path.
     * @return \Brickoo\Component\Routing\Route\RoutePathRegexGenerator
     */
    private function getRoutePathRegexGeneratorFixture() {
        $pathRegexGenerator = $this->getRoutePathRegexGeneratorStub();
        $pathRegexGenerator->expects($this->any())
                       ->method("generate")
                       ->with($this->isInstanceOf("\\Brickoo\\Component\\Routing\\Route\\Route"))
                       ->will($this->returnValue("~^/articles/([a-zA-Z][\\w\\-]+)/([0-9]+)$~"));

        return $pathRegexGenerator;
    }

    /**
     * Returns a route path regex generator stub.
     * @return \Brickoo\Component\Routing\Route\RoutePathRegexGenerator
     */
    private function getRoutePathRegexGeneratorStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Routing\\Route\\RoutePathRegexGenerator")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a router stub.
     * @return \Brickoo\Component\Routing\Router
     */
    private function getRouterStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Routing\\Router")
            ->disableOriginalConstructor()
            ->getMock();
    }

}
