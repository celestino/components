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

    namespace Brickoo\Routing\Route;

    use Brickoo\Validator\Argument;

    /**
     * Collection
     *
     * Implements a route collection containing route objects.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Collection implements Interfaces\Collection {

        /** @var array */
        private $routes;

        /**
         * Class constructor.
         * @param array $routes the routes to add to the collection
         * @return void
         */
        public function __construct(array $routes = array()) {
            $this->routes = $routes;
        }

        /** {@inheritDoc} */
        public function getRoutes() {
            return $this->routes;
        }

        /** {@inheritDoc} */
        public function addRoutes(array $routes) {
            foreach ($routes as $Route) {
                if ($this->hasRoute(($routeName = $Route->getName()))) {
                    throw new Exceptions\DuplicateRoute($routeName);
                }
                $this->routes[$routeName] = $Route;
            }
            return $this;
        }

        /** {@inheritDoc} */
        public function hasRoutes() {
            return (! empty($this->routes));
        }

        /** {@inheritDoc} */
        public function getRoute($routeName) {
            Argument::IsString($routeName);

            if (! $this->hasRoute($routeName)) {
                throw new Exceptions\RouteNotFound($routeName);
            }

            return $this->routes[$routeName];
        }

        /** {@inheritDoc} */
        public function hasRoute($routeName) {
            Argument::IsString($routeName);
            return isset($this->routes[$routeName]);
        }

        /**
         * {@inheritDoc}
         * @see IteratorAggregate::getIterator()
         * @return \Brickoo\Memory\Interfaces\Container
         */
        public function getIterator() {
            return new \ArrayIterator($this->getRoutes());
        }

    }