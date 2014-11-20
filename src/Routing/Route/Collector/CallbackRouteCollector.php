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

namespace Brickoo\Component\Routing\Route\Collector;

use ArrayIterator;
use Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint;

/**
 * CallbackRouteCollector
 *
 * Implementation of a route collection based on callback call.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class CallbackRouteCollector implements RouteCollector {

    /** @var array */
    private $collections;

    /** @var callable */
    private $callback;

    /**
     * Class constructor.
     * @param callable $callback
     */
    public function __construct(callable $callback) {
        $this->callback  = $callback;
        $this->collections = [];
    }

    /** {@inheritDoc} */
    public function collect() {
        if (($collections = call_user_func($this->callback))
            && is_array($collections)
            && (new ContainsInstancesOfConstraint("\\Brickoo\\Component\\Routing\\Route\\RouteCollection"))->matches($collections)
        ) {
            $this->collections = $collections;
        }
        return $this->getIterator();
    }

    /**
     * {@inheritDoc}
     * @see IteratorAggregate::getIterator()
     * @return \ArrayIterator containing the route collections
     */
    public function getIterator() {
        return new ArrayIterator($this->collections);
    }

}
