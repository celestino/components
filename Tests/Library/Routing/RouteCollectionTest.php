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

    use Brickoo\Library\Routing\RouteCollection;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * RouteCollectionTest
     *
     * Test suite for the RouteCollection class.
     * @see Brickoo\Library\Routing\RouteCollection
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RouteCollectionTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the RouteCollection class.
         * @var Brickoo\Library\Routing\RouteCollection
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
         * @covers Brickoo\Library\Routing\RouteCollection::__construct
         */
        public function testConstruct()
        {
            $this->assertInstanceOf('Brickoo\Library\Routing\RouteCollection', $this->RouteCollection);
        }

        /**
         * Test if a ROute object can be added and the ROuteCOllection reference is returned.
         * @covers Brickoo\Library\Routing\RouteCollection::addRoute
         */
        public function testAddRoute()
        {
            $Route = $this->getMock('Brickoo\Library\Routing\Interfaces\RouteInterface');
            $this->assertSame($this->RouteCollection, $this->RouteCollection->addRoute($Route));
            $this->assertAttributeEquals(array($Route), 'routes', $this->RouteCollection);

            return $this->RouteCollection;
        }

        /**
         * Test if the routes can be retrieved from the collection.
         * @covers Brickoo\Library\Routing\RouteCollection::getRoutes
         * @depends testAddRoute
         */
        public function testGetRoutes($RouteCollection)
        {
            $this->assertInternalType('array', $RouteCollection->getRoutes());
        }

        /**
         * Test if the availability of routes can be recognized.
         * @covers Brickoo\Library\Routing\RouteCollection::hasRoutes
         * @depends testAddRoute
         */
        public function testHasRoutes($RouteCollection)
        {
            $this->assertFalse($this->RouteCollection->hasRoutes());
            $this->assertTrue($RouteCollection->hasRoutes());
        }

        /**
         * Test if the properties can be reset and the RouteCollection reference is returned.
         * @covers Brickoo\Library\Routing\RouteCollection::reset
         */
        public function testClear()
        {
            $this->assertSame($this->RouteCollection, $this->RouteCollection->reset());
        }

        /**
         * Test if the ArrayIterator is returned with the collected routes.
         * @covers Brickoo\Library\Routing\RouteCollection::getIterator
         * @depends testAddRoute
         */
        public function testGetIterator($RouteCollection)
        {
            $this->assertInstanceof('ArrayIterator', ($Iterator = $RouteCollection->getIterator()));
            foreach($Iterator as $Route)
            {
                $this->assertInstanceOf('Brickoo\Library\Routing\Interfaces\RouteInterface', $Route);
            }
        }

    }

?>