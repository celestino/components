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

use Brickoo\Component\Http\HttpRequest;
use Brickoo\Component\Routing\Route\HttpRoute;
use Brickoo\Component\Routing\Route\Route;
use Brickoo\Component\Routing\Route\RouteCollection;
use Brickoo\Component\Routing\Route\RoutePathRegexGenerator;
use Brickoo\Component\Common\Assert;

/**
 * BasicRouteMatcher
 *
 * Implementation of a basic route matcher.
 * Matches the route collection and route against a path.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class BasicRouteMatcher implements RouteMatcher {

    use CommonRouteMatcherRoutines;

    /** @var string */
    private $matchingPath;

    /** @var \Brickoo\Component\Routing\Route\RoutePathRegexGenerator */
    private $regexGenerator;

    /** @var array */
    private $routeParameters;

    /**
     * Class constructor
     * @param string $matchingPath
     * @param \Brickoo\Component\Routing\Route\RoutePathRegexGenerator $regexGenerator
     */
    public function __construct($matchingPath, RoutePathRegexGenerator $regexGenerator) {
        Assert::isString($matchingPath);
        $this->matchingPath = $matchingPath;
        $this->regexGenerator = $regexGenerator;
        $this->routeParameters = [];
        $this->pathParameters = [];
    }

    /** {@inheritDoc} */
    public function matchesCollection(RouteCollection $routeCollection) {
        return ((!$routeCollection->hasPath())
            || strpos($this->matchingPath, $routeCollection->getPath()) === 0);
    }

    /** {@inheritDoc} */
    public function matchesRoute(Route $route) {
        $this->routeParameters = [];

        if (($doesMatch = $this->isMatchingRoute(
            $this->matchingPath,
            $this->regexGenerator->generate($route)))) {
                $this->routeParameters = $this->collectRouteParameters($route);
        }

        return $doesMatch;
    }

    /** {@inheritDoc} */
    public function getRouteParameters() {
        return $this->routeParameters ?: [];
    }

}
