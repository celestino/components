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

use Brickoo\Component\Routing\Route\RouteCollection;
use PHPUnit_Framework_TestCase;

/**
 * RouteCollectionTest
 *
 * Test suite for the route RouteCollection class.
 * @see Brickoo\Component\Routing\Route\RouteCollection
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class RouteCollectionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Routing\Route\RouteCollection::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidNameThrowsArgumentException() {
        new RouteCollection(["wrongType"], "/some/path");
    }

    /**
     * @covers Brickoo\Component\Routing\Route\RouteCollection::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidPathThrowsArgumentException() {
        new RouteCollection("test.collection", ["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Routing\Route\RouteCollection::__construct
     * @covers Brickoo\Component\Routing\Route\RouteCollection::getName
     */
    public function testGetRouteCollectionName() {
        $name = "test.collection";
        $routeCollection = new RouteCollection($name, "/articles/test-collection");
        $this->assertEquals($name, $routeCollection->getName());
    }

    /** @covers Brickoo\Component\Routing\Route\RouteCollection::hasName */
    public function testHasRouteCollectionName() {
        $routeCollection = new RouteCollection();
        $this->assertFalse($routeCollection->hasName());

        $routeCollection = new RouteCollection("some-name");
        $this->assertTrue($routeCollection->hasName());
    }

    /** @covers Brickoo\Component\Routing\Route\RouteCollection::getPath */
    public function testGetRouteCollectionCommonPath() {
        $path = "/articles/test-collection";
        $routeCollection = new RouteCollection("test.collection", $path);
        $this->assertEquals($path, $routeCollection->getPath());
    }

    /** @covers Brickoo\Component\Routing\Route\RouteCollection::hasPath */
    public function testHasRouteCollectionPath() {
        $routeCollection = new RouteCollection();
        $this->assertFalse($routeCollection->hasPath());

        $routeCollection = new RouteCollection("articles", "/articles/lists");
        $this->assertTrue($routeCollection->hasPath());
    }

    /** @covers Brickoo\Component\Routing\Route\RouteCollection::addRoutes */
    public function testAddingRoutes() {
        $route = $this->getRouteStub();
        $route->expects($this->any())
              ->method("getName")
              ->will($this->returnValue("test.route"));

        $expectedRoutes = array("test.route" => $route);
        $routeCollection = new RouteCollection("name", "/path");
        $this->assertSame($routeCollection, $routeCollection->addRoutes($expectedRoutes));
        $this->assertAttributeEquals($expectedRoutes, "routes", $routeCollection);
    }

    /**
     * @covers Brickoo\Component\Routing\Route\RouteCollection::addRoutes
     * @covers Brickoo\Component\Routing\Route\Exception\DuplicateRouteException
     * @expectedException \Brickoo\Component\Routing\Route\Exception\DuplicateRouteException
     */
    public function testAddingDuplicatedRoutesThrowsAnException() {
        $route = $this->getRouteStub();
        $route->expects($this->any())
              ->method("getName")
              ->will($this->returnValue("test.route"));

        $routeCollection = new RouteCollection("name", "/path");
        $routeCollection->addRoutes(array($route));
        $routeCollection->addRoutes(array($route));
    }

    /** @covers Brickoo\Component\Routing\Route\RouteCollection::getRoutes */
    public function testGetRoutes() {
        $route = $this->getRouteStub();
        $route->expects($this->any())
              ->method("getName")
              ->will($this->returnValue("test.route"));
        $expectedRoutes = array(
            "test.route" => $route
        );
        $routeCollection = new RouteCollection("name", "/path");
        $routeCollection->addRoutes($expectedRoutes);
        $this->assertAttributeSame($expectedRoutes, "routes", $routeCollection);
        $this->assertEquals($expectedRoutes, $routeCollection->getRoutes());
    }

    /** @covers Brickoo\Component\Routing\Route\RouteCollection::hasRoutes */
    public function testHasRoutes(){
        $routeCollection = new RouteCollection("name", "/path");
        $this->assertFalse($routeCollection->hasRoutes());

        $route = $this->getRouteStub();
        $route->expects($this->any())
              ->method("getName")
              ->will($this->returnValue("test.route"));

        $routeCollection = new RouteCollection("name", "/path");
        $routeCollection->addRoutes(array("test.route" => $route));
        $this->assertTrue($routeCollection->hasRoutes());
    }

    /** @covers Brickoo\Component\Routing\Route\RouteCollection::getRoute */
    public function testGetRoute(){
        $expectedRoute = $this->getRouteStub();
        $expectedRoute->expects($this->any())
                      ->method("getName")
                      ->will($this->returnValue("test.route"));

        $routeCollection = new RouteCollection("name", "/path");
        $routeCollection->addRoutes(array("test.route" => $expectedRoute));
        $this->assertSame($expectedRoute, $routeCollection->getRoute("test.route"));
    }

    /**
     * @covers Brickoo\Component\Routing\Route\RouteCollection::getRoute
     * @expectedException \InvalidArgumentException
     */
    public function testGetRouteThrowsInvalidArgumentException() {
        $routeCollection = new RouteCollection("name", "/path");
        $routeCollection->getRoute(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Routing\Route\RouteCollection::getRoute
     * @covers Brickoo\Component\Routing\Route\Exception\RouteNotFoundException
     * @expectedException \Brickoo\Component\Routing\Route\Exception\RouteNotFoundException
     */
    public function testGetRouteThrowsRouteNotFoundException() {
        $routeCollection = new RouteCollection("name", "/path");
        $routeCollection->getRoute("not.available.fail");
    }

    /** @covers Brickoo\Component\Routing\Route\RouteCollection::hasRoute */
    public function testHasRoute(){
        $routeCollection = new RouteCollection("name", "/path");
        $this->assertFalse($routeCollection->hasRoute("test.route"));
    }

    /**
     * @covers Brickoo\Component\Routing\Route\RouteCollection::hasRoute
     * @expectedException \InvalidArgumentException
     */
    public function testHasRouteThrowsInvalidArgumentException() {
        $routeCollection = new RouteCollection("name", "/path");
        $routeCollection->hasRoute(array("wrongType"));
    }

    /** @covers Brickoo\Component\Routing\Route\RouteCollection::getIterator */
    public function testGetIterator() {
        $route = $this->getRouteStub();
        $route->expects($this->any())
              ->method("getName")
              ->will($this->returnValue("test.route"));

        $routeCollection = new RouteCollection("name", "/path");
        $routeCollection->addRoutes([$route]);
        $iterator = $routeCollection->getIterator();
        $this->assertInstanceOf("Traversable", $iterator);
        $iterator->rewind();
        $this->assertSame($route, $iterator->current());
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
