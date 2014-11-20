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

namespace Brickoo\Component\Routing\Route\Matcher;

use Brickoo\Component\Http\HttpRequest;
use Brickoo\Component\Routing\Route\HttpRoute;
use Brickoo\Component\Routing\Route\Route;
use Brickoo\Component\Routing\Route\RouteCollection;
use Brickoo\Component\Routing\Route\RoutePathRegexGenerator;

/**
 * HttpRouteMatcher
 *
 * Implementation of a http route matcher.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpRouteMatcher implements RouteMatcher {

    /** @var \Brickoo\Component\Http\HttpRequest */
    private $request;

    /** @var \Brickoo\Component\Routing\Route\RoutePathRegexGenerator */
    private $regexGenerator;

    /** @var array */
    private $routeParameters;

    /** @var array */
    private $pathParameters;

    /**
     * Class constructor
     * @param \Brickoo\Component\Http\HttpRequest $request
     * @param \Brickoo\Component\Routing\Route\RoutePathRegexGenerator $regexGenerator
     */
    public function __construct(HttpRequest $request, RoutePathRegexGenerator $regexGenerator) {
        $this->request = $request;
        $this->regexGenerator = $regexGenerator;
        $this->routeParameters = [];
        $this->pathParameters = [];
    }

    /** {@inheritDoc} */
    public function matchesCollection(RouteCollection $routeCollection) {
        return ((! $routeCollection->hasPath()) || strpos($this->request->getUri()->getPath(), $routeCollection->getPath()) === 0);
    }

    /** {@inheritDoc} */
    public function matchesRoute(Route $route) {
        if ((! $route instanceof HttpRoute) || (! $this->isAllowedRoute($route))) {
            return false;
        }

        $this->routeParameters = [];

        if (($doesMatch = $this->isMatchingRoute($route))) {
            $this->routeParameters = $this->collectRouteParameters($route);
        }

        return $doesMatch;
    }

    /** {@inheritDoc} */
    public function getRouteParameters() {
        return $this->routeParameters ?: [];
    }

    /**
     * Checks if the Route is allowed to be executed.
     * @param \Brickoo\Component\Routing\Route\HttpRoute $route
     * @return boolean check result
     */
    private function isAllowedRoute(HttpRoute $route) {
        return (
            $this->doesPropertyMatch($route->getMethod(), $this->request->getMethod()->toString())
            && $this->doesPropertyMatch($route->getHostname(), $this->request->getUri()->getHostname())
            && $this->doesPropertyMatch($route->getScheme(), $this->request->getUri()->getScheme())
        );
    }

    /**
     * Check if the route property matches the request.
     * @param string $routeProperty
     * @param string $requestProperty
     * @return boolean check result
     */
    private function doesPropertyMatch($routeProperty, $requestProperty) {
        return $routeProperty === null
            || (is_string($routeProperty) && preg_match("~^(".$routeProperty.")$~i", $requestProperty) == 1);
    }

    /**
     * Checks if the route does match the request uri path.
     * On matching, the request matching parameters will be extracted.
     * @param \Brickoo\Component\Routing\Route\HttpRoute $route
     * @return boolean check result
     */
    private function isMatchingRoute(HttpRoute $route) {
        $this->pathParameters = array();

        return (preg_match($this->regexGenerator->generate($route),
            $this->request->getUri()->getPath(),
            $this->pathParameters
        ) == 1);
    }

    /**
     * Returns the route matched parameters for the current request.
     * @param \Brickoo\Component\Routing\Route\HttpRoute $route
     * @return array the path parameters of the matching route
     */
    private function collectRouteParameters(HttpRoute $route) {
        if (! $route->hasRules()) {
            return [];
        }

        $routeParameters = [];
        foreach(array_keys($route->getRules()) as $ruleParameter) {
            $routeParameters[$ruleParameter] = $this->getRuleCorrespondingRouteParameter($ruleParameter, $route);
        }
        return $routeParameters;
    }

    /**
     * Return the rule corresponding route parameter.
     * @param string $ruleParameter
     * @param HttpRoute $route
     * @return mixed the rule corresponding route parameter
     */
    private function getRuleCorrespondingRouteParameter($ruleParameter, HttpRoute $route) {
        if (isset($this->pathParameters[$ruleParameter]) && (! empty($this->pathParameters[$ruleParameter]))) {
            return $this->pathParameters[$ruleParameter];
        }
        return $route->getDefaultValue($ruleParameter);
    }

}
