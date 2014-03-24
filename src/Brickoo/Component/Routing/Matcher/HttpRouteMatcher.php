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

namespace Brickoo\Component\Routing\Matcher;

use Brickoo\Component\Http\HttpRequest,
    Brickoo\Component\Routing\Route,
    Brickoo\Component\Routing\RouteCollection,
    Brickoo\Component\Routing\RouteMatcher,
    Brickoo\Component\Routing\RoutePathRegexGenerator,
    Brickoo\Component\Routing\Route\HttpRoute;

/**
 * HttpRouteMatcher
 *
 * Implementation of a http route matcher.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpRouteMatcher implements RouteMatcher {

    /** @var \Brickoo\Component\Http\HttpRequest */
    private $request;

    /** @var \Brickoo\Component\Routing\RoutePathRegexGenerator */
    private $regexGenerator;

    /** @var null|array */
    private $routeParameters;

    /** @var null|array */
    private $pathParameters;

    /**
     * Class constructor
     * @param \Brickoo\Component\Http\HttpRequest $request
     * @param \Brickoo\Component\Routing\RoutePathRegexGenerator $regexGenerator
     */
    public function __construct(HttpRequest $request, RoutePathRegexGenerator $regexGenerator) {
        $this->request = $request;
        $this->regexGenerator = $regexGenerator;
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

        $this->pathParameters = null;
        $this->routeParameters = null;

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
            $route->getMethod() !== null
            && preg_match("~^(". $route->getMethod() .")$~i", $this->request->getMethod()) == 1
            && (
                (($hostname = $route->getHostname()) === null)
                || preg_match("~^(". $hostname .")$~i", $this->request->getUri()->getHostname()) == 1
            )
            && (
                (($scheme = $route->getScheme()) === null)
                || preg_match("~^(". $scheme .")$~i", $this->request->getUri()->getScheme()) == 1
            )
        );
    }

    /**
     * Checks if the route does match the request uri path.
     * On matching, the request matching parameters will be extracted.
     * @param \Brickoo\Component\Routing\Route\HttpRoute $route
     * @return boolean check result
     */
    private function isMatchingRoute(HttpRoute $route) {
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
            if (isset($this->pathParameters[$ruleParameter]) && (! empty($this->pathParameters[$ruleParameter]))) {
                $routeParameters[$ruleParameter] = $this->pathParameters[$ruleParameter];
            }
            elseif ($route->hasDefaultValue($ruleParameter)) {
                $routeParameters[$ruleParameter] = $route->getDefaultValue($ruleParameter);
            }
        }
        return $routeParameters;
    }

}