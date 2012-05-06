<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    use Brickoo\Validator\TypeValidator;

    /**
     * RouteCollection
     *
     * Implements an RouteCollection to iterate through the routes.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RouteCollection implements Interfaces\RouteCollection {

        /**
         * Holds the routes collected.
         * @var array
         */
        protected $routes;

        /**
         * Returns the collected routes.
         * @return array the collected routes
         */
        public function getRoutes() {
            return $this->routes;
        }

        /**
         * Adds a collection of routes.
         * @param array $routes the routes to add
         * @return Brickoo\Routing\Interfaces\RouteCollection
         */
        public function addRoutes(array $routes) {
            $this->routes = array_merge($this->routes, $routes);

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
         * Lazy Route initialization which is returned for configuration.
         * @param string $name the unique route name
         * @throws DuplicateRouteException if the route name is already used
         * @return Brickoo\Routing\Interfaces\Route
         */
        public function createRoute($name) {
            TypeValidator::IsStringAndNotEmpty($name);

            if ($this->hasRoute($name)) {
                throw new Exceptions\DuplicateRouteException($name);
            }

            $this->routes[$name] = ($Route = new Route($name));

            return $Route;
        }

        /**
         * Returns the route matching the unique name.
         * @param string $name the route to return
         * @throws RouteNotFoundException if the route is not available
         * @return \Bricko\Routing\Interfaces\Route
         */
        public function getRoute($name) {
            TypeValidator::IsStringAndNotEmpty($name);

            if (! $this->hasRoute($name)) {
                throw new Exceptions\RouteNotFoundException($name);
            }

            return $this->routes[$name];
        }

        /**
         * Checks if the Route exists.
         * @param string $name the route to check
         * @return boolean check result
         */
        public function hasRoute($name) {
            TypeValidator::IsStringAndNotEmpty($name);

            return isset($this->routes[$name]);
        }

        /**
        * Class constructor.
        * Initializes the class properties.
        * @return void
        */
        public function __construct() {
            $this->routes = array();
        }

    }