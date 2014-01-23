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

namespace Brickoo\Tests\Routing\Collector;

use Brickoo\Routing\RouteCollection,
    Brickoo\Routing\Collector\MessageRouteCollector,
    PHPUnit_Framework_TestCase;

/**
 * MessageRouteCollectorTest
 *
 * Test suite for the route message based collector class.
 * @see Brickoo\Routing\Collector\MessageRouteCollector
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageRouteCollectorTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Routing\Collector\MessageRouteCollector::__construct */
    public function testConstructorImplementsInterface() {
        $messageRouteCollector = new MessageRouteCollector($this->getMessageDispatcherStub());
        $this->assertInstanceOf("Brickoo\Routing\RouteCollector",$messageRouteCollector);
    }

    /**
     * @covers Brickoo\Routing\Collector\MessageRouteCollector::collect
     * @covers Brickoo\Routing\Collector\MessageRouteCollector::extractRouteCollections
     */
    public function testCollectRouteCollection() {
        $routeCollection = new RouteCollection();

        $messageDispatcher = $this->getMessageDispatcherStub();
        $messageDispatcher->expects($this->once())
                          ->method("dispatch")
                          ->with($this->isInstanceOf("\\Brickoo\\Messaging\\Message"));

        $messageRouteCollector = new MessageRouteCollector($messageDispatcher);
        $this->assertSame($messageRouteCollector, $messageRouteCollector->collect());
        // $this->assertAttributeEquals(array($routeCollection), "collections", $messageRouteCollector);
    }

    /** covers Brickoo\Routing\Collector\MessageRouteCollector::getIterator */
    public function testGetCollectionsIterator() {
        $messageRouteCollector = new MessageRouteCollector($this->getMessageDispatcherStub());
        $this->assertInstanceOf("ArrayIterator", $messageRouteCollector->getIterator());
    }

    /**
     * Returns a message dispatcher stub.
     * @return \Brickoo\Messaging\MessageDispatcher
     */
    private function getMessageDispatcherStub() {
        return $this->getMockBuilder("\\Brickoo\\Messaging\\MessageDispatcher")
            ->disableOriginalConstructor()
            ->getMock();
    }

}