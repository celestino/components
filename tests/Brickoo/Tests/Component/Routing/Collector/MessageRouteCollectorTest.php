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

namespace Brickoo\Tests\Component\Routing\Collector;

use Brickoo\Component\Messaging\Message,
    Brickoo\Component\Messaging\MessageDispatcher,
    Brickoo\Component\Messaging\MessageListener,
    Brickoo\Component\Routing\Messages,
    Brickoo\Component\Routing\Collector\MessageRouteCollector,
    PHPUnit_Framework_TestCase;

/**
 * MessageRouteCollectorTest
 *
 * Test suite for the route message based collector class.
 * @see Brickoo\Component\Routing\Collector\MessageRouteCollector
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageRouteCollectorTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Routing\Collector\MessageRouteCollector::__construct */
    public function testConstructorImplementsInterface() {
        $messageRouteCollector = new MessageRouteCollector($this->getMessageDispatcherStub());
        $this->assertInstanceOf("Brickoo\Component\Routing\RouteCollector",$messageRouteCollector);
    }

    /**
     * @covers Brickoo\Component\Routing\Collector\MessageRouteCollector::collect
     * @covers Brickoo\Component\Routing\Collector\MessageRouteCollector::extractRouteCollections
     */
    public function testCollectRouteCollection() {
        $routeCollection = $this->getMock("\\Brickoo\\Component\\Routing\\RouteCollection");
        $messageRouteCollector = new MessageRouteCollector($this->getMessageDispatcherFixture($routeCollection));
        $this->assertSame($messageRouteCollector, $messageRouteCollector->collect());
        $this->assertAttributeEquals([$routeCollection], "collections", $messageRouteCollector);
    }

    /** covers Brickoo\Component\Routing\Collector\MessageRouteCollector::getIterator */
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

        $listenerCollection = $this->getMock("\\Brickoo\\Component\\Messaging\ListenerCollection");
        $listenerCollection->expects($this->any())
                           ->method("hasListeners")
                           ->will($this->returnValue(true));
        $listenerCollection->expects($this->any())
                           ->method("getListeners")
                           ->will($this->returnValue([$listener]));
        return new MessageDispatcher($listenerCollection, $this->getMock("\\Brickoo\\Component\\Messaging\\MessageRecursionDepthList"));
    }

}