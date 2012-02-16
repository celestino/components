<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    use Brickoo\Routing\RouteCollection;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * RouteCollectionTest
     *
     * Test suite for the RouteCollection class.
     * @see Brickoo\Routing\RouteCollection
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RouteCollectionTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the RouteCollection class.
         * @var Brickoo\Routing\RouteCollection
         */
        protected $RouteCollection;

        /**
         * Setup the Logger instance used for the tests.
         * @see PHPUnit_Framework_TestCase::setUp()
         * @return void
         */
        protected function setUp()
        {
            $this->RouteCollection = new RouteCollection();
        }

        /**
         * Test if the RouteCOllection implements  the RouteCollectionInterface.
         * @covers Brickoo\Routing\RouteCollection::__construct
         */
        public function testConstruct()
        {
            $this->assertInstanceOf('Brickoo\Routing\RouteCollection', $this->RouteCollection);
        }

        /**
         * Test if a Route instance can be lazy created and the Route reference is returned.
         * @covers Brickoo\Routing\RouteCollection::getRoute
         */
        public function testGetRoute()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Routing\Interfaces\RouteInterface',
                ($Route = $this->RouteCollection->getRoute())
            );
            $this->assertAttributeEquals(array($Route), 'routes', $this->RouteCollection);

            return $this->RouteCollection;
        }

        /**
         * Test if the routes can be retrieved from the collection as an array.
         * @covers Brickoo\Routing\RouteCollection::getRoutes
         */
        public function testGetRoutes()
        {
            $this->assertInternalType('array', $this->RouteCollection->getRoutes());
        }

        public function testAddRoutes()
        {
            $routes = array
            (
                $this->getMock('Brickoo\Routing\Interfaces\RouteInterface'),
                $this->getMock('Brickoo\Routing\Interfaces\RouteInterface'),
                $this->getMock('Brickoo\Routing\Interfaces\RouteInterface')
            );
            $this->assertSame($this->RouteCollection, $this->RouteCollection->addRoutes($routes));
            $this->assertAttributeEquals($routes, 'routes', $this->RouteCollection);
        }

        /**
         * Test if the availability of routes can be recognized.
         * @covers Brickoo\Routing\RouteCollection::hasRoutes
         * @depends testGetRoute
         */
        public function testHasRoutes($RouteCollection)
        {
            $this->assertFalse($this->RouteCollection->hasRoutes());
            $this->assertTrue($RouteCollection->hasRoutes());
        }

    }