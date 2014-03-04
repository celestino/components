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

use Brickoo\Component\Routing\ExecutableRoute,
    Brickoo\Component\Routing\Route,
    Brickoo\Component\Routing\RouteCollector,
    Brickoo\Component\Routing\RouteCollection,
    Brickoo\Component\Routing\Exception\NoMatchingRouteFoundException,
    Brickoo\Component\Routing\Exception\RouteNotFoundException,
    Brickoo\Component\Validation\Argument;

/**
 * Router
 *
 * Router which can return an executable matching route
 * and any route available based on its unique name.
 * For collecting the availables routes a route collector is used..
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class Router {

    /**  @var \Brickoo\Component\Routing\ExecutableRoute */
    private $executableRoute;

    /** @var \Brickoo\Component\Routing\RouteMatcher */
    private $routeMatcher;

    /** @var \Brickoo\Component\Routing\RouteCollector */
    private $routeCollector;

    /** @var \ArrayIterator */
    private $routeCollectorIterator;

    /**
    * Class constructor.
    * @param \Brickoo\Component\Routing\RouteCollector $routeCollector
    * @param \Brickoo\Component\Routing\RouteMatcher $routeMatcher
    * @return void
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
     * @return \Brickoo\Component\Routing\Route
     */
    public function getRoute($routeName, $collectionName = "") {
        Argument::IsString($routeName);
        Argument::IsString($collectionName);

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
        Argument::IsString($collectionName);
        Argument::IsString($routeName);

        try {
            $route = $this->getRoute($routeName, $collectionName);
        }
        catch (RouteNotFoundException $exception) {
            return false;
        }

        return ($route instanceof Route);
    }

    /**
     * Returns the executable route.
     * @throws \Brickoo\Component\Routing\Exception\NoMatchingRouteFoundException
     * @return \Brickoo\Component\Routing\ExecutableRoute
     */
    public function getExecutableRoute() {
        if ($this->executableRoute instanceof ExecutableRoute) {
            return $this->executableRoute;
        }

        $this->executableRoute = $this->getMatchingExecutableRoute();
        return $this->executableRoute;
    }

    /**
     * Returns the matching executable route.
     * @throws \Brickoo\Component\Routing\Exception\NoMatchingRouteFoundException
     * @return \Brickoo\Component\Routing\ExecutableRoute
     */
    private function getMatchingExecutableRoute() {
        $matchingRoute = null;

        foreach ($this->getRouteCollectorIterator() as $routeCollection) {
            if ($this->routeMatcher->matchesCollection($routeCollection)
                && ($matchingRoute = $this->getMatchingRouteFromCollection($routeCollection))) {
                break;
            }
        }

        if (! $matchingRoute instanceof ExecutableRoute) {
            throw new NoMatchingRouteFoundException();
        }

        return $matchingRoute;
    }

    /**
     * Returns the matching route from collection if available.
     * @param \Brickoo\Component\Routing\RouteCollection $routeCollection
     * @return \Brickoo\Component\Routing\Route otherwise null
     */
    private function getMatchingRouteFromCollection(RouteCollection $routeCollection) {
        $matchingRoute = null;
        foreach ($routeCollection->getRoutes() as $route) {
            if ($this->routeMatcher->matchesRoute($route)) {
                $matchingRoute = new ExecutableRoute($route, $this->routeMatcher->getRouteParameters());
                break;
            }
        }
        return $matchingRoute;
    }

    /**
     * Checks if the route collection is responsible for the requested route and collection.
     * @param string $routeName
     * @param string $collectionName
     * @param \Brickoo\Component\Routing\RouteCollection $routeCollection
     * @return boolean check result
     */
    private function isCollectionResponsible($routeName, $collectionName, RouteCollection $routeCollection) {
        return ((empty($collectionName) || $routeCollection->getName() == $collectionName)
            && $routeCollection->hasRoute($routeName));
    }

    /**
     * Returns an iterator from the route collector.
     * @return \ArrayIterator the route collection iterator
     */
    private function getRouteCollectorIterator() {
        if ($this->routeCollectorIterator === null) {
            $this->routeCollectorIterator = $this->routeCollector->collect()->getIterator();
        }
        return $this->routeCollectorIterator;
    }

}