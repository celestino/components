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

    namespace Brickoo\Routing\Route\Interfaces;

    /**
     * Collection
     *
     * Defines an iterable route collection providing available routes.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface Collection extends \IteratorAggregate {

        /**
        * Returns all containing routes.
        * @return array the containing routes
        */
        public function getRoutes();

        /**
         * Adds routes to the current collection.
         * @param array $routes the routes implementing \Brickoo\Routing\Interfaces\Route to add
         * @throws \Brickoo\Routing\Route\Exceptions\DuplicateRouteException if a route does already exist
         * @return \Brickoo\Routing\Interfaces\RouteCollection
         */
        public function addRoutes(array $routes);

        /**
         * Checks if the collection contains routes.
         * @return boolean check result
         */
        public function hasRoutes();

        /**
         * Returns the route matching the unique name.
         * @param string $name the route to return
         * @throws \InvalidArgumentException if an argument is invalid
         * @throws \Brickoo\Routing\Route\Exceptions\RouteNotFound if the route is not available
         * @return \Bricko\Routing\Interfaces\Route
         */
        public function getRoute($routeName);

        /**
         * Checks if the route is in the collection.
         * @param string $name the route to check
         * @throws \InvalidArgumentException if an argument is invalid
         * @return boolean check result
         */
        public function hasRoute($routeName);

    }