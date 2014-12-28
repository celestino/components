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

namespace Brickoo\Component\Routing\Route\Matcher;

use Brickoo\Component\Routing\Route\Route;
use Brickoo\Component\Routing\Route\RouteCollection;

/**
 * RouteMatcher
 *
 * Defines a route matcher.
 * If the route does match, the route rules parameters can be retrieved.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
interface RouteMatcher {

    /**
     * Check if a route collection matches the request at all.
     * @param \Brickoo\Component\Routing\Route\RouteCollection $routeCollection
     * @return boolean check result
     */
    public function matchesCollection(RouteCollection $routeCollection);

    /**
     * Checks if a route matches the complete request.
     * @param \Brickoo\Component\Routing\Route\Route $route
     * @return boolean check result
     */
    public function matchesRoute(Route $route);

    /**
     * Returns the routing parameters and values from the last matched route.
     * @return array the parameters list as parameter/value pairs
     */
    public function getRouteParameters();

}
