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

namespace Brickoo\Tests\Routing\Route;

use Brickoo\Routing\RouteCollection,
    PHPUnit_Framework_TestCase;

/**
 * RouteCollectionTest
 *
 * Test suite for the route RouteCollection class.
 * @see Brickoo\Routing\RouteCollection
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class RouteCollectionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Routing\RouteCollection::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidNameThrowsArgumentException() {
        new RouteCollection(["wrongType"], "/some/path");
    }

    /**
     * @covers Brickoo\Routing\RouteCollection::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidPathThrowsArgumentException() {
        new RouteCollection("test.collection", ["wrongType"]);
    }

    /**
     * @covers Brickoo\Routing\RouteCollection::__construct
     * @covers Brickoo\Routing\RouteCollection::getName
     */
    public function testGetRouteCollectionName() {
        $name = "test.collection";
        $routeCollection = new RouteCollection($name, "/articles/test-collection");
        $this->assertEquals($name, $routeCollection->getName());
    }

    /** @covers Brickoo\Routing\RouteCollection::hasName */
    public function testHasRouteCollectionName() {
        $routeCollection = new RouteCollection();
        $this->assertFalse($routeCollection->hasName());

        $routeCollection = new RouteCollection("some-name");
        $this->assertTrue($routeCollection->hasName());
    }

    /** @covers Brickoo\Routing\RouteCollection::getPath */
    public function testGetRouteCollectionCommonPath() {
        $path = "/articles/test-collection";
        $routeCollection = new RouteCollection("test.collection", $path);
        $this->assertEquals($path, $routeCollection->getPath());
    }

    /** @covers Brickoo\Routing\RouteCollection::hasPath */
    public function testHasRouteCollectionPath() {
        $routeCollection = new RouteCollection();
        $this->assertFalse($routeCollection->hasPath());

        $routeCollection = new RouteCollection("articles", "/articles/lists");
        $this->assertTrue($routeCollection->hasPath());
    }

    /** @covers Brickoo\Routing\RouteCollection::addRoutes */
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
     * @covers Brickoo\Routing\RouteCollection::addRoutes
     * @covers Brickoo\Routing\Exception\DuplicateRouteException
     * @expectedException Brickoo\Routing\Exception\DuplicateRouteException
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

    /** @covers Brickoo\Routing\RouteCollection::getRoutes */
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

    /** @covers Brickoo\Routing\RouteCollection::hasRoutes */
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

    /** @covers Brickoo\Routing\RouteCollection::getRoute */
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
     * @covers Brickoo\Routing\RouteCollection::getRoute
     * @expectedException InvalidArgumentException
     */
    public function testGetRouteThrowsInvalidArgumentException() {
        $routeCollection = new RouteCollection("name", "/path");
        $routeCollection->getRoute(["wrongType"]);
    }

    /**
     * @covers Brickoo\Routing\RouteCollection::getRoute
     * @covers Brickoo\Routing\Exception\RouteNotFoundException
     * @expectedException Brickoo\Routing\Exception\RouteNotFoundException
     */
    public function testGetRouteThrowsRouteNotFoundException() {
        $routeCollection = new RouteCollection("name", "/path");
        $routeCollection->getRoute("not.available.fail");
    }

    /** @covers Brickoo\Routing\RouteCollection::hasRoute */
    public function testHasRoute(){
        $routeCollection = new RouteCollection("name", "/path");
        $this->assertFalse($routeCollection->hasRoute("test.route"));
    }

    /**
     * @covers Brickoo\Routing\RouteCollection::hasRoute
     * @expectedException InvalidArgumentException
     */
    public function testHasRouteThrowsInvalidArgumentException() {
        $routeCollection = new RouteCollection("name", "/path");
        $routeCollection->hasRoute(array("wrongType"));
    }

    /** @covers Brickoo\Routing\RouteCollection::getIterator */
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
     * @return \Brickoo\Routing\Route
     */
    private function getRouteStub() {
        return $this->getMockBuilder("\\Brickoo\\Routing\\Route")
            ->disableOriginalConstructor()
            ->getMock();
    }

}