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

namespace Brickoo\Tests\Component\Routing\Route\Collector;

use Brickoo\Component\Messaging\Message;
use Brickoo\Component\Messaging\MessageDispatcher;
use Brickoo\Component\Messaging\MessageListener;
use Brickoo\Component\Routing\Messaging\Messages;
use Brickoo\Component\Routing\Route\Collector\MessageRouteCollector;
use PHPUnit_Framework_TestCase;

/**
 * MessageRouteCollectorTest
 *
 * Test suite for the route message based collector class.
 * @see Brickoo\Component\Routing\Route\Collector\MessageRouteCollector
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageRouteCollectorTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Routing\Route\Collector\MessageRouteCollector::__construct */
    public function testConstructorImplementsInterface() {
        $messageRouteCollector = new MessageRouteCollector($this->getMessageDispatcherStub());
        $this->assertInstanceOf("\\Brickoo\\Component\\Routing\\Route\\Collector\\RouteCollector", $messageRouteCollector);
    }

    /**
     * @covers Brickoo\Component\Routing\Route\Collector\MessageRouteCollector::collect
     * @covers Brickoo\Component\Routing\Route\Collector\MessageRouteCollector::extractRouteCollections
     */
    public function testCollectRouteCollection() {
        $routeCollection = $this->getMock("\\Brickoo\\Component\\Routing\\Route\\RouteCollection");
        $messageRouteCollector = new MessageRouteCollector($this->getMessageDispatcherFixture($routeCollection));
        $this->assertInstanceOf("ArrayIterator", $messageRouteCollector->collect());
        $this->assertAttributeEquals([$routeCollection], "collections", $messageRouteCollector);
    }

    /** covers Brickoo\Component\Routing\Route\Collector\MessageRouteCollector::getIterator */
    public function testGetCollectionsIterator() {
        $messageRouteCollector = new MessageRouteCollector($this->getMessageDispatcherStub());
        $this->assertInstanceOf("ArrayIterator", $messageRouteCollector->getIterator());
    }

    /**
     * Returns a message dispatcher stub.
     * @return \Brickoo\Component\Messaging\MessageDispatcher
     */
    private function getMessageDispatcherStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Messaging\\MessageDispatcher")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a message dispatcher fixture.
     * @param $routeCollection
     * @return \Brickoo\Component\Messaging\MessageDispatcher
     */
    private function getMessageDispatcherFixture($routeCollection) {
        $listener = new MessageListener(Messages::COLLECT_ROUTES, 0, function(Message $message) use ($routeCollection) {
            $message->getResponse()->push($routeCollection);
        });

        $listenerCollection = $this->getMock("\\Brickoo\\Component\\Messaging\\ListenerCollection");
        $listenerCollection->expects($this->any())
                           ->method("hasListeners")
                           ->will($this->returnValue(true));
        $listenerCollection->expects($this->any())
                           ->method("getListeners")
                           ->will($this->returnValue([$listener]));
        return new MessageDispatcher($listenerCollection, $this->getMock("\\Brickoo\\Component\\Messaging\\MessageRecursionDepthList"));
    }

}
