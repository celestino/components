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

namespace Brickoo\Component\Routing;

use Brickoo\Component\Routing\Route\Route;
use Brickoo\Component\Routing\Route\RequestRoute;
use Brickoo\Component\Routing\Exception\NoMatchingRouteFoundException;
use Brickoo\Component\Routing\Exception\RouteNotFoundException;
use Brickoo\Component\Routing\Route\RouteCollection;
use Brickoo\Component\Routing\Route\Collector\RouteCollector;
use Brickoo\Component\Routing\Route\Matcher\RouteMatcher;
use Brickoo\Component\Common\Assert;

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

    /** @var null|\Brickoo\Component\Common\Collection */
    private $routeCollections;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Routing\Route\Collector\RouteCollector $routeCollector
     * @param \Brickoo\Component\Routing\Route\Matcher\RouteMatcher $routeMatcher
     */
    public function __construct(RouteCollector $routeCollector, RouteMatcher $routeMatcher) {
        $this->routeCollector = $routeCollector;
        $this->routeMatcher = $routeMatcher;
        $this->routeCollections = null;
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
        Assert::isString($routeName);
        Assert::isString($collectionName);

        $route = null;
        foreach ($this->getRouteCollections() as $routeCollection) {
            if ($this->isCollectionResponsible($routeName, $collectionName, $routeCollection)) {
                $route = $routeCollection->getRoute($routeName);
                break;
            }
        }

        if ($route === null || (!$route instanceof Route)) {
            throw new RouteNotFoundException($routeName);
        }

        return $route;
    }

    /**
     * Checks if the route is available.
     * @param string $routeName the route unique name
     * @param string $collectionName the route collections name
     * @throws \InvalidArgumentException if an argument is not valid
     * @return boolean
     */
    public function hasRoute($routeName, $collectionName = "") {
        Assert::isString($collectionName);
        Assert::isString($routeName);

        try {
            $this->getRoute($routeName, $collectionName);
            return true;
        }
        catch (RouteNotFoundException $exception) {
            return false;
        }
    }

    /**
     * Returns the matching request route.
     * @throws \Brickoo\Component\Routing\Exception\NoMatchingRouteFoundException
     * @return \Brickoo\Component\Routing\Route\RequestRoute
     */
    public function getRequestRoute() {
        $matchingRoute = null;

        foreach ($this->getRouteCollections() as $routeCollection) {
            if (($matchingRoute = $this->getMatchingRoute($routeCollection))) {
                break;
            }
        }

        if (!$matchingRoute instanceof RequestRoute) {
            throw new NoMatchingRouteFoundException();
        }

        return $matchingRoute;
    }

    /**
     * Return the matching route.
     * @param RouteCollection $routeCollection
     * @return null|RequestRoute
     */
    private function getMatchingRoute(RouteCollection $routeCollection) {
        $matchingRoute = null;
        if ($this->routeMatcher->matchesCollection($routeCollection)) {
            $matchingRoute = $this->getMatchingRouteFromCollection($routeCollection);
        }
        return $matchingRoute;
    }

    /**
     * Returns the matching route from collection if available.
     * @param \Brickoo\Component\Routing\Route\RouteCollection $routeCollection
     * @return null|RequestRoute
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
     * @return boolean
     */
    private function isCollectionResponsible($routeName, $collectionName, RouteCollection $routeCollection) {
        return ((empty($collectionName) || $routeCollection->getName() == $collectionName)
            && $routeCollection->hasRoute($routeName));
    }

    /**
     * Returns an iterator from the route collector.
     * @return \Brickoo\Component\Common\Collection
     */
    private function getRouteCollections() {
        if ($this->routeCollections === null) {
            $this->routeCollections = $this->routeCollector->collect();
        }
        return $this->routeCollections;
    }

}
