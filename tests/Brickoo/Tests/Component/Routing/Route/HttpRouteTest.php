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

use Brickoo\Component\Routing\Route\HttpRoute,
    PHPUnit_Framework_TestCase;

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
        $hostname = "exmaple.org";
        $route = $this->getHttpRouteFixture();
        $this->assertSame($route, $route->setHostname($hostname));
        $this->assertEquals($hostname, $route->getHostname());
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
        return $route;
    }

}