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

    namespace Tests\Brickoo\Routing\Search;

    use Brickoo\Routing\Search\EventSearch;

    /**
     * EventSearchTest
     *
     * Test suite for the route event based search class.
     * @see Brickoo\Routing\Search\EventSearch
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class EventSearchTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Routing\Search\EventSearch::__construct
         */
        public function testConstructor() {
            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $RouteSearch = new EventSearch($EventManager);
            $this->assertInstanceOf('Brickoo\Routing\Search\Interfaces\Search',$RouteSearch);
            $this->assertAttributeSame($EventManager, 'EventManager', $RouteSearch);
        }

        /**
         * @covers Brickoo\Routing\Search\EventSearch::find
         * @covers Brickoo\Routing\Search\EventSearch::getRouteCollection
         */
        public function testFindRouteCollection() {
            $RouteCollection = $this->getMock('Brickoo\Routing\Route\Interfaces\Collection');
            $EventResponseCollection = new \Brickoo\Event\Response\Collection(array($RouteCollection));

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $EventManager->expects($this->once())
                         ->method('collect')
                         ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Event'))
                         ->will($this->returnValue($EventResponseCollection));

            $RouteSearch = new EventSearch($EventManager);
            $this->assertEquals($RouteCollection, $RouteSearch->find());
        }

        /**
         * @covers Brickoo\Routing\Search\EventSearch::find
         * @covers Brickoo\Routing\Search\EventSearch::getRouteCollection
         * @covers Brickoo\Routing\Search\EventSearch::getMergedRouteCollection
         */
        public function testFindManyRouteCollections() {
            $Route1 = $this->getMock('Brickoo\Routing\Interfaces\Route');
            $Route1->expects($this->any())
                   ->method('getName')
                   ->will($this->returnValue('test.route.1'));

            $Route2 = $this->getMock('Brickoo\Routing\Interfaces\Route');
            $Route2->expects($this->any())
                   ->method('getName')
                   ->will($this->returnValue('test.route.2'));

            $EventResponseCollection = new \Brickoo\Event\Response\Collection(array(
                new \Brickoo\Routing\Route\Collection(array($Route1)),
                new \Brickoo\Routing\Route\Collection(array($Route2))
            ));

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $EventManager->expects($this->once())
                         ->method('collect')
                         ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Event'))
                         ->will($this->returnValue($EventResponseCollection));

            $RouteSearch = new EventSearch($EventManager);
            $this->assertInstanceOf('Brickoo\Routing\Route\Interfaces\Collection', ($FoundCollection = $RouteSearch->find()));
            $this->assertEquals(array('test.route.1' => $Route1, 'test.route.2' => $Route2), $FoundCollection->getRoutes());
        }

        /**
         * @covers Brickoo\Routing\Search\EventSearch::find
         * @covers Brickoo\Routing\Search\Exceptions\RoutesNotAvailable
         * @expectedException Brickoo\Routing\Search\Exceptions\RoutesNotAvailable
         */
        public function testFindThrowsRoutesNotAvailable() {
            $EventResponseCollection = $this->getMock('Brickoo\Event\Response\Interfaces\Collection');
            $EventResponseCollection->expects($this->any())
                                    ->method('isEmpty')
                                    ->will($this->returnValue(true));

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $EventManager->expects($this->once())
                         ->method('collect')
                         ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Event'))
                         ->will($this->returnValue($EventResponseCollection));

            $RouteSearch = new EventSearch($EventManager);
            $RouteSearch->find();
        }

        /**
         * @covers Brickoo\Routing\Search\EventSearch::find
         * @covers Brickoo\Routing\Search\EventSearch::getRouteCollection
         * @covers Brickoo\Routing\Search\EventSearch::getMergedRouteCollection
         * @covers Brickoo\Routing\Search\Exceptions\RouteCollectionExpected
         * @expectedException Brickoo\Routing\Search\Exceptions\RouteCollectionExpected
         */
        public function testFindThrowsRouteCollectionExpectedException() {
            $unexpectedValue = "someString";
            $EventResponseCollection = new \Brickoo\Event\Response\Collection(array($unexpectedValue));

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $EventManager->expects($this->once())
                         ->method('collect')
                         ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Event'))
                         ->will($this->returnValue($EventResponseCollection));

            $RouteSearch = new EventSearch($EventManager);
            $RouteSearch->find();
        }

        /**
         * @covers Brickoo\Routing\Search\EventSearch::find
         * @covers Brickoo\Routing\Search\EventSearch::getRouteCollection
         * @covers Brickoo\Routing\Search\EventSearch::getMergedRouteCollection
         * @covers Brickoo\Routing\Search\Exceptions\RouteCollectionExpected
         * @expectedException Brickoo\Routing\Search\Exceptions\RouteCollectionExpected
         */
        public function testFindThrowsRouteCollectionExpectedExceptionWhileTryingToMerge() {
            $unexpectedValue = new \stdClass();
            $EventResponseCollection = new \Brickoo\Event\Response\Collection(array($unexpectedValue, $unexpectedValue));

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $EventManager->expects($this->once())
                         ->method('collect')
                         ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Event'))
                         ->will($this->returnValue($EventResponseCollection));

            $RouteSearch = new EventSearch($EventManager);
            $RouteSearch->find();
        }

    }