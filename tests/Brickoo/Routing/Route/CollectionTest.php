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

    use Brickoo\Routing\Route\Collection;

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
            $Collection = new Collection();
            $this->assertInstanceOf(
                'Brickoo\Routing\Route\Interfaces\Collection', $Collection
            );
            $this->assertAttributeEquals(array(), 'routes', $Collection);
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::addRoutes
         */
        public function testAddingRoutes() {
            $Route = $this->getMock('Brickoo\Routing\Interfaces\Route');
            $Route->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue('test.route'));

            $expectedRoutes = array('test.route' => $Route);
            $Collection = new Collection();
            $this->assertSame($Collection, $Collection->addRoutes($expectedRoutes));
            $this->assertAttributeEquals($expectedRoutes, 'routes', $Collection);
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::addRoutes
         * @covers Brickoo\Routing\Route\Exceptions\DuplicateRoute
         * @expectedException Brickoo\Routing\Route\Exceptions\DuplicateRoute
         */
        public function testAddingDuplicatedRoutesThrowsAnException() {
            $Route = $this->getMock('Brickoo\Routing\Interfaces\Route');
            $Route->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue('test.route'));

            $Collection = new Collection(array('test.route' => $Route));
            $Collection->addRoutes(array($Route));
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::getRoutes
         */
        public function testGetRoutes() {
            $expectedRoutes = array(
                'test.route' => $this->getMock('Brickoo\Routing\Interfaces\Route')
            );
            $Collection = new Collection($expectedRoutes);
            $this->assertAttributeSame($expectedRoutes, 'routes', $Collection);
            $this->assertSame($expectedRoutes, $Collection->getRoutes());
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::hasRoutes
         */
        public function testHasRoutes(){
            $Collection = new Collection();
            $this->assertFalse($Collection->hasRoutes());

            $Collection = new Collection(array('test.route' => $this->getMock('Brickoo\Routing\Interfaces\Route')));
            $this->assertTrue($Collection->hasRoutes());
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::getRoute
         */
        public function testGetRoute(){
            $expectedRoute = $this->getMock('Brickoo\Routing\Interfaces\Route');
            $Collection = new Collection(array('test.route' => $expectedRoute));
            $this->assertSame($expectedRoute, $Collection->getRoute('test.route'));
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::getRoute
         * @expectedException InvalidArgumentException
         */
        public function testGetRouteThrowsInvalidArgumentException() {
            $Collection = new Collection();
            $Collection->getRoute(array('wrongType'));
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::getRoute
         * @covers Brickoo\Routing\Route\Exceptions\RouteNotFound
         * @expectedException Brickoo\Routing\Route\Exceptions\RouteNotFound
         */
        public function testGetRouteThrowsRouteNotFoundException() {
            $Collection = new Collection();
            $Collection->getRoute('not.available.fail');
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::hasRoute
         */
        public function testHasRoute(){
            $Collection = new Collection();
            $this->assertFalse($Collection->hasRoute('test.route'));
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::hasRoute
         * @expectedException InvalidArgumentException
         */
        public function testHasRouteThrowsInvalidArgumentException() {
            $Collection = new Collection();
            $Collection->hasRoute(array('wrongType'));
        }

        /**
         * @covers Brickoo\Routing\Route\Collection::getIterator
         */
        public function testGetIterator() {
            $Route = $this->getMock('Brickoo\Routing\Interfaces\Route');
            $Collection = new Collection(array($Route));
            $Container = $Collection->getIterator();
            $this->assertInstanceOf('Traversable', $Container);
            $Container->rewind();
            $this->assertSame($Route, $Container->current());
        }

    }