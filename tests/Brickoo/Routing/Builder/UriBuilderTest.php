<?php

    /*
     * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

    namespace Tests\Brickoo\Routing\Builder;

    use Brickoo\Routing\Builder\UriBuilder;

    /**
     * UriBuilderTest
     *
     * Test suite for the Uri Builder class.
     * @see Brickoo\Routing\Builder\UriBuilder
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class UriBuilderTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Routing\Builder\UriBuilder::__construct
         */
        public function testConstructor() {
            $Router = $this->getRouterMock("test.case");
            $baseUrl = "http://test-case.localhost:8080";
            $RegexGenerator = $this->getMock('Brickoo\Routing\Route\Interfaces\RegexGenerator');

            $UriBuilder = new UriBuilder($Router, $baseUrl, $RegexGenerator);
            $this->assertAttributeSame($Router, "Router", $UriBuilder);
            $this->assertAttributeEquals($baseUrl, "baseUrl", $UriBuilder);
            $this->assertAttributeSame($RegexGenerator, "RegexGenerator", $UriBuilder);
        }

        /**
         * @covers Brickoo\Routing\Builder\UriBuilder::__construct
         * @expectedException InvalidArgumentException
         */
        public function testConstructorThrowsInvalidArgumentException() {
            $Router = $this->getRouterMock("test.case");
            $UriBuilder = new UriBuilder($Router, "");
        }

        /**
         * @covers Brickoo\Routing\Builder\UriBuilder::build
         * @covers Brickoo\Routing\Builder\UriBuilder::createUriString
         * @covers Brickoo\Routing\Builder\UriBuilder::getExpectedRoutePath
         * @covers Brickoo\Routing\Builder\UriBuilder::getRegexGenerator
         */
        public function testBuildUriWithCustomRegexGenerator() {
            $routeName = "news.get.articles";
            $pathParameters = array("article" => "test-case");
            $queryString = "order=date_desc";

            $RegexGenerator = $this->getRegexGeneratorMock();
            $Router = $this->getRouterMock($routeName);

            $UriBuilder = new UriBuilder($Router, "http://test-case.localhost:8080", $RegexGenerator);
            $uriString = $UriBuilder->build($routeName, $pathParameters, $queryString);

            $this->assertEquals("http://test-case.localhost:8080/articles/test-case/1?order=date_desc", $uriString);
        }

        /**
         * @covers Brickoo\Routing\Builder\UriBuilder::build
         * @covers Brickoo\Routing\Builder\UriBuilder::createUriString
         * @covers Brickoo\Routing\Builder\UriBuilder::getExpectedRoutePath
         * @covers Brickoo\Routing\Builder\UriBuilder::getRegexGenerator
         */
        public function testBuildUriWithDefaultRegexGenerator() {
            $routeName = "news.get.articles";
            $pathParameters = array("article" => "test-case");
            $queryString = "order=date_desc";

            $Router = $this->getRouterMock($routeName);

            $UriBuilder = new UriBuilder($Router, "http://test-case.localhost:8080");
            $uriString = $UriBuilder->build($routeName, $pathParameters, $queryString);

            $this->assertEquals("http://test-case.localhost:8080/articles/test-case/1?order=date_desc", $uriString);
        }

        /**
         * @covers Brickoo\Routing\Builder\UriBuilder::build
         * @covers Brickoo\Routing\Builder\Exceptions\RouteNotFound
         * @expectedException Brickoo\Routing\Builder\Exceptions\RouteNotFound
         */
        public function testBuildThrowsRouteNotFoundException() {
            $routeName = "news.get.articles";
            $Router = $this->getRouterMock($routeName);

            $UriBuilder = new UriBuilder($Router, "http://test-case.localhost:8080");
            $UriBuilder->build("unknown.route.name", array());
        }

        /**
         * @covers Brickoo\Routing\Builder\UriBuilder::build
         * @covers Brickoo\Routing\Builder\Exceptions\PathNotValid
         * @expectedException Brickoo\Routing\Builder\Exceptions\PathNotValid
         */
        public function testBuildThrowsPathNotValidException() {
            $routeName = "news.get.articles";
            $pathParameters = array("article" => "111-i-am-not-valid-111");

            $Router = $this->getRouterMock($routeName);

            $UriBuilder = new UriBuilder($Router, "http://test-case.localhost:8080");
            $UriBuilder->build($routeName, $pathParameters);
        }

        /**
         * @covers Brickoo\Routing\Builder\UriBuilder::build
         * @covers Brickoo\Routing\Builder\UriBuilder::getExpectedRoutePath
         * @covers Brickoo\Routing\Builder\Exceptions\RequiredParametersMissing
         * @expectedException Brickoo\Routing\Builder\Exceptions\RequiredParametersMissing
         */
        public function testBuildThrowsRequiredParametersMissingException() {
            $routeName = "news.get.articles";
            $Router = $this->getRouterMock($routeName);

            $UriBuilder = new UriBuilder($Router, "http://test-case.localhost:8080");
            $UriBuilder->build($routeName, array());
        }

        /**
         * Returns a router mock object containing a configured route.
         * @param string $routeName the route name of the test case
         * @return Brickoo\Routing\Interfaces\Router
         */
        private function getRouterMock($routeName) {
            $rulesValuesMap = array(
                array("article", "[a-zA-Z][\w\-]+"),
                array("page", "[0-9]+")
            );

            $defaultValues = array("page" => 1);
            $defaultValuesMap = array(
                array("article", false),
                array("page", true)
            );

            $Route = $this->getMock('Brickoo\Routing\Route\Interfaces\Route');
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

            $Router = $this->getMock('Brickoo\Routing\Interfaces\Router');
            $Router->expects($this->any())
                   ->method("hasRoute")
                   ->will($this->returnValueMap($routeValuesMap));
            $Router->expects($this->any())
                   ->method("getRoute")
                   ->with($routeName, null)
                   ->will($this->returnValue($Route));

            return $Router;
        }

        /**
         * Returns a regular route expression generator to validate the path.
         * @return Brickoo\Routing\Route\Interfaces\RegexGenerator
         */
        private function getRegexGeneratorMock() {
            $RegexGenerator = $this->getMock('Brickoo\Routing\Route\Interfaces\RegexGenerator');
            $RegexGenerator->expects($this->once())
                           ->method("generatePathRegex")
                           ->with($this->isInstanceOf('Brickoo\Routing\Route\Interfaces\Route'))
                           ->will($this->returnValue("~^/articles/([a-zA-Z][\w\-]+)/([0-9]+)$~"));

            return $RegexGenerator;
        }

    }