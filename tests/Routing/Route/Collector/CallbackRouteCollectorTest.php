<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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

use Brickoo\Component\Routing\Route\RouteCollection;
use Brickoo\Component\Routing\Route\Collector\CallbackRouteCollector;
use PHPUnit_Framework_TestCase;

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
