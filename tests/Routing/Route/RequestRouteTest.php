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

use Brickoo\Component\Routing\Route\RequestRoute;
use PHPUnit_Framework_TestCase;

/**
 * RequestRouteTest
 *
 * Test suite for the RequestRoute class.
 * @see Brickoo\Component\Routing\Route\RequestRoute
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class RequestRouteTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Routing\Route\RequestRoute::__construct
     * @covers Brickoo\Component\Routing\Route\RequestRoute::getRoute
     */
    public function testGetRoute() {
        $route = $this->getRouteStub();
        $executableRoute = new RequestRoute($route);
        $this->assertSame($route, $executableRoute->getRoute());
    }

    /** @covers Brickoo\Component\Routing\Route\RequestRoute::getParameters */
    public function testGetParameters() {
        $expectedParameters = ["param" => "the parameter value"];

        $executableRoute = new RequestRoute($this->getRouteStub(), $expectedParameters);
        $this->assertEquals($expectedParameters, $executableRoute->getParameters());
    }

    /** @covers Brickoo\Component\Routing\Route\RequestRoute::getParameter */
    public function testGetParameter() {
        $parameters = ["param" => "the parameter value"];

        $executableRoute = new RequestRoute($this->getRouteStub(), $parameters);
        $this->assertEquals($parameters["param"], $executableRoute->getParameter("param"));
    }

    /**
     * @covers Brickoo\Component\Routing\Route\RequestRoute::getParameter
     * @expectedException \InvalidArgumentException
     */
    public function testGetParameterThrowsInvalidArgumentException() {
        $executableRoute = new RequestRoute($this->getRouteStub());
        $executableRoute->getParameter(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Routing\Route\RequestRoute::getParameter
     * @covers Brickoo\Component\Routing\Route\Exception\ParameterNotAvailableException
     * @expectedException \Brickoo\Component\Routing\Route\Exception\ParameterNotAvailableException
     */
    public function testGetParameterThrowsParameterNotAvailableException() {
        $executableRoute = new RequestRoute($this->getRouteStub());
        $executableRoute->getParameter("not.available");
    }

    /** @covers Brickoo\Component\Routing\Route\RequestRoute::hasParameter */
    public function testHasParameter() {
        $parameters = ["param" => "the parameter value"];

        $executableRoute = new RequestRoute($this->getRouteStub(), $parameters);
        $this->assertFalse($executableRoute->hasParameter("not.available"));
        $this->assertTrue($executableRoute->hasParameter("param"));
    }

    /**
     * @covers Brickoo\Component\Routing\Route\RequestRoute::hasParameter
     * @expectedException \InvalidArgumentException
     */
    public function testHasParameterThrowsInvalidArgumentException() {
        $executableRoute = new RequestRoute($this->getRouteStub());
        $executableRoute->hasParameter(["wrongType"]);
    }

    /**
     * Returns a route stub.
     * @return \Brickoo\Component\Routing\Route\Route
     */
    private function getRouteStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Routing\\Route\\Route")
           ->disableOriginalConstructor()
            ->getMock();
    }

}
