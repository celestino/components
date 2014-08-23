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

namespace Brickoo\Component\Routing;

use Brickoo\Component\Routing\Route\Route;
use Brickoo\Component\Routing\Route\RequestRoute;
use Brickoo\Component\Routing\Exception\NoMatchingRouteFoundException;
use Brickoo\Component\Routing\Exception\RouteNotFoundException;
use Brickoo\Component\Routing\Route\RouteCollection;
use Brickoo\Component\Routing\Route\Collector\RouteCollector;
use Brickoo\Component\Routing\Route\Matcher\RouteMatcher;
use Brickoo\Component\Validation\Argument;

/**
 * Router
 *
 * Router which can return an executable matching route
 * and any route available based on its unique name.
 * For collecting the routes a collector is used.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class Router {

    /** @var \Brickoo\Component\Routing\Route\Matcher\RouteMatcher */
    private $routeMatcher;

    /** @var \Brickoo\Component\Routing\Route\Collector\RouteCollector */
    private $routeCollector;

    /** @var \Iterator */
    private $routeCollectorIterator;

    /**
    * Class constructor.
    * @param \Brickoo\Component\Routing\Route\Collector\RouteCollector $routeCollector
    * @param \Brickoo\Component\Routing\Route\Matcher\RouteMatcher $routeMatcher
    */
    public function __construct(RouteCollector $routeCollector, RouteMatcher $routeMatcher) {
        $this->routeCollector = $routeCollector;
        $this->routeMatcher = $routeMatcher;
    }

    /**
     * Returns the route having the given unique name.
     * @param string $routeName the route unique name
     * @param string $collectionName the route collections name
     * @throws \InvalidArgumentException if an argument is not valid
     * @throws \Brickoo\Component\Routing\Exception\RouteNotFoundException
     * @return \Brickoo\Component\Routing\Route\Route
     */
    public function getRoute($routeName, $collectionName = "") {
        Argument::isString($routeName);
        Argument::isString($collectionName);

        $route = null;
        foreach ($this->getRouteCollectorIterator() as $routeCollection) {
            if ($this->isCollectionResponsible($routeName, $collectionName, $routeCollection)) {
                $route = $routeCollection->getRoute($routeName);
                break;
            }
        }

        if ($route === null) {
            throw new RouteNotFoundException($routeName);
        }

        return $route;
    }

    /**
     * Checks if the route is available.
     * @param string $routeName the route unique name
     * @param string $collectionName the route collections name
     * @throws \InvalidArgumentException if an argument is not valid
     * @return boolean check result
     */
    public function hasRoute($routeName, $collectionName = "") {
        Argument::isString($collectionName);
        Argument::isString($routeName);

        try {
            $route = $this->getRoute($routeName, $collectionName);
        }
        catch (RouteNotFoundException $exception) {
            return false;
        }

        return ($route instanceof Route);
    }

    /**
     * Returns the matching request route.
     * @throws \Brickoo\Component\Routing\Exception\NoMatchingRouteFoundException
     * @return \Brickoo\Component\Routing\Route\RequestRoute
     */
    public function getRequestRoute() {
        $matchingRoute = null;

        foreach ($this->getRouteCollectorIterator() as $routeCollection) {
            if ($this->routeMatcher->matchesCollection($routeCollection)
                && ($matchingRoute = $this->getMatchingRouteFromCollection($routeCollection))) {
                break;
            }
        }

        if (! $matchingRoute instanceof RequestRoute) {
            throw new NoMatchingRouteFoundException();
        }

        return $matchingRoute;
    }

    /**
     * Returns the matching route from collection if available.
     * @param \Brickoo\Component\Routing\Route\RouteCollection $routeCollection
     * @return RequestRoute|null otherwise null
     */
    private function getMatchingRouteFromCollection(RouteCollection $routeCollection) {
        $matchingRoute = null;
        foreach ($routeCollection as $route) {
            if ($this->routeMatcher->matchesRoute($route)) {
                $matchingRoute = new RequestRoute($route, $this->routeMatcher->getRouteParameters());
                break;
            }
        }
        return $matchingRoute;
    }

    /**
     * Checks if the route collection is responsible for the requested route and collection.
     * @param string $routeName
     * @param string $collectionName
     * @param \Brickoo\Component\Routing\Route\RouteCollection $routeCollection
     * @return boolean check result
     */
    private function isCollectionResponsible($routeName, $collectionName, RouteCollection $routeCollection) {
        return ((empty($collectionName) || $routeCollection->getName() == $collectionName)
            && $routeCollection->hasRoute($routeName));
    }

    /**
     * Returns an iterator from the route collector.
     * @return \Iterator the route collection iterator
     */
    private function getRouteCollectorIterator() {
        if ($this->routeCollectorIterator === null) {
            $this->routeCollectorIterator = $this->routeCollector->collect();
        }
        return $this->routeCollectorIterator;
    }

}
