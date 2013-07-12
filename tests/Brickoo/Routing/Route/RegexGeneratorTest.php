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

    namespace Tests\Brickoo\Routing\Route;

    use Brickoo\Routing\Route\RegexGenerator;

    /**
     * RegexGeneratorTest
     *
     * Test suite for the RegexGenerator class.
     * @see Brickoo\Routing\Route\RegexGenerator
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RegexGeneratorTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Routing\Route\RegexGenerator::__construct
         */
        public function testConstructor() {
            $aliases = array("articles" => "test-case");
            $RegexGenerator = new RegexGenerator($aliases);
            $this->assertInstanceOf('Brickoo\Routing\Route\Interfaces\RegexGenerator', $RegexGenerator);
            $this->assertAttributeEquals($aliases, "aliases", $RegexGenerator);
        }

        /**
         * @covers Brickoo\Routing\Route\RegexGenerator::generatePathRegex
         * @covers Brickoo\Routing\Route\RegexGenerator::getRoutePath
         * @covers Brickoo\Routing\Route\RegexGenerator::replaceRoutePathWithRulesExpressions
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
            $RegexGenerator = new RegexGenerator($aliases);
            $this->assertEquals($expectedRegex, $RegexGenerator->generatePathRegex($this->getRouteFixture()));
        }

        /**
         * @covers Brickoo\Routing\Route\RegexGenerator::generatePathRegex
         * @covers Brickoo\Routing\Route\RegexGenerator::getRoutePath
         * @covers Brickoo\Routing\Route\RegexGenerator::replaceRoutePathWithRulesExpressions
         */
        public function testGeneratePathRegexFromRouteWithoutRules() {
            $expectedRegex = "~^/(articles|artikeln)/{articleName}$~i";
            $aliases = array("articles" => "artikeln");

            $Route = new \Brickoo\Routing\Route\Route(
                "articles", "/articles/{articleName}", "MyBlog", "displayArticle"
            );

            $RegexGenerator = new RegexGenerator($aliases);
            $this->assertEquals($expectedRegex, $RegexGenerator->generatePathRegex($Route));
        }

        /**
         * Returns a route complete configured fixture.
         * @return \Brickoo\Routing\Route\Interfaces\Route
         */
        private function getRouteFixture() {
            return new \Brickoo\Routing\Route\Route(
                "articles", "/articles/{articleName}/{pageNumber}{version}{format}", "MyBlog", "displayArticle",
                array("articleName" => "[\w\-]+", "pageNumber" => "[0-9]+", "version" => "\.[0-9]+", "format" => "\.html|\.json"),
                array("pageNumber" => 1, "format" => "html")
            );
        }

    }