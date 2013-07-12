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

    use Brickoo\Validator\Argument;

    /**
     * Router
     *
     * Router which can return the request corresponding executable route
     * and any route available in the collection.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Router implements Interfaces\Router {

        /**  @var \Brickoo\Routing\Interfaces\Executable */
        private $Executable;

        /** @var \Brickoo\Routing\Route\Interfaces\Collection */
        private $RouteCollection;

        /** @var \Brickoo\Routing\Matcher\Interfaces\Matcher */
        private $Matcher;

        /** @var \Brickoo\Routing\Collector\Interfaces\Collector */
        private $Collector;

        /**
        * Class constructor.
        * @param \Brickoo\Routing\Route\Interfaces\Collector $Collector
        * @param \Brickoo\Routing\Route\Interfaces\Matcher $Matcher
        * @return void
        */
        public function __construct(
            \Brickoo\Routing\Collector\Interfaces\Collector $Collector,
            \Brickoo\Routing\Matcher\Interfaces\Matcher $Matcher
        ){
            $this->Collector = $Collector;
            $this->Matcher = $Matcher;
            $this->RouteCollection = null;
            $this->Executable = null;
        }

        /** {@inheritDoc} */
        public function getRoute($routeName) {
            Argument::IsString($routeName);

            $RouteCollection = $this->getRouteCollection();

            if (! $RouteCollection->hasRoute($routeName)) {
                throw new Route\Exceptions\RouteNotFound($routeName);
            }

            return $RouteCollection->getRoute($routeName);
        }

        /** {@inheritDoc} */
        public function hasRoute($routeName) {
            Argument::IsString($routeName);

            $RouteCollection = $this->getRouteCollection();
            return $RouteCollection->hasRoute($routeName);
        }

        /** {@inheritDoc} */
        public function getExecutable() {
            if ($this->Executable === null) {
                $RouteCollection = $this->getRouteCollection();

                foreach($RouteCollection->getRoutes() as $Route) {
                    if ($this->Matcher->matches($Route)) {
                        $this->Executable = new Route\Executable($Route, $this->Matcher->getParameters());
                        break;
                    }
                }

                if ($this->Executable === null) {
                    throw new Exceptions\NoMatchingRouteFound();
                }
            }

            return $this->Executable;
        }

        /**
         * Returns the route collection or searches for the routes if not available.
         * @return \Brickoo\Routing\Route\Interfaces\Collection
         */
        private function getRouteCollection() {
            if ($this->RouteCollection === null) {
                $this->RouteCollection = $this->Collector->collect();
            }

            return $this->RouteCollection;
        }

    }