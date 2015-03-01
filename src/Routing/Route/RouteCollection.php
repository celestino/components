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

namespace Brickoo\Component\Routing\Route;

use ArrayIterator;
use IteratorAggregate;
use Brickoo\Component\Routing\Route\Exception\DuplicateRouteException;
use Brickoo\Component\Routing\Route\Exception\RouteNotFoundException;
use Brickoo\Component\Common\Assert;

/**
 * RouteCollection
 *
 * Implements an iterable route collection.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class RouteCollection implements IteratorAggregate {

    /** @var string */
    private $name;

    /** @var string */
    private $path;

    /** @var array */
    private $routes;

    /**
     * Class constructor.
     * @param string $name the collection (unique) name
     * @param string $path the routes common path
     */
    public function __construct($name = "", $path = "") {
        Assert::isString($name);
        Assert::isString($path);

        $this->name = $name;
        $this->path = $path;
        $this->routes = [];
    }

    /**
     * Returns the route collection (unique) name.
     * @return string the collection name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Checks if the name is set.
     * @return boolean check result
     */
    public function hasName() {
        return ($this->name != "");
    }

    /**
     * Returns the routes common path.
     * @return string the routes common path
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Checks if the path is set.
     * @return boolean check result
     */
    public function hasPath() {
        return ($this->path != "");
    }

    /**
     * Returns all containing routes.
     * @return array the containing routes
     */
    public function getRoutes() {
        return $this->routes;
    }

    /**
     * Adds routes to the current collection.
     * @param array $routes values implementing \Brickoo\Component\Routing\Route\Route
     * @throws \Brickoo\Component\Routing\Route\Exception\DuplicateRouteException
     * @return \Brickoo\Component\Routing\Route\RouteCollection
     */
    public function addRoutes(array $routes) {
        foreach ($routes as $route) {
            if ($this->hasRoute(($routeName = $route->getName()))) {
                throw new DuplicateRouteException($routeName);
            }
            $this->routes[$routeName] = $route;
        }
        return $this;
    }

    /**
     * Checks if the collection contains routes.
     * @return boolean check result
     */
    public function hasRoutes() {
        return (! empty($this->routes));
    }

    /**
     * Returns the route matching the unique name.
     * @param string $routeName the route to return
     * @throws \Brickoo\Component\Routing\Route\Exception\RouteNotFoundException
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Routing\Route\Route
     */
    public function getRoute($routeName) {
        Assert::isString($routeName);

        if (! $this->hasRoute($routeName)) {
            throw new RouteNotFoundException($routeName);
        }

        return $this->routes[$routeName];
    }

    /**
     * Checks if the route is in the collection.
     * @param string $routeName the route to check
     * @throws \InvalidArgumentException if an argument is invalid
     * @return boolean check result
     */
    public function hasRoute($routeName) {
        Assert::isString($routeName);
        return isset($this->routes[$routeName]);
    }

    /**
     * {@inheritDoc}
     * @see IteratorAggregate::getIterator()
     * @return \ArrayIterator containing the collection routes
     */
    public function getIterator() {
        return new ArrayIterator($this->getRoutes());
    }

}
