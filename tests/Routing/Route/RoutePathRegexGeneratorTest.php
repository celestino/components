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

use Brickoo\Component\Routing\Route\RoutePathRegexGenerator;
use Brickoo\Component\Routing\Route\GenericRoute;
use PHPUnit_Framework_TestCase;

/**
 * RoutePathRegexGeneratorTest
 *
 * Test suite for the RoutePathRegexGenerator class.
 * @see Brickoo\Component\Routing\Route\RoutePathRegexGenerator
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class RoutePathRegexGeneratorTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Routing\Route\RoutePathRegexGenerator::__construct
     * @covers Brickoo\Component\Routing\Route\RoutePathRegexGenerator::generate
     * @covers Brickoo\Component\Routing\Route\RoutePathRegexGenerator::getRoutePath
     * @covers Brickoo\Component\Routing\Route\RoutePathRegexGenerator::replaceRoutePathWithRulesExpressions
     * @covers Brickoo\Component\Routing\Route\RoutePathRegexGenerator::replaceRoutePathParameter
     */
    public function testGeneratePathRegexFromRoute() {
        $expectedRegex = "~^".
                         "/(articles|artikeln)".
                         "/(?<articleName>[\\w\\-]+)".
                         "(/(?<pageNumber>([0-9]+)?))?".
                         "(?<version>\\.[0-9]+)".
                         "(?<format>(\\.html|\\.json)?)".
                         "$~i";
        $aliases = array("articles" => "artikeln");
        $routePathRegexGenerator = new RoutePathRegexGenerator($aliases);
        $this->assertEquals($expectedRegex, $routePathRegexGenerator->generate($this->getRouteFixture()));
    }

    /**
     * @covers Brickoo\Component\Routing\Route\RoutePathRegexGenerator::generate
     * @covers Brickoo\Component\Routing\Route\RoutePathRegexGenerator::getRoutePath
     * @covers Brickoo\Component\Routing\Route\RoutePathRegexGenerator::replaceRoutePathWithRulesExpressions
     * @covers Brickoo\Component\Routing\Route\RoutePathRegexGenerator::replaceRoutePathParameter
     */
    public function testGeneratePathRegexFromRouteWithoutRules() {
        $expectedRegex = "~^/(articles|artikeln)/{articleName}$~i";
        $aliases = array("articles" => "artikeln");

        $route = new GenericRoute(
            "articles", "/articles/{articleName}", "MyBlog", "displayArticle"
        );

        $routePathRegexGenerator = new RoutePathRegexGenerator($aliases);
        $this->assertEquals($expectedRegex, $routePathRegexGenerator->generate($route));
    }

    /**
     * Returns a generic route complete configured.
     * @return \Brickoo\Component\Routing\Route\GenericRoute
     */
    private function getRouteFixture() {
        $route = new GenericRoute(
            "articles", "/articles/{articleName}/{pageNumber}{version}{format}", "MyBlog", "displayArticle"
        );
        $route->setRules([
            "articleName" => "[\\w\\-]+",
            "pageNumber" => "[0-9]+",
            "version" => "\\.[0-9]+",
            "format" => "\\.html|\\.json"]
        );
        $route->setDefaultValues(["pageNumber" => 1, "format" => "html"]);
        return $route;
    }

}
