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

namespace Brickoo\Tests\Component\Routing\Route\Collector;

use Brickoo\Component\Routing\Route\RouteCollection,
    Brickoo\Component\Routing\Route\Collector\CallbackRouteCollector,
    PHPUnit_Framework_TestCase;

/**
 * CallbackRouteCollectorTest
 *
 * Test suite for the route message based collector class.
 * @see Brickoo\Component\Routing\Route\Collector\CallbackRouteCollector
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class CallbackRouteCollectorTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Routing\Route\Collector\CallbackRouteCollector::__construct
     * @covers Brickoo\Component\Routing\Route\Collector\CallbackRouteCollector::collect
     */
    public function testCollectWithCallback() {
        $routeCollection = new RouteCollection();
        $callback = function() use ($routeCollection) {return [$routeCollection];};
        $callbackCollector  =  new CallbackRouteCollector($callback);
        $this->assertInstanceOf("\\ArrayIterator", ($iterator = $callbackCollector->collect()));
        $this->assertSame($routeCollection, $iterator->current());
    }

    /** @covers Brickoo\Component\Routing\Route\Collector\CallbackRouteCollector::getIterator */
    public function testGetIterator() {
        $callbackCollector  =  new CallbackRouteCollector(function(){});
        $this->assertInstanceOf("\\ArrayIterator", $callbackCollector->getIterator());
    }

}