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

    namespace Tests\Brickoo\Routing\Collector;

    use Brickoo\Routing\Collector\EventCollector;

    /**
     * EventCollectorTest
     *
     * Test suite for the route event based collector class.
     * @see Brickoo\Routing\Collector\EventCollector
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class EventCollectorTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Routing\Collector\EventCollector::__construct
         */
        public function testConstructor() {
            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $RouteCollector = new EventCollector($EventManager);
            $this->assertInstanceOf('Brickoo\Routing\Collector\Interfaces\Collector',$RouteCollector);
            $this->assertAttributeSame($EventManager, 'EventManager', $RouteCollector);
        }

        /**
         * @covers Brickoo\Routing\Collector\EventCollector::collect
         * @covers Brickoo\Routing\Collector\EventCollector::getRouteCollection
         */
        public function testCollectRouteCollection() {
            $RouteCollection = $this->getMock('Brickoo\Routing\Route\Interfaces\Collection');
            $EventResponseCollection = new \Brickoo\Event\Response\Collection(array($RouteCollection));

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $EventManager->expects($this->once())
                         ->method('collect')
                         ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Event'))
                         ->will($this->returnValue($EventResponseCollection));

            $RouteCollector = new EventCollector($EventManager);
            $this->assertEquals($RouteCollection, $RouteCollector->collect());
        }

        /**
         * @covers Brickoo\Routing\Collector\EventCollector::collect
         * @covers Brickoo\Routing\Collector\EventCollector::getRouteCollection
         * @covers Brickoo\Routing\Collector\EventCollector::getMergedRouteCollection
         */
        public function testCollectManyRouteCollections() {
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

            $RouteCollector = new EventCollector($EventManager);
            $this->assertInstanceOf('Brickoo\Routing\Route\Interfaces\Collection', ($FoundCollection = $RouteCollector->collect()));
            $this->assertEquals(array('test.route.1' => $Route1, 'test.route.2' => $Route2), $FoundCollection->getRoutes());
        }

        /**
         * @covers Brickoo\Routing\Collector\EventCollector::collect
         * @covers Brickoo\Routing\Collector\Exceptions\RoutesNotAvailable
         * @expectedException Brickoo\Routing\Collector\Exceptions\RoutesNotAvailable
         */
        public function testCollectThrowsRoutesNotAvailable() {
            $EventResponseCollection = $this->getMock('Brickoo\Event\Response\Interfaces\Collection');
            $EventResponseCollection->expects($this->any())
                                    ->method('isEmpty')
                                    ->will($this->returnValue(true));

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $EventManager->expects($this->once())
                         ->method('collect')
                         ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Event'))
                         ->will($this->returnValue($EventResponseCollection));

            $RouteCollector = new EventCollector($EventManager);
            $RouteCollector->collect();
        }

        /**
         * @covers Brickoo\Routing\Collector\EventCollector::collect
         * @covers Brickoo\Routing\Collector\EventCollector::getRouteCollection
         * @covers Brickoo\Routing\Collector\EventCollector::getMergedRouteCollection
         * @covers Brickoo\Routing\Collector\Exceptions\RouteCollectionExpected
         * @expectedException Brickoo\Routing\Collector\Exceptions\RouteCollectionExpected
         */
        public function testCollectThrowsRouteCollectionExpectedException() {
            $unexpectedValue = "someString";
            $EventResponseCollection = new \Brickoo\Event\Response\Collection(array($unexpectedValue));

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $EventManager->expects($this->once())
                         ->method('collect')
                         ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Event'))
                         ->will($this->returnValue($EventResponseCollection));

            $RouteCollector = new EventCollector($EventManager);
            $RouteCollector->collect();
        }

        /**
         * @covers Brickoo\Routing\Collector\EventCollector::collect
         * @covers Brickoo\Routing\Collector\EventCollector::getRouteCollection
         * @covers Brickoo\Routing\Collector\EventCollector::getMergedRouteCollection
         * @covers Brickoo\Routing\Collector\Exceptions\RouteCollectionExpected
         * @expectedException Brickoo\Routing\Collector\Exceptions\RouteCollectionExpected
         */
        public function testCollectThrowsRouteCollectionExpectedExceptionWhileTryingToMerge() {
            $unexpectedValue = new \stdClass();
            $EventResponseCollection = new \Brickoo\Event\Response\Collection(array($unexpectedValue, $unexpectedValue));

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $EventManager->expects($this->once())
                         ->method('collect')
                         ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Event'))
                         ->will($this->returnValue($EventResponseCollection));

            $RouteCollector = new EventCollector($EventManager);
            $RouteCollector->collect();
        }

    }