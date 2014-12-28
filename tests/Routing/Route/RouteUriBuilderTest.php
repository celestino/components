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
