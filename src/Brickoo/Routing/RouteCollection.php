<?php

/*
 * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

namespace Brickoo\Routing;

use Brickoo\Routing\Exception\DuplicateRouteException,
    Brickoo\Routing\Exception\RouteNotFoundException,
    Brickoo\Validation\Argument;

/**
 * Collection
 *
 * Implements an iterable route collection providing available routes.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class RouteCollection {

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
     * @return void
     */
    public function __construct($name = "", $path = "") {
        Argument::IsString($name);
        Argument::IsString($path);

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
     * @param array $routes values implementing \Brickoo\Routing\Route\Interfaces\Route
     * @throws \Brickoo\Routing\Route\Exceptions\DuplicateRouteException if a route does already exist
     * @return \Brickoo\Routing\Interfaces\RouteCollection
     */
    public function addRoutes(array $routes) {
        foreach ($routes as $Route) {
            if ($this->hasRoute(($routeName = $Route->getName()))) {
                throw new DuplicateRouteException($routeName);
            }
            $this->routes[$routeName] = $Route;
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
     * @param string $name the route to return
     * @throws \Brickoo\Routing\Exception\RouteNotFoundException
     * @throws \InvalidArgumentException if an argument is invalid
     * @return \Bricko\Routing\Route\Interfaces\Route
     */
    public function getRoute($routeName) {
        Argument::IsString($routeName);

        if (! $this->hasRoute($routeName)) {
            throw new RouteNotFoundException($routeName);
        }

        return $this->routes[$routeName];
    }

    /**
     * Checks if the route is in the collection.
     * @param string $name the route to check
     * @throws \InvalidArgumentException if an argument is invalid
     * @return boolean check result
     */
    public function hasRoute($routeName) {
        Argument::IsString($routeName);
        return isset($this->routes[$routeName]);
    }

    /**
     * {@inheritDoc}
     * @see IteratorAggregate::getIterator()
     * @return \ArrayIterator containing the collection routes
     */
    public function getIterator() {
        return new \ArrayIterator($this->getRoutes());
    }

}