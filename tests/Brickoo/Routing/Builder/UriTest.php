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

    namespace Tests\Brickoo\Routing\Builder;

    use Brickoo\Routing\Builder\Uri;

    /**
     * UriTest
     *
     * Test suite for the Uri Builder class.
     * @see Brickoo\Routing\Builder\Uri
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class UriTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Routing\Builder\Uri::__construct
         */
        public function testConstructor() {
            $Router = $this->getRouterFixture("test.case");
            $location = "http://test-case.localhost:8080";
            $UriBuilder = new Uri($Router, $location);
            $this->assertAttributeEquals($location, "location", $UriBuilder);
            $this->assertAttributeEquals(array(), "pathParameters", $UriBuilder);
            $this->assertAttributeEquals("", "queryString", $UriBuilder);
        }

        /**
         * @covers Brickoo\Routing\Builder\Uri::setPathParameters
         */
        public function testSetPathParamers() {
            $Router = $this->getRouterFixture("test.case");
            $pathParameters = array("article" => "test-case");
            $UriBuilder = new Uri($Router, "http://test-case.localhost:8080");

            $this->assertSame($UriBuilder, $UriBuilder->setPathParameters($pathParameters));
            $this->assertAttributeEquals($pathParameters, "pathParameters", $UriBuilder);
        }

        /**
         * @covers Brickoo\Routing\Builder\Uri::setQueryString
         */
        public function testSetQueryString() {
            $Router = $this->getRouterFixture("test.case");
            $queryString = "order=date_desc";
            $UriBuilder = new Uri($Router, "http://test-case.localhost:8080");

            $this->assertSame($UriBuilder, $UriBuilder->setQueryString($queryString));
            $this->assertAttributeEquals($queryString, "queryString", $UriBuilder);
        }

        /**
         * @covers Brickoo\Routing\Builder\Uri::build
         * @covers Brickoo\Routing\Builder\Uri::getExpectedRoutePath
         * @covers Brickoo\Routing\Builder\Uri::getRegexFromRoute
         * @covers Brickoo\Routing\Builder\Uri::replaceRoutePathWithRulesExpressions
         */
        public function testBuildUri() {
            $routeName = "news.get.articles";
            $pathParameters = array("article" => "test-case");
            $queryString = "order=date_desc";

            $Router = $this->getRouterFixture($routeName);

            $UriBuilder = new Uri($Router, "http://test-case.localhost:8080");
            $CreatedUri = $UriBuilder->setPathParameters($pathParameters)
                                     ->setQueryString($queryString)
                                     ->build($routeName);

            $this->assertInstanceOf('Brickoo\Http\Request\Interfaces\Uri', $CreatedUri);
        }

        /**
         * @covers Brickoo\Routing\Builder\Uri::build
         * @covers Brickoo\Routing\Builder\Exceptions\PathNotValid
         * @expectedException Brickoo\Routing\Builder\Exceptions\PathNotValid
         */
        public function testBuildThrowsPathNotValidException() {
            $routeName = "news.get.articles";
            $pathParameters = array("article" => "111-i-am-not-valid-111");

            $Router = $this->getRouterFixture($routeName);

            $UriBuilder = new Uri($Router, "http://test-case.localhost:8080");
            $CreatedUri = $UriBuilder->setPathParameters($pathParameters)
                                     ->build($routeName);
        }

        /**
         * @covers Brickoo\Routing\Builder\Uri::build
         * @covers Brickoo\Routing\Builder\Uri::getExpectedRoutePath
         * @covers Brickoo\Routing\Builder\Exceptions\RequiredParametersMissing
         * @expectedException Brickoo\Routing\Builder\Exceptions\RequiredParametersMissing
         */
        public function testBuildThrowsRequiredParametersMissingExcpetion() {
            $routeName = "news.get.articles";
            $Router = $this->getRouterFixture($routeName);

            $UriBuilder = new Uri($Router, "http://test-case.localhost:8080");
            $CreatedUri = $UriBuilder->build($routeName);
        }

        /**
         * Returns a router fixture containing a configured route.
         * @param string $routeName the route name of the test case
         * @return Brickoo\Routing\Interfaces\Router
         */
        private function getRouterFixture($routeName) {
            $rulesValuesMap = array(
                array("article", "[a-zA-Z][\w\-]+"),
                array("page", "[0-9]+")
            );

            $defaultValues = array("page" => 1);
            $defaultValuesMap = array(
                array("article", false),
                array("page", true)
            );

            $Route = $this->getMock('Brickoo\Routing\Interfaces\Route');
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

            $Router = $this->getMock('Brickoo\Routing\Interfaces\Router');
            $Router->expects($this->any())
                   ->method("getRoute")
                   ->with($routeName)
                   ->will($this->returnValue($Route));

            return $Router;
        }

    }