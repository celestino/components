<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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

use Brickoo\Component\Routing\Route\HttpRoute;
use PHPUnit_Framework_TestCase;

/**
 * RouteTest
 *
 * Test suite for the HttpRoute class.
 * @see Brickoo\Component\Routing\Route\HttpRoute
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpRouteTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Routing\Route\HttpRoute::__construct
     * @covers Brickoo\Component\Routing\Route\HttpRoute::setMethod
     * @covers Brickoo\Component\Routing\Route\HttpRoute::getMethod
     */
    public function testMethodRoutines() {
        $method = "GET";
        $route = $this->getHttpRouteFixture();
        $this->assertSame($route, $route->setMethod($method));
        $this->assertEquals($method, $route->getMethod());
    }

    /**
     * @covers Brickoo\Component\Routing\Route\HttpRoute::setScheme
     * @covers Brickoo\Component\Routing\Route\HttpRoute::getScheme
     */
    public function testSchemeRoutines() {
        $scheme = "https";
        $route = $this->getHttpRouteFixture();
        $this->assertSame($route, $route->setScheme($scheme));
        $this->assertEquals($scheme, $route->getScheme());
    }

    /**
     * @covers Brickoo\Component\Routing\Route\HttpRoute::setHostname
     * @covers Brickoo\Component\Routing\Route\HttpRoute::getHostname
     */
    public function testGetHostname() {
        $hostname = "example.org";
        $route = $this->getHttpRouteFixture();
        $this->assertSame($route, $route->setHostname($hostname));
        $this->assertEquals($hostname, $route->getHostname());
    }

    /**
     * Returns a http route fixture.
     * @param array $rules
     * @param array $defaultValues
     * @return \Brickoo\Component\Routing\Route\HttpRoute
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
        return $route;
    }

}
