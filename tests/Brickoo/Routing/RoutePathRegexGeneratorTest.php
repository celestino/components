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

namespace Brickoo\Tests\Routing;

use Brickoo\Routing\RoutePathRegexGenerator,
    PHPUnit_Framework_TestCase;

/**
 * RoutePathRegexGeneratorTest
 *
 * Test suite for the RoutePathRegexGenerator class.
 * @see Brickoo\Routing\RoutePathRegexGenerator
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class RoutePathRegexGeneratorTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Routing\RoutePathRegexGenerator::__construct
     * @covers Brickoo\Routing\RoutePathRegexGenerator::generate
     * @covers Brickoo\Routing\RoutePathRegexGenerator::getRoutePath
     * @covers Brickoo\Routing\RoutePathRegexGenerator::replaceRoutePathWithRulesExpressions
     */
    public function testGeneratePathRegexFromRoute() {
        $expectedRegex = "~^".
                         "/(articles|artikeln)".
                         "/(?<articleName>[\w\-]+)".
                         "(/(?<pageNumber>([0-9]+)?))?".
                         "(?<version>\.[0-9]+)".
                         "(?<format>(\.html|\.json)?)".
                         "$~i";
        $aliases = array("articles" => "artikeln");
        $routePathRegexGenerator = new RoutePathRegexGenerator($aliases);
        $this->assertEquals($expectedRegex, $routePathRegexGenerator->generate($this->getRouteFixture()));
    }

    /**
     * @covers Brickoo\Routing\RoutePathRegexGenerator::generate
     * @covers Brickoo\Routing\RoutePathRegexGenerator::getRoutePath
     * @covers Brickoo\Routing\RoutePathRegexGenerator::replaceRoutePathWithRulesExpressions
     */
    public function testGeneratePathRegexFromRouteWithoutRules() {
        $expectedRegex = "~^/(articles|artikeln)/{articleName}$~i";
        $aliases = array("articles" => "artikeln");

        $route = new \Brickoo\Routing\Route\GenericRoute(
            "articles", "/articles/{articleName}", "MyBlog", "displayArticle"
        );

        $routePathRegexGenerator = new RoutePathRegexGenerator($aliases);
        $this->assertEquals($expectedRegex, $routePathRegexGenerator->generate($route));
    }

    /**
     * Returns a generic route complete configured.
     * @return \Brickoo\Routing\Route\GenericRoute
     */
    private function getRouteFixture() {
        $route = new \Brickoo\Routing\Route\GenericRoute(
            "articles", "/articles/{articleName}/{pageNumber}{version}{format}", "MyBlog", "displayArticle"
        );
        $route->setRules([
            "articleName" => "[\w\-]+",
            "pageNumber" => "[0-9]+",
            "version" => "\.[0-9]+",
            "format" => "\.html|\.json"]
        );
        $route->setDefaultValues(["pageNumber" => 1, "format" => "html"]);
        return $route;
    }

}