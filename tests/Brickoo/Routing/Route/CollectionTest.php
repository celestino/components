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

    namespace Tests\Brickoo\Routing\Route;

    use Brickoo\Routing\Route\RouteCollection;

    /**
     * CollectionTest
     *
     * Test suite for the route Collection class.
     * @see Brickoo\Routing\Route\Collection
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class CollectionTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Routing\Route\Collection::__construct
         */
        public function testConstructor() {
            $name = "test.collection";
            $path = "/articles/test-collection";
            $Collection = new RouteCollection($name, $path);
            $this->assertInstanceOf(
                'Brickoo\Routing\Route\Interfaces\Collection', $Collection
            );
            $this->assertAttributeEquals($name, 'name', $Collection);
            $this->assertAttributeEquals($path, 'path', $Collection);
            $this->assertAttributeEquals(array(), 'routes', $Collection);
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::__construct
         * @expectedException \InvalidArgumentException
         */
        public function testConstructorInvalidNameThrowsArgumentException() {
            $Collection = new RouteCollection(array("wrongType"), "/some/path");
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::__construct
         * @expectedException \InvalidArgumentException
         */
        public function testConstructorInvalidPathThrowsArgumentException() {
            $Collection = new RouteCollection("test.collection", array("wrongType"));
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::getName
         */
        public function testGetCollectionName() {
            $name = "test.collection";
            $Collection = new RouteCollection($name, "/articles/test-collection");
            $this->assertEquals($name, $Collection->getName());
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::getName
         * @expectedException \UnexpectedValueException
         */
        public function testGetCollectionNameThrowsValueException() {
            $Collection = new RouteCollection();
            $Collection->getName();
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::hasName
         */
        public function testHasCollectionName() {
            $Collection = new RouteCollection();
            $this->assertFalse($Collection->hasName());

            $Collection = new RouteCollection("some-name");
            $this->assertTrue($Collection->hasName());
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::getPath
         */
        public function testGetCollectionCommonPath() {
            $path = "/articles/test-collection";
            $Collection = new RouteCollection("test.collection", $path);
            $this->assertEquals($path, $Collection->getPath());
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::getPath
         * @expectedException \UnexpectedValueException
         */
        public function testGetCollectionPathThrowsValueException() {
            $Collection = new RouteCollection();
            $Collection->getPath();
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::hasPath
         */
        public function testHasCollectionPath() {
            $Collection = new RouteCollection();
            $this->assertFalse($Collection->hasPath());

            $Collection = new RouteCollection(null, "/articles/lists");
            $this->assertTrue($Collection->hasPath());
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::addRoutes
         */
        public function testAddingRoutes() {
            $Route = $this->getMock('Brickoo\Routing\Route\Interfaces\Route');
            $Route->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue('test.route'));

            $expectedRoutes = array('test.route' => $Route);
            $Collection = new RouteCollection("name", "/path");
            $this->assertSame($Collection, $Collection->addRoutes($expectedRoutes));
            $this->assertAttributeEquals($expectedRoutes, 'routes', $Collection);
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::addRoutes
         * @covers Brickoo\Routing\Route\Exceptions\DuplicateRoute
         * @expectedException Brickoo\Routing\Route\Exceptions\DuplicateRoute
         */
        public function testAddingDuplicatedRoutesThrowsAnException() {
            $Route = $this->getMock('Brickoo\Routing\Route\Interfaces\Route');
            $Route->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue('test.route'));

            $Collection = new RouteCollection("name", "/path", array('test.route' => $Route));
            $Collection->addRoutes(array($Route));
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::getRoutes
         */
        public function testGetRoutes() {
            $expectedRoutes = array(
                'test.route' => $this->getMock('Brickoo\Routing\Route\Interfaces\Route')
            );
            $Collection = new RouteCollection("name", "/path", $expectedRoutes);
            $this->assertAttributeSame($expectedRoutes, 'routes', $Collection);
            $this->assertSame($expectedRoutes, $Collection->getRoutes());
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::hasRoutes
         */
        public function testHasRoutes(){
            $Collection = new RouteCollection("name", "/path");
            $this->assertFalse($Collection->hasRoutes());

            $Collection = new RouteCollection("name", "/path", array('test.route' => $this->getMock('Brickoo\Routing\Route\Interfaces\Route')));
            $this->assertTrue($Collection->hasRoutes());
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::getRoute
         */
        public function testGetRoute(){
            $expectedRoute = $this->getMock('Brickoo\Routing\Route\Interfaces\Route');
            $Collection = new RouteCollection("name", "/path", array('test.route' => $expectedRoute));
            $this->assertSame($expectedRoute, $Collection->getRoute('test.route'));
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::getRoute
         * @expectedException InvalidArgumentException
         */
        public function testGetRouteThrowsInvalidArgumentException() {
            $Collection = new RouteCollection("name", "/path");
            $Collection->getRoute(array('wrongType'));
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::getRoute
         * @covers Brickoo\Routing\Route\Exceptions\RouteNotFound
         * @expectedException Brickoo\Routing\Route\Exceptions\RouteNotFound
         */
        public function testGetRouteThrowsRouteNotFoundException() {
            $Collection = new RouteCollection("name", "/path");
            $Collection->getRoute('not.available.fail');
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::hasRoute
         */
        public function testHasRoute(){
            $Collection = new RouteCollection("name", "/path");
            $this->assertFalse($Collection->hasRoute('test.route'));
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::hasRoute
         * @expectedException InvalidArgumentException
         */
        public function testHasRouteThrowsInvalidArgumentException() {
            $Collection = new RouteCollection("name", "/path");
            $Collection->hasRoute(array('wrongType'));
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::getIterator
         */
        public function testGetIterator() {
            $Route = $this->getMock('Brickoo\Routing\Route\Interfaces\Route');
            $Collection = new RouteCollection("name", "/path", array($Route));
            $Container = $Collection->getIterator();
            $this->assertInstanceOf('Traversable', $Container);
            $Container->rewind();
            $this->assertSame($Route, $Container->current());
        }

    }