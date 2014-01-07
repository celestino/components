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

    namespace Tests\Brickoo\Routing\Matcher;

    use Brickoo\Routing\Matcher\HttpRouteMatcher;

    /**
     * HttpMatcherTest
     *
     * Test suite for the Matcher class.
     * @see Brickoo\Routing\Matcher\HttpMatcher
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class HttpMatcherTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Routing\Matcher\HttpMatcher::__construct
         */
        public function testConstructor() {
            $Request = $this->getRequestStub();
            $RegexGenerator = $this->getRegexGeneratorStub();

            $RouteMatcher = new HttpRouteMatcher($Request, $RegexGenerator);
            $this->assertInstanceOf('Brickoo\Routing\Matcher\Interfaces\Matcher',$RouteMatcher);
            $this->assertAttributeSame($Request, "Request", $RouteMatcher);
            $this->assertAttributeSame($RegexGenerator, "RegexGenerator", $RouteMatcher);
        }

        /**
         * @covers Brickoo\Routing\Matcher\HttpMatcher::matchesCollection
         */
        public function testMatchesCollectionWithPath() {
            $RegexGenerator = $this->getRegexGeneratorStub();
            $Request = $this->getRequestStub();

            $RouteCollection = $this->getMock("Brickoo\Routing\Route\Interfaces\Collection");
            $RouteCollection->expects($this->any())
                            ->method("hasPath")
                            ->will($this->returnValue(true));
            $RouteCollection->expects($this->any())
                            ->method("getPath")
                            ->will($this->returnValue("/articles"));

            $RouteMatcher = new HttpRouteMatcher($Request, $RegexGenerator);
            $this->assertTrue($RouteMatcher->matchesCollection($RouteCollection));
        }

        /**
         * @covers Brickoo\Routing\Matcher\HttpMatcher::matchesCollection
         */
        public function testMatchesCollectionWithoutPath() {
            $RegexGenerator = $this->getRegexGeneratorStub();
            $Request = $this->getRequestStub();

            $RouteCollection = $this->getMock("Brickoo\Routing\Route\Interfaces\Collection");
            $RouteCollection->expects($this->any())
                            ->method("hasPath")
                            ->will($this->returnValue(false));

            $RouteMatcher = new HttpRouteMatcher($Request, $RegexGenerator);
            $this->assertTrue($RouteMatcher->matchesCollection($RouteCollection));
        }

        /**
         * @covers Brickoo\Routing\Matcher\HttpMatcher::matchesCollection
         */
        public function testMatchesCollectionFails() {
            $RegexGenerator = $this->getRegexGeneratorStub();
            $Request = $this->getRequestStub();

            $RouteCollection = $this->getMock("Brickoo\Routing\Route\Interfaces\Collection");
            $RouteCollection->expects($this->any())
                            ->method("hasPath")
                            ->will($this->returnValue(true));
            $RouteCollection->expects($this->any())
                            ->method("getPath")
                            ->will($this->returnValue("/undefined"));

            $RouteMatcher = new HttpRouteMatcher($Request, $RegexGenerator);
            $this->assertFalse($RouteMatcher->matchesCollection($RouteCollection));
        }

        /**
         * @covers Brickoo\Routing\Matcher\HttpMatcher::matchesRoute
         * @covers Brickoo\Routing\Matcher\HttpMatcher::isAllowedRoute
         * @covers Brickoo\Routing\Matcher\HttpMatcher::isMatchingRoute
         */
        public function testMatchesRouteCompleteWorkflow() {
            $Request = $this->getRequestStub();
            $RegexGenerator = $this->getRegexGeneratorStub();
            $Route = $this->getRouteFixture();

            $RouteMatcher = new HttpRouteMatcher($Request, $RegexGenerator);
            $this->assertTrue($RouteMatcher->matchesRoute($Route));
        }

        /**
         * @covers Brickoo\Routing\Matcher\HttpMatcher::matchesRoute
         * @covers Brickoo\Routing\Matcher\HttpMatcher::isAllowedRoute
         */
        public function testMatchesRouteRequestNotAllowed() {
            $RegexGenerator = $this->getRegexGeneratorStub();
            $Request = $this->getMock('Brickoo\Http\Interfaces\Request');
            $Request->expects($this->any())
                    ->method("getMethod")
                    ->will($this->returnValue('POST'));

            $Route = $this->getRestrictedRouteFixture();

            $RouteMatcher = new HttpRouteMatcher($Request, $RegexGenerator);
            $this->assertFalse($RouteMatcher->matchesRoute($Route));
        }

        /**
         * @covers Brickoo\Routing\Matcher\HttpMatcher::getRouteParameters
         * @covers Brickoo\Routing\Matcher\HttpMatcher::collectRouteParameters
         */
        public function testGetRouteRulesParameters() {
            $expectedParameters = array(
                "articleName" => "doing_unit-tests",
                "pageNumber" => 1
            );
            $RegexGenerator = $this->getRegexGeneratorStub();
            $Request = $this->getRequestStub();
            $Route = $this->getRouteFixture();

            $RouteMatcher = new HttpRouteMatcher($Request, $RegexGenerator);
            $this->assertTrue($RouteMatcher->matchesRoute($Route));
            $this->assertEquals($expectedParameters, $RouteMatcher->getRouteParameters());
        }

        /**
         * Returns a request stub.
         * @return \Brickoo\Http\Interfaces\Request
         */
        private function getRequestStub() {
            $Uri = $this->getMock('Brickoo\Http\Request\Interfaces\Uri');
            $Uri->expects($this->any())
                ->method("getHostname")
                ->will($this->returnValue("localhost"));
            $Uri->expects($this->any())
                ->method("getScheme")
                ->will($this->returnValue("https"));
            $Uri->expects($this->any())
                ->method('getPath')
                ->will($this->returnValue("/articles/doing_unit-tests"));

            $Request = $this->getMock('Brickoo\Http\Interfaces\Request');
            $Request->expects($this->any())
                    ->method('getMethod')
                    ->will($this->returnValue('GET'));
            $Request->expects($this->any())
                    ->method('getUri')
                    ->will($this->returnValue($Uri));

            return $Request;
        }

        /**
         * Returns a route regex generator stub.
         * @return \Brickoo\Routing\Route\Interfaces\RegexGenerator
         */
        private function getRegexGeneratorStub() {
            $generatedRegex = "~^/articles/(?<articleName>[\w\-]+)(/(?<pageNumber>([0-9]+)?))?$~i";
            $RegexGenerator = $this->getMock('Brickoo\Routing\Route\Interfaces\RegexGenerator');
            $RegexGenerator->expects($this->any())
                           ->method("generatePathRegex")
                           ->will($this->returnValue($generatedRegex));

            return $RegexGenerator;
        }

        /**
         * Returns a route complete configured fixture.
         * @return \Brickoo\Routing\Route\Interfaces\HttpRoute
         */
        private function getRouteFixture() {
            return new \Brickoo\Routing\Route\HttpRoute(
                "articles", "/articles/{articleName}/{pageNumber}", "MyBlog", "displayArticle",
                array("articleName" => "[\w\-]+", "pageNumber" => "[0-9]+"), array("pageNumber" => 1)
            );
        }

        /**
         * Returns a restricted to GET requests route fixture.
         * @return \Brickoo\Routing\Route\Interfaces\HttpRoute
         */
        private function getRestrictedRouteFixture() {
            return new \Brickoo\Routing\Route\HttpRoute(
                "articles", "/articles/{articleName}/{pageNumber}", "MyBlog", "displayArticle",
                array("articleName" => "[\w\-]+", "pageNumber" => "[0-9]+"), array("pageNumber" => 1),
                "GET"
            );
        }

    }